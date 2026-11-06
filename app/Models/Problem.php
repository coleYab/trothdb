<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'acceptance_rate' => 'float',
            'total_points' => 'integer',
            'time_limit_ms' => 'integer',
            'memory_limit_mb' => 'integer',
            'version' => 'integer',
            'is_active' => 'boolean',
            'tags' => 'array',
            'description' => 'array',
            'examples' => 'array',
            'constraints' => 'array',
            'editorial' => 'array',
            'starter_code' => 'array',
            'testing' => 'array',
            'hints' => 'array',
            'companies' => 'array',
        ];
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(ProblemTestCase::class, 'problem_id', 'id')->orderBy('order_index');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ProblemAsset::class, 'problem_id', 'id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'problem_id', 'id');
    }

    public function dailyChallenges(): HasMany
    {
        return $this->hasMany(DailyChallenge::class, 'problem_id', 'id');
    }

    /**
     * Scope to return the summary representation.
     */
    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'title' => $this->title,
            'difficulty' => $this->difficulty,
            'acceptance_rate' => (float) $this->acceptance_rate,
            'tags' => $this->tags ?? [],
            'summary' => $this->summary,
            'discipline' => $this->discipline ?? 'Algorithms',
            'topic' => $this->topic ?? ($this->tags[0] ?? 'General'),
            'companies' => $this->companies ?? [],
            'total_points' => (int) ($this->total_points ?? 100),
            'time_limit_ms' => (int) ($this->time_limit_ms ?? 2000),
        ];
    }
}
