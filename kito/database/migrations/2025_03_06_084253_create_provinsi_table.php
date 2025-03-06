<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinsiTable extends Migration
{
    public function up()
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->id('id_provinsi');
            $table->string('kode_provinsi', );
            $table->string('nama_provinsi', );

        });
    }

    public function down()
    {
        Schema::dropIfExists('provinsi');
    }
}
