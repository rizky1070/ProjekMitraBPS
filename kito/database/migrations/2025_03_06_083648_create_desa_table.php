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
        });
    }

    public function down()
    {
        Schema::dropIfExists('desa');
    }
}
