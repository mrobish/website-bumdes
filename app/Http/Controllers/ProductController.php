<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::published()->with('category');
        
        if ($request->category) {
            $query->category($request->category);
        }
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->sort == 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_high') {
            $query->orderBy('price', 'desc');
        } elseif ($request->sort == 'popular') {
            $query->orderBy('sold_count', 'desc');
        } else {
            $query->latest();
        }
        
        $products = $query->paginate(12);
        $categories = ProductCategory::active()->withCount('products')->get();
        
        return view('produk.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();
        
        $product->increment('views');
        
        // Get related products
        $related = Product::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
        
        return view('produk.show', compact('product', 'related'));
    }
}
