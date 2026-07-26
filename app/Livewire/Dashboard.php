<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }

    public function render()
    {
        $today = Carbon::today();

        // KPIs
        $todayRevenue = Sale::whereDate('created_at', $today)->sum('total_amount');
        $todaySalesCount = Sale::whereDate('created_at', $today)->count();
        $totalProductsCount = Product::count();

        // Low stock products
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->orderBy('stock_quantity', 'asc')
            ->take(6)
            ->get();

        // Expiring products (within 60 days)
        $expiringProducts = Product::with('category')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', Carbon::now()->addDays(60))
            ->orderBy('expiration_date', 'asc')
            ->take(6)
            ->get();

        // Recent sales with user/cashier relationship
        $recentSales = Sale::with(['items', 'user'])->latest()->take(5)->get();

        // Top Selling Products (Produits les plus vendus)
        $topSellingProducts = Product::select('products.*', \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Least Selling / Unsold Products (Produits les moins vendus / Stocks dormants)
        $leastSellingProducts = Product::select('products.*', \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'todayRevenue' => $todayRevenue,
            'todaySalesCount' => $todaySalesCount,
            'totalProductsCount' => $totalProductsCount,
            'lowStockCount' => Product::whereColumn('stock_quantity', '<=', 'min_stock_alert')->count(),
            'expiringSoonCount' => Product::whereNotNull('expiration_date')->where('expiration_date', '<=', Carbon::now()->addDays(60))->count(),
            'lowStockProducts' => $lowStockProducts,
            'expiringProducts' => $expiringProducts,
            'recentSales' => $recentSales,
            'topSellingProducts' => $topSellingProducts,
            'leastSellingProducts' => $leastSellingProducts,
        ])->layout('components.layouts.app', ['header' => 'Tableau de Bord & Vue d\'Ensemble']);
    }
}
