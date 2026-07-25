<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Template;
use App\Models\History;
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
 
                $recipients = $this->randomRecipients();
 
                History::create([
                    'user_id' => $userId,
                    'template_id' => $template->id,
                    'blast' => $blast,
                    'status' => $this->randomStatus(),
                    'recipients' => $recipients,
                    'last_sent_at' => $this->randomDateTimeBetween('-2 months', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
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
     * Random recipients: 1 to 4 unique names from the pool.
     */
    protected function randomRecipients(): array
    {
        $count = random_int(1, 4);
        $keys = array_rand($this->firstNames, min($count, count($this->firstNames)));
        $keys = is_array($keys) ? $keys : [$keys];
 
        return array_values(array_map(fn ($k) => $this->firstNames[$k], $keys));
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
