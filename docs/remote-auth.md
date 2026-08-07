# Remote Authentication & Data Syncing

This app (the "client") syncs SMS blast data to a separate Textbitz API server. Users
authenticate to that server with a Sanctum personal access token, stored per-user in
`users.remote_token`. This doc covers how that connection and its resync-on-reconnect
behavior work on the client, and how to build the counterpart server.

## How it works on the client

Connection is automatic — there is no manual "connect" step in Settings. Registration
and login take **different** paths to get a remote token, described below.

### Identifier: phone number, not email

The remote server keys accounts by **phone number**. `users.phone_number` (unique,
collected at sign-up) is what's sent to `/api/register` and `/api/login` — never
`email`. It's validated on both ends:

- **Backend**: `App\Rules\PhilippineMobileNumber` enforces the canonical E.164 shape
  `+639XXXXXXXXX` (regex `^\+639\d{9}$`) on the registration request.
- **Frontend**: `SignUp.vue` uses the existing `usePHPhoneFormatter` composable —
  `formatPhonePartial()` live-formats as the user types, and `normalizePhone()` /
  `stripSpaces()` collapse the display format down to the canonical shape right before
  the form posts, so what the backend rule receives always matches what it expects.

### On register — job-based

`RegisteredUserController@store` creates the local user, then dispatches
`ConnectRemoteAccountJob(user, password, isNewRegistration: true)`, which calls
`RemoteAuthService::register()`. This job:

- Checks `ServerConnectivityService::isOnline()` first; if offline, it releases itself
  back to the queue every 30s rather than failing outright.
- Otherwise retries failed attempts with backoff (`[10, 30, 60, 120, 300]`s) for up to
  3 days (`retryUntil()`).
- Is `ShouldBeEncrypted` — the plaintext password only ever exists inside this
  encrypted, transient job payload, never persisted to a column.

### On login — direct call + deferred retry (no job)

`AuthenticatedSessionController@store` calls `RemoteAuthService::authenticateOrDefer()`
synchronously in the request, but only if the user has no `remote_token` yet:

- If the server is reachable, it tries `login()` immediately, inline.
- If it's not reachable (or that immediate attempt fails), the password is encrypted
  and cached per-user (`remote_auth_pending:{id}`, 6h TTL) via `deferRemoteAuth()`
  instead of holding a queued job open for it.

This is intentionally asymmetric with registration: register still uses
`ConnectRemoteAccountJob`'s job-based hold-and-retry, while login defers through the
cache and relies on the reconnect listener (below) to flush it. Worth knowing if you're
debugging why a fresh signup's remote link behaves differently from a returning user's.

### Reconnect detection: event + listener, not job polling

Instead of every job independently polling for connectivity, there's a single
centralized detector:

1. `routes/console.php` schedules `ServerConnectivityService::checkAndNotify()` every
   minute. It does a fresh ping, compares against the last known state (cached
   indefinitely, separate from the normal 15s `isOnline()` cache), and fires
   **`App\Events\ServerConnectionRestored`** only on an unreachable→reachable edge.
2. **`App\Listeners\SyncPendingClientData`** (auto-discovered, `ShouldQueue` — runs on
   the queue, not inline during the scheduler tick) handles that event by running, in
   order:
   - `RemoteAuthService::flushPendingAuth()` — walks all users, decrypts any
     cached deferred password (from the login path above), retries `login()` once,
     and clears the cache key regardless of outcome (it does not keep retrying beyond
     that single attempt, even though the cache entry has a 6h TTL).
   - `RemoteAuthService::verifyAllTokens()` — re-validates every existing
     `remote_token` via `/api/user`, clearing any that come back `401`.
   - `DataSyncToJob::retryPendingSyncs()` — resyncs blasts/recipients (see below).

There is currently no periodic fallback sweep — `DataSyncToJob::retryPendingSyncs()`
only runs from this listener now. If the queue worker is down at the exact moment
`ServerConnectionRestored` fires, that sync round is missed until the *next*
offline→online transition.

### Token invalidation

`PushBlastToServerJob` and `PushBlastRecipientsJob` clear `remote_token` whenever the
server responds `401`, so the next login (or the listener's `verifyAllTokens()` pass)
re-establishes the connection.

### Blast/recipient resync — chained, not independent

`DataSyncToJob::retryRecipients()` used to skip a blast's pending recipients outright
if that blast hadn't synced yet (no `remote_id`), leaving them stuck until a separate
sweep happened to catch the now-synced blast later. It now mirrors
`SMSBlastService::postSendBlast()`'s original-send pattern:

- If a blast still needs syncing (`sync_status` is `pending`, no `remote_id`), its
  pending recipient chunks are appended to that same blast's push via
  `Bus::chain([PushBlastToServerJob, ...recipientJobs])->catch(...)` — recipients fire
  automatically right after the blast push succeeds, in the same chain, with the same
  failure handling (marks the blast `failed` if any link throws).
- `retryBlasts()` excludes any blast that's about to be pushed via that chain, so it's
  never double-dispatched (once standalone, once as the chain's first link).
- A blast in a terminal `failed` state is still not auto-retried (matching
  `retryBlasts()`'s own `allowFailed: false` default) — its recipients stay pending
  until the blast itself is resolved another way.

## Key files

| File | Purpose |
|---|---|
| `app/Services/RemoteApiClient.php` | Base HTTP client — sends the bearer token, classifies responses into `success` / `retry` / `failed` / `unauthorized` |
| `app/Services/RemoteAuthService.php` | `register()` / `login()` / `verify()` / `logout()` / `authenticateOrDefer()` / `flushPendingAuth()` / `verifyAllTokens()` |
| `app/Services/ServerConnectivityService.php` | Health check (`/api/health`); `isOnline()` (15s cache) for gating jobs, `checkAndNotify()` for the reconnect heartbeat |
| `app/Services/DataSyncToJob.php` | Resyncs local pending/failed blasts & recipients, chaining blast+recipient pushes where needed |
| `app/Events/ServerConnectionRestored.php` | Fired once on the unreachable→reachable transition |
| `app/Listeners/SyncPendingClientData.php` | Queued listener — orchestrates the reconnect sync tasks above |
| `app/Jobs/ConnectRemoteAccountJob.php` | Dispatched on **register only**, to obtain the remote token |
| `app/Jobs/PushBlastToServerJob.php` | Pushes a blast to `/api/blasts` |
| `app/Jobs/PushBlastRecipientsJob.php` | Pushes recipient chunks to `/api/blasts/{id}/recipients` |
| `app/Rules/PhilippineMobileNumber.php` | Validates `phone_number` as `+639XXXXXXXXX` |
| `resources/js/Composables/usePHPhoneFormatter.js` | Frontend phone formatting/normalization, reused in `SignUp.vue` |
| `config/services.php` (`textbitz.server_url`) | Base URL of the remote server, from `TEXTBITZ_SERVER_URL` |

## Config

Set the remote server's base URL in `.env`:

```
TEXTBITZ_SERVER_URL=http://127.0.0.1:8001
```

## Building the server (Laravel + Sanctum)

The client expects a specific contract. This is token auth (Bearer), not SPA cookie
auth — no `SANCTUM_STATEFUL_DOMAINS` or stateful middleware needed, just `auth:sanctum`
on protected routes.

### 1. Scaffold

```bash
composer create-project laravel/laravel textbitz-server
cd textbitz-server
composer require laravel/sanctum
php artisan install:api
```

### 2. Users table — key by phone number

```bash
php artisan make:migration add_phone_number_to_users_table
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone_number')->unique()->after('email');
    $table->string('email')->nullable()->change();
});
```

Add `phone_number` to `User::$fillable` and the `HasApiTokens` trait:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}
```

### 3. Auth endpoints

The client sends/expects these exact shapes:

- `POST /api/register` — in: `name, phone_number, password, device_name` → out: `{ user, token }`
- `POST /api/login` — in: `phone_number, password, device_name` → out: `{ user, token }`
- `GET /api/user` (auth) → current user
- `POST /api/logout` (auth) → revoke current token

```php
// app/Http/Controllers/Api/AuthController.php
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone_number' => 'required|string|unique:users,phone_number',
        'password' => 'required|string|min:8',
        'device_name' => 'required|string',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'phone_number' => $validated['phone_number'],
        'password' => Hash::make($validated['password']),
    ]);

    return response()->json([
        'user' => $user,
        'token' => $user->createToken($validated['device_name'])->plainTextToken,
    ], 201);
}

public function login(Request $request)
{
    $validated = $request->validate([
        'phone_number' => 'required|string',
        'password' => 'required|string',
        'device_name' => 'required|string',
    ]);

    $user = User::where('phone_number', $validated['phone_number'])->first();

    if (!$user || !Hash::check($validated['password'], $user->password)) {
        throw ValidationException::withMessages([
            'phone_number' => ['Invalid credentials.'],
        ]);
    }

    $user->tokens()->where('name', $validated['device_name'])->delete();

    return response()->json([
        'user' => $user,
        'token' => $user->createToken($validated['device_name'])->plainTextToken,
    ]);
}

public function user(Request $request)
{
    return $request->user();
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->noContent();
}
```

### 4. Health check (public)

Polled by the client (`ServerConnectivityService`, 25s timeout, 15s result cache) to
decide whether to hold jobs and to drive the reconnect heartbeat:

```php
Route::get('/health', fn () => response()->json(['status' => 'ok']));
```

### 5. Business endpoints

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/blasts', [BlastController::class, 'store']);
    Route::post('/blasts/{blast}/recipients', [BlastController::class, 'addRecipients']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/health', fn () => response()->json(['status' => 'ok']));
```

`BlastController::store` must return `{ "blast_id": <id> }` — `PushBlastToServerJob`
reads that exact key from the response.

### 6. 401s must actually be 401s

The client treats any `401` from a token-authenticated call as "token invalid" and wipes
its local copy. Don't apply `web` middleware to these routes — `auth:sanctum` on its own
returns a plain JSON 401, which is what the client relies on.

### 7. Test it

```bash
php artisan serve --port=8001

curl -X POST http://127.0.0.1:8001/api/register \
  -H 'Content-Type: application/json' \
  -d '{"name":"Test","phone_number":"+639171234567","password":"password","device_name":"curl"}'

curl http://127.0.0.1:8001/api/health
```

Then point this client's `TEXTBITZ_SERVER_URL` at it and register/log in through the UI —
watch `storage/logs/laravel.log` on both sides.
