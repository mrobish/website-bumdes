<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image',
        'type',
        'category',
        'is_published',
        'views',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Scope: published only
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope: by type
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope: by category
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Get image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return 'https://placehold.co/600x400/e2e8f0/64748b?text=Galeri';
    }
}
