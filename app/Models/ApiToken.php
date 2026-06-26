<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createForUser(User $user, string $name = 'teacher-app'): string
    {
        $plainTextToken = Str::random(64);

        self::create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
        ]);

        return $plainTextToken;
    }

    public static function findUserByPlainToken(?string $plainTextToken): ?User
    {
        if (!$plainTextToken) {
            return null;
        }

        $apiToken = self::query()
            ->where('token', hash('sha256', $plainTextToken))
            ->first();

        if (!$apiToken) {
            return null;
        }

        $apiToken->forceFill(['last_used_at' => now()])->save();

        return $apiToken->user;
    }
}
