<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraTable extends Migration
{
    public function up()
    {
        Schema::create('mitra', function (Blueprint $table) {
            $table->id('id_mitra');
            $table->unsignedBigInteger('id_kecamatan');
            $table->unsignedBigInteger('id_kabupaten');
            $table->unsignedBigInteger('id_provinsi');
            $table->unsignedBigInteger('id_desa');
            $table->string('foto_profil', 1024)->nullable();
            $table->string('sobat_id', 1024);
            $table->string('nama_lengkap', 1024);
            $table->string('alamat_mitra', 1024);
            $table->smallInteger('jenis_kelamin');
            $table->string('no_hp_mitra', 1024);
            $table->string('email_mitra', 1024);
            $table->date('tahun');
            $table->date('tahun_selesai')->nullable();


            $table->foreign('id_desa')->references('id_desa')->on('desa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kabupaten')->references('id_kabupaten')->on('kabupaten')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kecamatan')->references('id_kecamatan')->on('kecamatan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_provinsi')->references('id_provinsi')->on('provinsi')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mitra');
    }
}
