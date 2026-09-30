<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = ['id'];

    // Relasi balik ke Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}