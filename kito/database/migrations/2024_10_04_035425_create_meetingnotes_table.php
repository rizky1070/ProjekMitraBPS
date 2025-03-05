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
        Schema::create('meetingnotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->text('notulen');
            $table->string('filekelengkapan');
            $table->string('catatan')->nullable();
            $table->string('kegiatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetingnotes');
    }
};
