<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $fillable = [
        'user_id',
        'jenis',
        'status',
        'surat_balasan',
        'invoice',
        'bukti_pembayaran',
        'ci_id',
        'ci_id_2',
        'ruangan_id',
        'status_galasan',
        'status_pembayaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

// Relasi CI ke-1 (Sudah ada)
public function ci()
{
    return $this->belongsTo(CorporateInstructor::class, 'ci_id');
}

// TAMBAHKAN: Relasi CI ke-2
public function ci2()
{
    return $this->belongsTo(CorporateInstructor::class, 'ci_id_2');
}
    
public function dataRuangan()
{
    return $this->belongsTo(Ruangan::class, 'ruangan_id', 'id');
}

    public function presentasi()
    {
        return $this->hasOne(Presentasi::class);
    }
}