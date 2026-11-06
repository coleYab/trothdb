<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_secret' => 'boolean',
        ];
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class, 'problem_id', 'id');
    }
}
