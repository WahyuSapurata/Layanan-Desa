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
        Schema::create('keterangan_pengantar_hewans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('nomor_surat')->nullable();
            $table->string('nama');
            $table->string('nik_penjual');
            $table->string('nomor_penjual');
            $table->string('alamat');
            $table->string('jumlah_hewan');
            $table->json('data_hewan');
            $table->string('nama_pembeli');
            $table->string('nik_pembeli');
            $table->string('nomor_pembeli');
            $table->string('alamat_pembeli');
            $table->string('foto_ktp');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keterangan_pengantar_hewans');
    }
};
