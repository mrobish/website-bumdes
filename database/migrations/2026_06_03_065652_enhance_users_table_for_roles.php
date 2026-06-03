<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator')->after('name'); // super_admin, admin, operator, bendahara, viewer
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->text('last_login_ip')->nullable()->after('last_login_at');
            $table->integer('login_count')->default(0)->after('last_login_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'avatar', 'status', 'last_login_at', 'last_login_ip', 'login_count']);
        });
    }
};
