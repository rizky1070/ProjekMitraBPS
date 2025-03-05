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
        Schema::create('sks', function (Blueprint $table) {
            $table->id();
            $table->string('bulan');
            $table->integer('tahun');
            $table->date('tanggal');
            $table->string('nosurat');
            $table->string('fungsi');
            $table->string('nomorfull');
            $table->string('perihal');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sks');
    }
};
