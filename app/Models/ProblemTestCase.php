<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemTestCase extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'time_limit_ms' => 'integer',
            'is_hidden' => 'boolean',
            'order_index' => 'integer',
        ];
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class, 'problem_id', 'id');
    }
}
