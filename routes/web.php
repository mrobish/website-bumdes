<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VillageInfoController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    $villageInfo = \App\Models\VillageInfo::first();
    
    // Get latest news for homepage
    $latestNews = \App\Models\News::published()
        ->latest('published_at')
        ->take(3)
        ->get();
    
    // Get featured products
    $featuredProducts = \App\Models\Product::published()
        ->featured()
        ->take(4)
        ->get();
    
    // Get latest gallery items
    $latestGallery = \App\Models\Gallery::published()
        ->latest()
        ->take(6)
        ->get();
    
    return view('welcome', compact('villageInfo', 'latestNews', 'featuredProducts', 'latestGallery'));
});

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Village Info Routes
Route::get('/profil-desa', [VillageInfoController::class, 'profile'])->name('village.profile');
Route::get('/geografis', [VillageInfoController::class, 'geography'])->name('village.geography');
Route::get('/demografi', [VillageInfoController::class, 'demographics'])->name('village.demographics');
Route::get('/pemerintahan', [VillageInfoController::class, 'government'])->name('village.government');
Route::get('/ekonomi', [VillageInfoController::class, 'economy'])->name('village.economy');
Route::get('/bumdes', [VillageInfoController::class, 'bumdes'])->name('village.bumdes');
Route::get('/potensi', [VillageInfoController::class, 'potential'])->name('village.potential');
Route::get('/kontak', [VillageInfoController::class, 'contact'])->name('village.contact');

// News Routes
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

// Gallery Routes
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/galeri/{id}', [GalleryController::class, 'show'])->name('gallery.show');

// Product Routes
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

// Unit Usaha Routes
Route::get('/unit-usaha', [UnitController::class, 'index'])->name('units.index');
Route::get('/unit-usaha/{slug}', [UnitController::class, 'show'])->name('units.show');

// PWA Offline Transaction
Route::get('/offline-transaksi', function () {
    return view('offline-transaction');
})->name('offline.transaction');

// PDF Report Downloads (authenticated)
Route::middleware(['auth'])->prefix('admin/pdf')->group(function () {
    Route::get('/neraca-saldo', [\App\Http\Controllers\PdfReportController::class, 'neracaSaldo'])->name('pdf.neraca-saldo');
    Route::get('/laba-rugi', [\App\Http\Controllers\PdfReportController::class, 'labaRugi'])->name('pdf.laba-rugi');
    Route::get('/neraca', [\App\Http\Controllers\PdfReportController::class, 'neraca'])->name('pdf.neraca');
    Route::get('/jurnal-umum', [\App\Http\Controllers\PdfReportController::class, 'jurnalUmum'])->name('pdf.jurnal-umum');
});

// Buku Besar PDF
Route::middleware(['auth'])->prefix('admin/pdf')->group(function () {
    Route::get('/buku-besar', [\App\Http\Controllers\PdfReportController::class, 'bukuBesar'])->name('pdf.buku-besar');
});
