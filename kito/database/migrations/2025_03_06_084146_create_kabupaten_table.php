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

        });
    }

    public function down()
    {
        Schema::dropIfExists('kabupaten');
    }
}
