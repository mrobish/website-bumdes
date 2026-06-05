<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->string('mail_mailer', 10)->default('smtp')->after('recaptcha_secret_key');
            $table->string('mail_host', 100)->nullable()->after('mail_mailer');
            $table->smallInteger('mail_port')->default(587)->after('mail_host');
            $table->string('mail_username', 100)->nullable()->after('mail_port');
            $table->string('mail_password', 100)->nullable()->after('mail_username');
            $table->string('mail_encryption', 10)->nullable()->after('mail_password');
            $table->string('mail_from_address', 100)->nullable()->after('mail_encryption');
            $table->string('mail_from_name', 50)->nullable()->after('mail_from_address');
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->dropColumn([
                'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
                'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name',
            ]);
        });
    }
};
