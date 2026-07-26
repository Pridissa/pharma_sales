<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'expiration_date',
        'quantity',
        'purchase_price',
        'supplier_name',
        'is_active',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function isExpiringSoon(int $days = 60): bool
    {
        return $this->expiration_date 
            && !$this->isExpired() 
            && $this->expiration_date->diffInDays(Carbon::now()) <= $days;
    }
}
