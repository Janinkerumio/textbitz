# Remote Authentication

This app (the "client") syncs SMS blast data to a separate Textbitz API server. Users
authenticate to that server with a Sanctum personal access token, stored per-user in
`users.remote_token`. This doc covers how that connection works on the client, and how
to build the counterpart server.

## How it works on the client

Connection is automatic — there is no manual "connect" step in Settings.

- **On register** (`RegisteredUserController@store`): the new user is created locally,
  then `ConnectRemoteAccountJob` is dispatched with `isNewRegistration: true`, which
  calls `RemoteAuthService::register()` to create the account on the remote server too.
- **On login** (`AuthenticatedSessionController@store`): if the user has no
  `remote_token` yet, `ConnectRemoteAccountJob` is dispatched to call
  `RemoteAuthService::login()`.
- **Identifier**: the remote server keys accounts by **phone number**, not email.
  `users.phone_number` (unique, collected at sign-up) is what's sent to `/api/register`
  and `/api/login` — not `email`.
- **Offline handling**: `ConnectRemoteAccountJob` checks
  `ServerConnectivityService::isOnline()` before attempting the call. If the server is
  unreachable, it releases itself back to the queue (30s) instead of failing, and keeps
  retrying with backoff for up to 3 days (`retryUntil()`). The plaintext password is
  never persisted — it only exists in the job payload, which is encrypted at rest via
  `ShouldBeEncrypted`.
- **Token invalidation**: `PushBlastToServerJob` and `PushBlastRecipientsJob` clear
  `remote_token` whenever the server responds `401`, so a future login will re-establish
  the connection.
- **Background sync sweep**: `DataSyncToJob::retryPendingSyncs()` is scheduled every 5
  minutes (`routes/console.php`), gated on connectivity, to retry any blasts/recipients
  that failed to push earlier.

### Key files

| File | Purpose |
|---|---|
| `app/Services/RemoteApiClient.php` | Base HTTP client — sends the bearer token, classifies responses into `success` / `retry` / `failed` / `unauthorized` |
| `app/Services/RemoteAuthService.php` | `register()` / `login()` / `verify()` / `logout()` against the remote server |
| `app/Services/ServerConnectivityService.php` | Cached health check (`/api/health`) used to gate jobs |
| `app/Services/DataSyncToJob.php` | Sweeps local pending/failed blasts & recipients and re-dispatches push jobs |
| `app/Jobs/ConnectRemoteAccountJob.php` | Dispatched on register/login to obtain the remote token |
| `app/Jobs/PushBlastToServerJob.php` | Pushes a blast to `/api/blasts` |
| `app/Jobs/PushBlastRecipientsJob.php` | Pushes recipient chunks to `/api/blasts/{id}/recipients` |
| `config/services.php` (`textbitz.server_url`) | Base URL of the remote server, from `TEXTBITZ_SERVER_URL` |

### Config

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

Polled every 15s (cached) by the client to decide whether to hold jobs:

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
  -d '{"name":"Test","phone_number":"09171234567","password":"password","device_name":"curl"}'

curl http://127.0.0.1:8001/api/health
```

Then point this client's `TEXTBITZ_SERVER_URL` at it and register/log in through the UI —
watch `storage/logs/laravel.log` on both sides.
