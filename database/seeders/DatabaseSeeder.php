<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 3 utilisateurs
        $users = [
            [
                'name' => 'Alice Campaigner',
                'email' => 'alice@acme.test',
                'password' => Hash::make('password'),
                'role' => 'employee'
            ],
            [
                'name' => 'Bob Donor',
                'email' => 'bob@acme.test',
                'password' => Hash::make('password'),
                'role' => 'employee'
            ],
            [
                'name' => 'Charlie Donor',
                'email' => 'charlie@acme.test',
                'password' => Hash::make('password'),
                'role' => 'employee'
            ]
        ];

        DB::table('users')->insert($users);

        $aliceId = DB::table('users')->where('email', 'alice@acme.test')->value('id');
        $bobId = DB::table('users')->where('email', 'bob@acme.test')->value('id');
        $charlieId = DB::table('users')->where('email', 'charlie@acme.test')->value('id');

        // Créer une campagne par Alice
        $campaignId = DB::table('campaigns')->insertGetId([
            'title' => 'Reforestation Project',
            'description' => 'A fundraising campaign to plant 10,000 trees.',
            'goal_amount' => 10000,
            'creator_id' => $aliceId,
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDays(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Bob et Charlie font un don
        DB::table('donations')->insert([
            [
                'amount' => 100,
                'currency' => 'EUR',
                'employee_id' => $bobId,
                'campaign_id' => $campaignId,
                'status' => 'confirmed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'amount' => 200,
                'currency' => 'EUR',
                'employee_id' => $charlieId,
                'campaign_id' => $campaignId,
                'status' => 'confirmed',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
