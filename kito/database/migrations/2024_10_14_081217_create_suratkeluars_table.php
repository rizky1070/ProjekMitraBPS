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
        Schema::create('suratkeluars', function (Blueprint $table) {
            $table->id();
            $table->string('perihal');
            $table->string('namainstansi');
            $table->string('jenis')->nullable();
            $table->integer('nomor');
            $table->string('kodeabjad');
            $table->string('kodeangka');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->date('tanggal');
            $table->string('file')->nullable();
            $table->string('nomorfull');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suratkeluars');
    }
};
