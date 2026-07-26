<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_barre',
        'name',
        'dci',
        'category_id',
        'price',
        'purchase_price',
        'stock_quantity',
        'min_stock_alert',
        'expiration_date',
        'requires_prescription',
        'dosage_unit',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_alert' => 'integer',
        'expiration_date' => 'date',
        'requires_prescription' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_alert;
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

    /**
     * Re-synchronise la quantité totale en stock à partir de la somme des lots actifs,
     * ou conserve la valeur actuelle si aucun lot n'a été spécifié.
     */
    public function syncStockFromBatches(): void
    {
        if ($this->batches()->exists()) {
            $totalQty = $this->batches()->where('is_active', true)->sum('quantity');
            $nearestExpBatch = $this->batches()
                ->where('is_active', true)
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date', 'asc')
                ->first();

            $this->update([
                'stock_quantity' => $totalQty,
                'expiration_date' => $nearestExpBatch ? $nearestExpBatch->expiration_date : $this->expiration_date,
            ]);
        }
    }

    /**
     * Déduit la quantité du stock en appliquant la méthode FEFO (First Expired, First Out)
     * et enregistre les mouvements de stock correspondants.
     */
    public function deductFefoStock(int $requestedQty, ?int $userId = null, ?string $referenceNumber = null): void
    {
        $remainingToDeduct = $requestedQty;
        $prevStock = $this->stock_quantity;

        // Récupérer les lots actifs avec du stock, triés par date de péremption la plus proche (FEFO)
        $activeBatches = $this->batches()
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->orderBy('expiration_date', 'asc')
            ->get();

        if ($activeBatches->count() > 0) {
            foreach ($activeBatches as $batch) {
                if ($remainingToDeduct <= 0) break;

                $deductFromBatch = min($batch->quantity, $remainingToDeduct);
                $prevBatchQty = $batch->quantity;
                $batch->decrement('quantity', $deductFromBatch);
                $remainingToDeduct -= $deductFromBatch;

                // Enregistrer le mouvement pour le lot
                StockMovement::create([
                    'product_id' => $this->id,
                    'product_batch_id' => $batch->id,
                    'user_id' => $userId,
                    'type' => 'vente',
                    'quantity' => -$deductFromBatch,
                    'previous_quantity' => $prevBatchQty,
                    'new_quantity' => $batch->quantity,
                    'reason' => 'Vente POS (FEFO)',
                    'reference_number' => $referenceNumber,
                ]);
            }
        }

        // Déduire le stock global du produit
        $this->decrement('stock_quantity', $requestedQty);

        // Si des lots existent, resynchroniser la date d'expiration du produit au prochain lot actif
        $this->syncStockFromBatches();

        // Si aucun lot n'était configuré, enregistrer quand même un mouvement global
        if ($activeBatches->count() === 0) {
            StockMovement::create([
                'product_id' => $this->id,
                'product_batch_id' => null,
                'user_id' => $userId,
                'type' => 'vente',
                'quantity' => -$requestedQty,
                'previous_quantity' => $prevStock,
                'new_quantity' => $this->stock_quantity,
                'reason' => 'Vente POS',
                'reference_number' => $referenceNumber,
            ]);
        }
    }
}
