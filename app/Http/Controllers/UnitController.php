<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\Product;
use App\Models\News;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Tampilkan semua unit usaha
     */
    public function index()
    {
        $villageInfo = \App\Models\VillageInfo::first();
        $units = BusinessUnit::orderBy('sort_order')->get();
        
        return view('unit.index', compact('villageInfo', 'units'));
    }

    /**
     * Tampilkan detail satu unit usaha
     */
    public function show($slug)
    {
        $villageInfo = \App\Models\VillageInfo::first();
        $unit = BusinessUnit::where('slug', $slug)->firstOrFail();
        
        // Ambil produk milik unit ini
        $products = Product::where('business_unit_id', $unit->id)
            ->published()
            ->latest()
            ->get();
        
        // Ambil berita terkait unit
        $news = News::where('business_unit_id', $unit->id)
            ->published()
            ->latest()
            ->take(5)
            ->get();
        
        return view('unit.show', compact('villageInfo', 'unit', 'products', 'news'));
    }
}
