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
        Schema::create('bas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('nosurat');
            $table->string('kodesurat');
            $table->string('uraian');
            $table->string('fungsi');
            $table->string('suratfull');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bas');
    }
};
