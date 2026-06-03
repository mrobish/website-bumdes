<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // e.g., 'INDUK', 'PANG', 'WIS', 'SAM', 'NET'
            $table->string('name'); // e.g., 'BUMDes Induk', 'Unit Ketahanan Pangan'
            $table->text('description')->nullable();
            $table->enum('type', ['induk', 'unit_usaha'])->default('unit_usaha');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_units');
    }
};
