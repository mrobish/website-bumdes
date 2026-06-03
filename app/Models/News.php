<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'category',
        'business_unit_id',
        'is_featured',
        'is_published',
        'views',
        'author',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            if (is_null($news->published_at)) {
                $news->published_at = now();
            }
        });
    }

    // Scope: published only
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope: featured
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope: by category
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Relationship: business unit
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_id');
    }

    // Get image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return 'https://placehold.co/800x400/e2e8f0/64748b?text=Berita';
    }

    // Get excerpt from content
    public function getExcerptAttribute()
    {
        if ($this->attributes['excerpt']) {
            return $this->attributes['excerpt'];
        }
        return Str::limit(strip_tags($this->content), 150);
    }
}
