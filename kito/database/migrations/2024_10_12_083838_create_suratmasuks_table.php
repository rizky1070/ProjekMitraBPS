<?php

use App\Models\Disposisi;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        Schema::create('suratmasuks', function (Blueprint $table) {
            $table->id();
            $table->date('tglterima');
            $table->date('tglsurat');
            $table->string('nosurat');
            $table->string('perihal');
            $table->string('namainstansi');
            $table->string('uraiandisposisi');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suratmasuks');
    }

};
