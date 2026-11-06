<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_sekolahs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sekolah_id')
                ->unique()
                ->constrained('sekolahs')
                ->cascadeOnDelete();

            $table->string('kepala_sekolah')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->year('tahun_berdiri')->nullable();
            $table->string('akreditasi', 5)->nullable();
            $table->integer('jumlah_siswa')->nullable();
            $table->integer('jumlah_guru')->nullable();
            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_sekolahs');
    }
};