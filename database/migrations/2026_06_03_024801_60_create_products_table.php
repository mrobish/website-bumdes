<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('details')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->string('unit')->default('pcs'); // pcs, kg, liter, pack, dll
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->json('gallery_images')->nullable(); // array of image paths
            $table->string('sku')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('views')->default(0);
            $table->integer('sold_count')->default(0);
            $table->string('weight')->nullable(); // for shipping
            $table->string('location')->nullable(); // lokasi produk
            $table->string('whatsapp_order')->nullable(); // nomor WA untuk pesan
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('category_id');
            $table->index('is_featured');
            $table->index('is_published');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
