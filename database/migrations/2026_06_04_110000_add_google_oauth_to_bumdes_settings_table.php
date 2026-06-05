<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->text('google_client_id')->nullable()->after('custom_css');
            $table->text('google_client_secret')->nullable()->after('google_client_id');
            $table->text('google_redirect_uri')->nullable()->after('google_client_secret');
            $table->boolean('google_login_enabled')->default(false)->after('google_redirect_uri');
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->dropColumn(['google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_login_enabled']);
        });
    }
};
