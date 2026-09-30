<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class AddStatusToInvoicesTable extends Migration
{
    public function up(): void {
        Schema::table('invoices', function (Blueprint $table) {
            // Cek dulu biar nggak error kalau kolom sudah ada
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->string('status')->default('Proses')->after('jumlah_dibayarkan');
            }
        });

        // Pakai cara manual (Raw SQL) supaya tidak butuh doctrine/dbal
        DB::statement('ALTER TABLE invoices MODIFY COLUMN tgl_invoice DATE NULL');
    }

    public function down(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}