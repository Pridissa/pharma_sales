<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductBatch;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Reports extends Component
{
    public string $period = 'this_month'; // 'today', 'this_week', 'this_month', 'all'
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }

    public function downloadStockCsv()
    {
        $products = Product::with('category')->get();
        $fileName = 'valorisation_stock_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Nom Produit', 'DCI', 'Catégorie', 'Stock', 'Prix Achat (FC)', 'Prix Vente (FC)', 'Valeur Achat Totale', 'Valeur Vente Totale', 'Péremption']);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->name,
                    $p->dci ?: '-',
                    $p->category ? $p->category->name : '-',
                    $p->stock_quantity,
                    $p->purchase_price,
                    $p->price,
                    $p->stock_quantity * $p->purchase_price,
                    $p->stock_quantity * $p->price,
                    $p->expiration_date ? $p->expiration_date->format('d/m/Y') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadSalesCsv()
    {
        $sales = Sale::with(['user', 'items'])->latest()->get();
        $fileName = 'rapport_ventes_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['N° Facture', 'Date/Heure', 'Vendeur / Caissier', 'Mode Paiement', 'Total Vente (FC)', 'Patient', 'Médecin']);

            foreach ($sales as $s) {
                fputcsv($file, [
                    $s->invoice_number,
                    $s->created_at->format('d/m/Y H:i'),
                    $s->user ? $s->user->name : '-',
                    $s->payment_method,
                    $s->total_amount,
                    $s->patient_name ?: '-',
                    $s->doctor_name ?: '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        // 1. Valorisation du Stock
        $totalProductsCount = Product::count();
        $totalStockUnits = Product::sum('stock_quantity');
        
        $stockPurchaseValuation = DB::table('products')
            ->selectRaw('SUM(stock_quantity * purchase_price) as total')
            ->value('total') ?? 0;

        $stockSellingValuation = DB::table('products')
            ->selectRaw('SUM(stock_quantity * price) as total')
            ->value('total') ?? 0;

        $potentialMargin = $stockSellingValuation - $stockPurchaseValuation;

        // 2. Filtrage des ventes selon la période
        $salesQuery = Sale::query();

        if ($this->period === 'today') {
            $salesQuery->whereDate('created_at', Carbon::today());
        } elseif ($this->period === 'this_week') {
            $salesQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->period === 'this_month') {
            $salesQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        }

        $totalRevenue = (float)$salesQuery->sum('total_amount');
        $totalSalesCount = $salesQuery->count();

        $cashRevenue = (float)(clone $salesQuery)->where('payment_method', 'Espèces')->sum('total_amount');
        $mobileRevenue = (float)(clone $salesQuery)->where('payment_method', 'Mobile Money')->sum('total_amount');

        // Estimation Marge Brute réalisée
        $saleIds = (clone $salesQuery)->pluck('id');
        $salesCost = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereIn('sale_items.sale_id', $saleIds)
            ->selectRaw('SUM(sale_items.quantity * products.purchase_price) as total_cost')
            ->value('total_cost') ?? 0;

        $grossProfit = $totalRevenue - $salesCost;
        $profitMarginRate = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Top 5 des produits les plus rentables (par marge)
        $topMarginProducts = Product::select('products.*', DB::raw('SUM(sale_items.quantity * (sale_items.unit_price - products.purchase_price)) as total_profit'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_profit')
            ->take(5)
            ->get();

        // Produits périssables à risque (< 60 jours)
        $expiringBatches = ProductBatch::with('product')
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('expiration_date', '<=', Carbon::now()->addDays(60))
            ->orderBy('expiration_date', 'asc')
            ->get();

        $atRiskFinancialValue = $expiringBatches->sum(fn($b) => $b->quantity * $b->purchase_price);

        return view('livewire.reports', [
            'totalProductsCount' => $totalProductsCount,
            'totalStockUnits' => $totalStockUnits,
            'stockPurchaseValuation' => $stockPurchaseValuation,
            'stockSellingValuation' => $stockSellingValuation,
            'potentialMargin' => $potentialMargin,
            'totalRevenue' => $totalRevenue,
            'totalSalesCount' => $totalSalesCount,
            'cashRevenue' => $cashRevenue,
            'mobileRevenue' => $mobileRevenue,
            'salesCost' => $salesCost,
            'grossProfit' => $grossProfit,
            'profitMarginRate' => $profitMarginRate,
            'topMarginProducts' => $topMarginProducts,
            'expiringBatches' => $expiringBatches,
            'atRiskFinancialValue' => $atRiskFinancialValue,
        ])->layout('components.layouts.app', ['header' => 'Rapports, Marges & Analytics']);
    }
}
