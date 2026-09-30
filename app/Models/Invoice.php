<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    
    // Semua kolom bisa diisi kecuali ID
    protected $guarded = ['id'];

    /**
     * Helper untuk generate nomor invoice otomatis
     * Format: 900/001/418.101/2026 (Menyesuaikan contoh gambar kamu)
     */
    public static function generateNumber() {
        $date = now();
        // Hitung jumlah invoice di tahun berjalan
        $count = self::whereYear('created_at', $date->year)->count();
        $sequence = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        
        // Kode 418.101 biasanya kode instansi RSUD SLG
        return "900/{$sequence}/418.101/{$date->format('Y')}";
    }
public function items() {
    return $this->hasMany(InvoiceItem::class); // Sesuaikan nama model detailnya
}
    /**
     * Scope untuk mempermudah filter status di Controller
     */
    public function scopeStatus($query, $status) {
        if ($status) {
            return $query->where('status', $status);
        }
    }
    
    
}