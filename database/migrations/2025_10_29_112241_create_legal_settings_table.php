<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('impressum_title')->nullable();
            $table->longText('impressum_html')->nullable();

            $table->string('datenschutz_title')->nullable();
            $table->longText('datenschutz_html')->nullable();

            $table->string('agb_title')->nullable();
            $table->longText('agb_html')->nullable();

            $table->string('widerruf_title')->nullable();
            $table->longText('widerruf_html')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_settings');
    }
};
