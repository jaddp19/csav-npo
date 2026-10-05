<?php

namespace Database\Seeders;

use App\Models\Sdg;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Pull actual program IDs from the database instead of hardcoding
        $sdgIds = Sdg::pluck('id');

        if ($sdgIds->isEmpty()) {
            $this->command->error('No programs found. Please seed SdgSeeder first.');
            return;
        }

        foreach ($sdgIds as $sdgId) {
            // One Project Manager
            $pmName = $faker->name;
            $projectManager = User::create([
                'name' => $pmName,
                'user_slug' => Str::slug($pmName . '-' . uniqid()),
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'current_sdg_id' => $sdgId,
            ]);
            $projectManager->assignRole('project-manager');
            $projectManager->sdgs()->attach($sdgId);

            // Five Staff Members
            foreach (range(1, 5) as $j) {
                $staffName = $faker->name;
                $staffUser = User::create([
                    'name' => $staffName,
                    'user_slug' => Str::slug($staffName . '-' . uniqid()),
                    'email' => $faker->unique()->safeEmail,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'remember_token' => Str::random(10),
                    'current_sdg_id' => $sdgId,
                ]);
                $staffUser->assignRole('staff');
                $staffUser->sdgs()->attach($sdgId);
            }
        }
    }
}