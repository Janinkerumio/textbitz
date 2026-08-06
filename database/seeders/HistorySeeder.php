<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Template;
use App\Models\History;
use App\Models\Contact;
use App\Models\Recipients;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class HistorySeeder extends Seeder
{
    protected array $firstNames = ['Alice', 'Bob', 'Charlie', 'Diana', 'Edward', 'Fiona', 'George'];
 
    protected array $companyNames = [
        'Acme Corp', 'Globex Trading', 'Initech Solutions', 'Umbrella Retail',
        'Stark Supplies', 'Wayne Goods', 'Wonka Industries', 'Hooli Services',
    ];
 
    protected array $productNames = [
        'Wireless Mouse', 'Coffee Tumbler', 'Desk Lamp', 'Phone Case',
        'Bluetooth Speaker', 'Notebook Set', 'Water Bottle', 'Backpack',
    ];
 
    protected array $statusPool = ['sent', 'queued', 'sent', 'failed']; // 'sent' weighted higher
 
    public function run(): void
    {
        $userId = User::query()->value('id');
 
        if (! $userId) {
            $this->call(UserSeeder::class);
            $userId = User::query()->value('id');
        }
 
        $templates = Template::query()->get(['id', 'message']);
 
        if ($templates->isEmpty()) {
            $this->command?->warn('No templates found. Run TemplateSeeder first.');
            Log::warning('HistorySeeder aborted: no templates found.');
            return;
        }

        $contacts = Contact::query()->where('user_id', $userId)->get();

        if ($contacts->isEmpty()) {
            $this->call(ContactSeeder::class);
            $contacts = Contact::query()->where('user_id', $userId)->get();
        }

        $sampleValues = [
            'name' => fn () => $this->randomFirstName(),
            'business_name' => fn () => $this->randomFrom($this->companyNames),
            'order_id' => fn () => (string) random_int(1000, 9999),
            'amount' => fn () => '$' . random_int(10, 500),
            'due_date' => fn () => $this->randomDate('-1 month', '+1 month'),
            'discount' => fn () => (string) random_int(10, 50),
            'product_name' => fn () => $this->randomFrom($this->productNames),
            'review_link' => fn () => $this->randomUrl(),
            'status' => fn () => $this->randomStatus(),
        ];
 
        $successCount = 0;
        $failCount = 0;
 
        foreach (range(1, 30) as $i) {
            try {
                $template = $templates->random();
 
                $blast = preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($sampleValues) {
                    $key = $matches[1];
                    return isset($sampleValues[$key]) ? $sampleValues[$key]() : $matches[0];
                }, $template->message);
 
                $recipientContacts = $this->pickRecipientContacts($contacts)->values();
                $recipientStatuses = $this->generateRecipientStatuses($recipientContacts->count());

                $history = History::create([
                    'user_id' => $userId,
                    'template_id' => $template->id,
                    'title' => null,//add title,
                    'blast' => $blast,
                    'status' => $this->resolveHistoryStatus($recipientStatuses),
                    'recipients' => $recipientContacts->count(),
                    'last_sent_at' => $this->randomDateTimeBetween('-2 months', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sentCount = 0;
                $failedCount = 0;

                foreach ($recipientContacts as $index => $contact) {
                    $status = $recipientStatuses[$index];

                    if ($status === Recipients::STATUS_SENT) {
                        $sentCount++;
                    } elseif ($status === Recipients::STATUS_FAILED) {
                        $failedCount++;
                    }

                    Recipients::create([
                        'history_id' => $history->id,
                        'contact_id' => $contact->id,
                        'name' => $contact->contact_name,
                        'mobile_num' => $contact->phone_num,
                        'status' => $status,
                        'error_message' => $status === Recipients::STATUS_FAILED
                            ? 'Simulated delivery failure'
                            : null,
                        'sent_at' => $status === Recipients::STATUS_SENT
                            ? $this->randomDateTimeBetween('-2 months', 'now')
                            : null,
                    ]);
                }

                $history->update([
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                ]);

                $successCount++;
            } catch (\Throwable $e) {
                $failCount++;
 
                Log::channel('seeder')->error('HistorySeeder: failed to create record', [
                    'iteration' => $i,
                    'template_id' => $template->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
 
                $this->command?->error("Failed on iteration {$i}: {$e->getMessage()}");
 
                continue;
            }
        }
 
        $this->command?->info("HistorySeeder finished: {$successCount} created, {$failCount} failed.");
 
        if ($failCount > 0) {
            Log::channel('seeder')->warning('HistorySeeder completed with errors.', [
                'success' => $successCount,
                'failed' => $failCount,
            ]);
        }
    }
 
    protected function randomFirstName(): string
    {
        return $this->randomFrom($this->firstNames);
    }
 
    protected function randomFrom(array $items): string
    {
        return $items[array_rand($items)];
    }
 
    protected function randomStatus(): string
    {
        return $this->randomFrom($this->statusPool);
    }
 
    protected function randomUrl(): string
    {
        $domains = ['example.com', 'reviewsite.io', 'feedback.co', 'ratehub.net'];
        $slug = strtolower(str_replace(' ', '-', $this->randomFrom($this->productNames))) . '-' . random_int(100, 999);
 
        return 'https://' . $this->randomFrom($domains) . '/' . $slug;
    }
 
    /**
     * Pick a large batch of contacts (10 up to all available) to act as
     * recipients for a history record.
     */
    protected function pickRecipientContacts(Collection $contacts): Collection
    {
        if ($contacts->isEmpty()) {
            return collect();
        }

        $min = min(10, $contacts->count());
        $count = random_int($min, $contacts->count());

        return $contacts->random($count);
    }

    /**
     * Generate a batch of recipient statuses biased towards a single outcome
     * scenario, so a good share of histories end up fully 'sent' or fully
     * 'failed' rather than almost always picking up a stray 'queued' one.
     */
    protected function generateRecipientStatuses(int $count): Collection
    {
        $scenario = $this->randomFrom([
            'all_sent', 'all_sent', 'all_sent',
            'all_failed',
            'in_progress', 'in_progress',
        ]);

        return match ($scenario) {
            'all_sent' => collect(array_fill(0, $count, Recipients::STATUS_SENT)),
            'all_failed' => collect(array_fill(0, $count, Recipients::STATUS_FAILED)),
            'in_progress' => $this->randomStatusesWithQueued($count),
        };
    }

    /**
     * Random sent/queued/failed statuses, guaranteed to contain at least one
     * 'queued' entry so the batch resolves to an in-progress history.
     */
    protected function randomStatusesWithQueued(int $count): Collection
    {
        $statuses = collect(range(0, $count - 1))->map(fn () => $this->randomFrom([
            Recipients::STATUS_SENT,
            Recipients::STATUS_QUEUED,
            Recipients::STATUS_QUEUED,
            Recipients::STATUS_FAILED,
        ]));

        if (! $statuses->contains(Recipients::STATUS_QUEUED)) {
            $statuses[0] = Recipients::STATUS_QUEUED;
        }

        return $statuses;
    }

    /**
     * Derive the history status from its recipients: 'queued' if any recipient
     * is still queued, 'sent' if every recipient sent, 'failed' if every
     * recipient failed. generateRecipientStatuses() only ever produces one of
     * these three shapes, so no other combination reaches this method.
     */
    protected function resolveHistoryStatus(Collection $statuses): string
    {
        if ($statuses->contains(Recipients::STATUS_QUEUED)) {
            return History::STATUS_QUEUED;
        }

        if ($statuses->every(fn ($status) => $status === Recipients::STATUS_SENT)) {
            return History::STATUS_SENT;
        }

        return History::STATUS_FAILED;
    }
 
    /**
     * Random date string (Y-m-d) between two relative time expressions.
     */
    protected function randomDate(string $start, string $end): string
    {
        return $this->randomDateTimeBetween($start, $end)->format('Y-m-d');
    }
 
    /**
     * Random Carbon instance between two relative time expressions.
     */
    protected function randomDateTimeBetween(string $start, string $end): \Carbon\Carbon
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);
 
        $randomTs = random_int($startTs, $endTs);
 
        return \Carbon\Carbon::createFromTimestamp($randomTs);
    }
}
