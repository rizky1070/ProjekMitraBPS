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
        Schema::create('surattugas', function (Blueprint $table) {
            $table->id();
            $table->string('nosurat');
            $table->string('nomorfull');
            $table->string('fungsi');
            $table->string('bulan'); 
            $table->integer('tahun');
            $table->date('tanggalsurat');
            $table->date('tanggalmulai');
            $table->date('tanggalselesai');
            $table->string('tujuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surattugas');
    }
};
