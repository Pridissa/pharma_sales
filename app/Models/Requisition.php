<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'requested_quantity',
        'user_id',
        'status',
        'type',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Synchronise automatiquement les réquisitions pour les produits en alerte
     * ou dont le stock approche du seuil d'alerte.
     */
    public static function syncAlertRequisitions(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $isLowStock = $product->stock_quantity <= $product->min_stock_alert;
            $isApproachingAlert = !$isLowStock && ($product->stock_quantity <= ceil($product->min_stock_alert * 1.5) || $product->stock_quantity <= ($product->min_stock_alert + 5));

            if ($isLowStock || $isApproachingAlert) {
                $type = $isLowStock ? 'seuil_alerte' : 'approche_alerte';
                $notes = $isLowStock 
                    ? "Stock au seuil d'alerte ({$product->stock_quantity} dispo, min: {$product->min_stock_alert})"
                    : "Stock proche du seuil d'alerte ({$product->stock_quantity} dispo, min: {$product->min_stock_alert})";

                $existing = self::where('product_id', $product->id)
                    ->where('status', 'en_attente')
                    ->first();

                if (!$existing) {
                    self::create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'requested_quantity' => max(10, $product->min_stock_alert * 2),
                        'user_id' => auth()->id() ?? 1,
                        'status' => 'en_attente',
                        'type' => $type,
                        'notes' => $notes,
                    ]);
                } else {
                    // Mettre à jour le type et les notes si l'état s'aggrave
                    $existing->update([
                        'type' => $type,
                        'notes' => $notes,
                    ]);
                }
            } else {
                // Si le stock est suffisant, nettoyer les réquisitions d'alerte en attente pour ce produit
                self::where('product_id', $product->id)
                    ->where('status', 'en_attente')
                    ->whereIn('type', ['seuil_alerte', 'approche_alerte'])
                    ->delete();
            }
        }
    }
}
