<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKabupatenTable extends Migration
{
    public function up()
    {
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id('id_kabupaten');
            $table->string('kode_kabupaten', );
            $table->string('nama_kabupaten', );
            $table->unsignedBigInteger('id_provinsi');
    
            $table->foreign('id_provinsi')->references('id_provinsi')->on('provinsi');

        });
    }

    public function down()
    {
        Schema::dropIfExists('kabupaten');
    }
}
