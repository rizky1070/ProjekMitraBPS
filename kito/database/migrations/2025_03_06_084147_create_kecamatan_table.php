<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKecamatanTable extends Migration
{
    public function up()
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id('id_kecamatan');
            $table->string('kode_kecamatan',3);
            $table->string('nama_kecamatan');
            $table->unsignedBigInteger('id_kabupaten');
    
            $table->foreign('id_kabupaten')->references('id_kabupaten')->on('kabupaten');

        });
    }

    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
}
