<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\CashSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Active Cash Session for current user
        $currentSession = CashSession::activeForUser(auth()->id());
        $sessionCashTotal = 0;
        $sessionMobileTotal = 0;
        $sessionSalesCount = 0;

        if ($currentSession) {
            $sessionCashTotal = Sale::where('cash_session_id', $currentSession->id)
                ->where('payment_method', 'Espèces')
                ->sum('total_amount');

            $sessionMobileTotal = Sale::where('cash_session_id', $currentSession->id)
                ->where('payment_method', 'Mobile Money')
                ->sum('total_amount');

            $sessionSalesCount = Sale::where('cash_session_id', $currentSession->id)->count();
        }

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

        // Top Selling Products
        $topSellingProducts = Product::select('products.*', DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Least Selling / Unsold Products
        $leastSellingProducts = Product::select('products.*', DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();

        // --- Chart 1: 7-Day Sales Trend ---
        $salesTrendLabels = [];
        $salesTrendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $salesTrendLabels[] = $date->format('d/m');
            $salesTrendData[] = (float) Sale::whereDate('created_at', $date)->sum('total_amount');
        }

        // --- Chart 2: Sales by Category ---
        $categorySales = Category::select('categories.name', DB::raw('COALESCE(SUM(sale_items.subtotal), 0) as total_sales'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();

        $categoryChartLabels = $categorySales->pluck('name')->toArray();
        $categoryChartData = $categorySales->pluck('total_sales')->map(fn($v) => (float)$v)->toArray();

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
            // Live Cash Session
            'currentSession' => $currentSession,
            'sessionCashTotal' => $sessionCashTotal,
            'sessionMobileTotal' => $sessionMobileTotal,
            'sessionSalesCount' => $sessionSalesCount,
            // Charts Data
            'salesTrendLabels' => $salesTrendLabels,
            'salesTrendData' => $salesTrendData,
            'categoryChartLabels' => $categoryChartLabels,
            'categoryChartData' => $categoryChartData,
        ])->layout('components.layouts.app', ['header' => 'Tableau de Bord & Vue d\'Ensemble']);
    }
}
