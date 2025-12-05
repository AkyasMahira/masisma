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
        'email',
        'nip',
        'pangkat_golongan',
        'jabatan',
        'instansi',
        'alamat',
        'no_hp',
        'pilihan_pelatihan', // JSON
        'pilihan_tempat',    // JSON
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
