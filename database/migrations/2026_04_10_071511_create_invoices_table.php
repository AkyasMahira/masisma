<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
  public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->string('jenis_kegiatan');
            $table->string('jenjang');
            $table->string('prodi');
            $table->string('instansi');
            $table->string('ruangan_ci');
            $table->date('tgl_mulai');
            $table->date('tgl_akhir');
            $table->integer('jml_mhs');
            $table->integer('jml_minggu');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2); // Jml Mhs * Jml Minggu * Harga
            $table->decimal('biaya_konsumsi', 15, 2)->default(0);
            $table->decimal('jumlah_dibayarkan', 15, 2); // Total Harga + Konsumsi
            $table->text('nama_mahasiswa_penelitian')->nullable();
            $table->date('tgl_invoice');
            $table->date('tgl_jatuh_tempo');
            $table->date('tgl_pembayaran')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->string('bank')->nullable();
            $table->string('penanggung_jawab');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
}
