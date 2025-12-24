<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('ktp'); // JANGAN dikasih unique() biar 1 NIK bisa banyak data
            $table->string('no_sertif')->unique(); // INI WAJIB UNIQUE biar gak ada sertifikat ganda
            $table->string('no_reg');
            $table->string('kualifikasi');
            $table->string('wilayah');
            $table->date('tgl_terbit');
            $table->date('tgl_expired');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};
