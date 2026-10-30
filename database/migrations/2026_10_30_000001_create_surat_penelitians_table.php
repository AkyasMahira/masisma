<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Register surat penelitian otomatis: nomor dibuat sekali & tetap (tidak berubah saat surat dibuka ulang),
 * plus kode verifikasi untuk QR.
 * jenis_surat: balasan (izin), pengantar (ke ruangan), selesai (keterangan selesai).
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_penelitians', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pra_penelitian_id')->constrained('pra_penelitians')->cascadeOnDelete();
            $t->unsignedBigInteger('pengajuan_id')->nullable()->index();
            $t->string('jenis_surat', 20);
            $t->unsignedSmallInteger('tahun');
            $t->unsignedInteger('nomor_urut');
            $t->string('nomor_surat', 80)->unique();
            $t->date('tanggal_surat');
            $t->string('kode_verifikasi', 40)->unique();
            $t->string('file_path')->nullable();
            $t->timestamps();

            $t->unique(['pra_penelitian_id', 'jenis_surat']);
            $t->index(['jenis_surat', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_penelitians');
    }
};
