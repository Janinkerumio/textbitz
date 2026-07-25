<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Template;
use App\Models\History;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

        $sampleNames = ['Alice', 'Bob', 'Charlie', 'Diana', 'Edward', 'Fiona', 'George'];
        $sampleValues = [
            'name' => fn () => fake()->firstName(),
            'business_name' => fn () => fake()->company(),
            'order_id' => fn () => fake()->numberBetween(1000, 9999),
            'amount' => fn () => '$' . fake()->numberBetween(10, 500),
            'due_date' => fn () => fake()->date(),
            'discount' => fn () => fake()->numberBetween(10, 50),
            'product_name' => fn () => fake()->words(2, true),
            'review_link' => fn () => fake()->url(),
            'status' => fn () => fake()->randomElement(['sent', 'queued', 'sent', 'failed']),
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

                $recepients = fake()->randomElements($sampleNames, fake()->numberBetween(1, 4));

                History::create([
                    'user_id' => $userId,
                    'template_id' => $template->id,
                    'blast' => $blast,
                    'status' => fake()->randomElement(['sent', 'queued', 'sent', 'failed']),
                    'recipients' => $recepients,
                    'last_sent_at' => fake()->dateTimeBetween('-2 months', 'now'),
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

                // continue to next iteration instead of aborting the whole seeder
                continue;
            }
        }

        $this->command?->info("HistorySeeder finished: {$successCount} created, {$failCount} failed.");

        if ($failCount > 0) {
            Log::warning("HistorySeeder completed with errors.", [
                'success' => $successCount,
                'failed' => $failCount,
            ]);
        }
    }
}
