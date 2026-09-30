<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiklatPeserta extends Model
{
    protected $fillable = [
        'diklat_form_id',
        'nama_lengkap',
        'gelar',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'email', // Ini akan diisi Email Plataran Sehat Peserta
        'nip',
        'pangkat_golongan',
        'jabatan',
        'instansi',
        'alamat',
        'no_hp', // Ini WhatsApp Peserta
        
        // --- FIELD BARU ---
        'profesi',
        'pendidikan_terakhir',
        'status_pegawai',
        // ------------------

        'pilihan_pelatihan', 
        'pilihan_tempat',    
        'ukuran_kaos',
        'pas_foto',
        'jawaban_custom',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'pilihan_pelatihan' => 'array',
        'pilihan_tempat' => 'array',
        'jawaban_custom' => 'array',
    ];
}