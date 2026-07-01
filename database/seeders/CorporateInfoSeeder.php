<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CorporateInfo;
use App\Models\User;

class CorporateInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(User::all() as $user)
        {
            CorporateInfo::create([
                'user_id' => $user->id,
                'business_name' => 'Right Apps Incorporated',
                'sms_signature' => 'Right Apps'
            ]);
        }
    }
}
