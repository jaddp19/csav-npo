<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SdgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sdgs = [
            'BSPsych',
            'BSSW',
            'BSA',
            'BSAB',
            'BSOA',
            'BSTM',
            'BSCrim',
            'BEED',
            'BPEd',
            'TCP',
            'BSCE',
            'BSME',
            'BSCS',
            'BSIS',
        ];

        foreach ($sdgs as $sdg) {
            DB::table('sdgs')->insert([
                'name' => $sdg,
                'created_at' => now(),
                'updated_at' => now(),
                'slug' => Str::slug($sdg),
            ]);
        }
    }
}
