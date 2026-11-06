<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Track extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'hours' => 'integer',
            'order' => 'integer',
            'prerequisites' => 'array',
        ];
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'track_id', 'id')->orderBy('order');
    }
}
