<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraSurveiTable extends Migration
{
    public function up()
    {
        Schema::create('mitra_survei', function (Blueprint $table) {
            $table->id('id_mitra_survei');
            $table->unsignedBigInteger('id_mitra');
            $table->unsignedBigInteger('id_survei');
            $table->unsignedBigInteger('id_posisi_mitra')->nullable();
            $table->string('rate_honor')->nullable();
            $table->string('catatan')->nullable();
            $table->string('nilai')->nullable();
            $table->string('vol')->nullable();
            $table->date('tgl_ikut_survei')->nullable();

            $table->foreign('id_posisi_mitra')->references('id_posisi_mitra')->on('posisi_mitra')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mitra')->references('id_mitra')->on('mitra')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_survei')->references('id_survei')->on('survei')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mitra_survei');
    }
}