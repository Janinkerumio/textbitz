<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Template;
use App\Models\History;

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
            'status' => fn () => fake()->randomElement(['closed', 'open with limited hours']),
        ];
 
        foreach (range(1, 20) as $i) {
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
                'recipients' => json_encode($recepients),
                'last_sent_at' => fake()->dateTimeBetween('-2 months', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
