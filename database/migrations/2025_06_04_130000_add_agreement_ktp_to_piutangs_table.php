<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piutangs', function (Blueprint $table) {
            $table->string('agreement_letter_path')->nullable()->after('attachment_path');
            $table->string('ktp_photo_path')->nullable()->after('agreement_letter_path');
        });
    }

    public function down(): void
    {
        Schema::table('piutangs', function (Blueprint $table) {
            $table->dropColumn(['agreement_letter_path', 'ktp_photo_path']);
        });
    }
};
