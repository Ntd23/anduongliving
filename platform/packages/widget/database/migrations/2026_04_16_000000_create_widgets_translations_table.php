<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('widgets_translations', function (Blueprint $table): void {
            $table->string('lang_code', 191)->index();
            $table->foreignId('widgets_id')->index();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique(['lang_code', 'widgets_id']);
            $table->foreign('widgets_id')->references('id')->on('widgets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets_translations');
    }
};