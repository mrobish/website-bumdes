<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->tinyInteger('otp_attempts')->default(0)->after('otp');
            $table->timestamp('otp_locked_until')->nullable()->after('otp_attempts');
            $table->tinyInteger('otp_send_count')->default(0)->after('otp_locked_until');
            $table->timestamp('otp_last_send_at')->nullable()->after('otp_send_count');
            $table->string('purpose')->default('reset_password')->after('email'); // reset_password or forgot_username
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn(['otp_attempts', 'otp_locked_until', 'otp_send_count', 'otp_last_send_at', 'purpose']);
        });
    }
};
