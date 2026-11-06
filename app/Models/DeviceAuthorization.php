<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DeviceAuthorization extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'callback_port' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Generate a new device authorization session.
     */
    public static function createRequest(string $clientName = 'LocalJudge', ?int $callbackPort = null): self
    {
        $deviceCode = Str::random(64);
        // User-friendly 8-character code: e.g. JUDGE-8492
        $randomNum = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $userCode = 'JUDGE-'.$randomNum;

        // Ensure unique user code
        while (static::where('user_code', $userCode)->where('status', 'pending')->exists()) {
            $randomNum = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $userCode = 'JUDGE-'.$randomNum;
        }

        return static::create([
            'device_code' => $deviceCode,
            'user_code' => $userCode,
            'client_name' => $clientName,
            'callback_port' => $callbackPort,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15),
        ]);
    }
}
