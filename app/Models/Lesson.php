<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'minutes' => 'integer',
            'order' => 'integer',
            'blocks' => 'array',
            'quiz' => 'array',
            'discussion' => 'array',
        ];
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class, 'track_id', 'id');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserLessonProgress::class, 'lesson_id', 'id');
    }
}
