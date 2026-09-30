<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentLinkToInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void {
    Schema::table('invoices', function (Blueprint $table) {
        $table->uuid('payment_token')->nullable()->unique(); // Untuk link publik
        $table->string('ttd_method')->default('manual'); // manual atau digital
        $table->string('bank_pengirim')->nullable();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
