<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasis';
    protected $fillable = [
        'mahasiswa_id', 'nama', 'instansi', 'kontak', 'jenis_kelamin', 'pendidikan',
        'umur', 'nama_kegiatan', 'kritik', 'saran', 'nilai_ikm',
    ];

    public function jawaban()
    {
        return $this->hasMany(EvaluasiJawaban::class, 'evaluasi_id');
    }
}
