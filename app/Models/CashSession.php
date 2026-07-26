<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'opening_balance',
        'closing_balance_expected',
        'closing_balance_actual',
        'difference',
        'status', // open, closed
        'opening_notes',
        'closing_notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance_expected' => 'decimal:2',
        'closing_balance_actual' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public static function activeForUser(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();
    }
}
