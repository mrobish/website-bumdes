<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::published()->latest();
        
        if ($request->category) {
            $query->category($request->category);
        }
        
        if ($request->type) {
            $query->type($request->type);
        }
        
        $galleries = $query->paginate(12);
        $categories = Gallery::published()
            ->select('category')
            ->distinct()
            ->pluck('category');
        
        return view('galeri.index', compact('galleries', 'categories'));
    }

    public function show($id)
    {
        $item = Gallery::published()->findOrFail($id);
        $item->increment('views');
        
        return view('galeri.show', compact('item'));
    }
}
