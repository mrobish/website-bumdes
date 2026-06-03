<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_units', function (Blueprint $table) {
            $table->string('slug')->after('code');
            $table->string('color', 20)->after('slug')->default('indigo');
            $table->string('phone', 20)->nullable()->after('color');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
            $table->string('image')->nullable()->after('address');
            $table->longText('about')->nullable()->after('image');
            $table->string('manager')->nullable()->after('about');
            $table->string('operating_hours')->nullable()->after('manager');
            $table->string('status')->after('operating_hours')->default('active');
        });

        // Update existing rows with slugs
        $units = DB::table('business_units')->get();
        foreach ($units as $unit) {
            $slug = Str::slug($unit->name);
            DB::table('business_units')
                ->where('id', $unit->id)
                ->update(['slug' => $slug]);
        }

        // Now add unique constraint
        Schema::table('business_units', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('business_units', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'color', 'phone', 'email', 'address',
                'image', 'about', 'manager', 'operating_hours', 'status'
            ]);
        });
    }
};
