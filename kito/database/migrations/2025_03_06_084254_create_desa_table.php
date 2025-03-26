<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesaTable extends Migration
{
    public function up()
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->id('id_desa');
            $table->string('kode_desa');
            $table->string('nama_desa');
            $table->unsignedBigInteger('id_kecamatan');
    
            $table->foreign('id_kecamatan')->references('id_kecamatan')->on('kecamatan');

        });
    }

    public function down()
    {
        Schema::dropIfExists('desa');
    }
}
