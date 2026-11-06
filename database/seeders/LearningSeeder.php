<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Track;
use Illuminate\Database\Seeder;

class LearningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = config('learning', []);

        foreach ($tracks as $trackIndex => $trackData) {
            Track::updateOrCreate(
                ['id' => $trackData['slug']],
                [
                    'title' => $trackData['title'],
                    'level' => $trackData['level'],
                    'hours' => $trackData['hours'],
                    'summary' => $trackData['summary'],
                    'description' => $trackData['description'],
                    'prerequisites' => $trackData['prerequisites'] ?? [],
                    'continue_lesson' => $trackData['continueLesson'] ?? null,
                    'order' => $trackIndex + 1,
                ]
            );

            // Find module info for each lesson
            $moduleMap = [];
            foreach ($trackData['modules'] ?? [] as $module) {
                foreach ($module['items'] ?? [] as $item) {
                    if (($item['type'] ?? '') === 'lesson' && ! empty($item['lessonSlug'])) {
                        $moduleMap[$item['lessonSlug']] = [
                            'module_title' => $module['title'],
                            'module_summary' => $module['summary'] ?? null,
                        ];
                    }
                }
            }

            foreach ($trackData['lessons'] ?? [] as $lessonIndex => $lessonData) {
                $mod = $moduleMap[$lessonData['slug']] ?? [
                    'module_title' => 'Core concepts',
                    'module_summary' => null,
                ];

                Lesson::updateOrCreate(
                    ['id' => $lessonData['slug']],
                    [
                        'track_id' => $trackData['slug'],
                        'slug' => $lessonData['slug'],
                        'title' => $lessonData['title'],
                        'summary' => $lessonData['summary'],
                        'minutes' => $lessonData['minutes'],
                        'order' => $lessonIndex + 1,
                        'module_title' => $mod['module_title'],
                        'module_summary' => $mod['module_summary'],
                        'blocks' => $lessonData['blocks'] ?? [],
                        'quiz' => $lessonData['quiz'] ?? [],
                        'assembly' => $lessonData['assembly'] ?? '',
                        'discussion' => $lessonData['discussion'] ?? [],
                    ]
                );
            }
        }
    }
}
