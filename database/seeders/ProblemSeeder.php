<?php

namespace Database\Seeders;

use App\Models\DailyChallenge;
use App\Models\Problem;
use Illuminate\Database\Seeder;

class ProblemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $problems = config('problems', []);

        foreach ($problems as $problemData) {
            Problem::updateOrCreate(
                ['id' => $problemData['id']],
                $problemData
            );
        }

        // Seed daily challenge for today
        DailyChallenge::updateOrCreate(
            ['date' => '2026-11-04'],
            [
                'problem_id' => 'ring-buffer',
                'participants_count' => 1284,
            ]
        );

        // Seed yesterday's daily challenge
        DailyChallenge::updateOrCreate(
            ['date' => '2026-11-03'],
            [
                'problem_id' => 'two-sum',
                'participants_count' => 2190,
            ]
        );
    }
}
