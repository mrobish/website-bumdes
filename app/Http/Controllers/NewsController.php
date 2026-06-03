<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()
            ->latest('published_at')
            ->paginate(9);
        
        return view('news.index', compact('news'));
    }

    public function show($slug)
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();
        
        // Increment views
        $article->increment('views');
        
        // Get related news
        $related = News::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('news.show', compact('article', 'related'));
    }
}
