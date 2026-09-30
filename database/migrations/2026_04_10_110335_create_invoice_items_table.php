<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel invoices
            // onDelete('cascade') artinya kalau Invoice dihapus, rincian itemnya otomatis ikut kehapus
            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->onDelete('cascade');

            $table->string('deskripsi'); // Nama layanan/diklat
            $table->integer('jml_mhs')->default(1);
            $table->integer('jml_minggu')->default(1);
            
            // Menggunakan decimal(15,2) agar aman untuk perhitungan mata uang
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};