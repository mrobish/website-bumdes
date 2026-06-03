<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'business_unit_id',
        'name',
        'slug',
        'description',
        'details',
        'price',
        'discount_price',
        'unit',
        'stock',
        'image',
        'gallery_images',
        'sku',
        'is_featured',
        'is_published',
        'views',
        'sold_count',
        'weight',
        'location',
        'whatsapp_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'gallery_images' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relationship: category
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Relationship: business unit
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_id');
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
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Get image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return 'https://placehold.co/400x300/e2e8f0/64748b?text=Produk';
    }

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Get formatted discount price
    public function getFormattedDiscountPriceAttribute()
    {
        if ($this->discount_price) {
            return 'Rp ' . number_format($this->discount_price, 0, ',', '.');
        }
        return null;
    }

    // Check if has discount
    public function getHasDiscountAttribute()
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    // Check if in stock
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    // Generate WhatsApp order link
    public function getWhatsAppLinkAttribute()
    {
        $number = $this->whatsapp_order ?? '';
        $message = "Halo, saya tertarik dengan produk *{$this->name}* seharga {$this->formatted_price}. Apakah masih tersedia?";
        $encoded = urlencode($message);
        return "https://wa.me/{$number}?text={$encoded}";
    }
}
