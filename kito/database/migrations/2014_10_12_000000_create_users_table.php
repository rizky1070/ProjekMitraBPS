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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('jabatan');
            $table->string('nomer_telepon')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_leader')->default(false);
            $table->boolean('is_hamukti')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('username')->unique();
            $table->string('gambar')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
