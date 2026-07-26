<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Pos;
use App\Livewire\Dashboard;
use App\Livewire\Products;
use App\Livewire\Requisitions;
use App\Livewire\SalesHistory;
use App\Livewire\Users;
use App\Livewire\Categories;
use App\Livewire\CashRegister;
use App\Livewire\Reports;
use Illuminate\Support\Facades\Auth;

// Guest Route
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', Pos::class)->name('pos');
    Route::get('/sales-history', SalesHistory::class)->name('sales-history');
    Route::get('/requisitions', Requisitions::class)->name('requisitions');
    Route::get('/cash-register', CashRegister::class)->name('cash-register');
    
    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/products', Products::class)->name('products');
        Route::get('/categories', Categories::class)->name('categories');
        Route::get('/users', Users::class)->name('users');
        Route::get('/reports', Reports::class)->name('reports');
    });
});
