<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasis';
    protected $fillable = [
        'jenis', 'kegiatan_id', 'mou_id', 'pra_penelitian_id',
        'mahasiswa_id', 'nama', 'instansi', 'kontak', 'jenis_kelamin', 'pendidikan',
        'umur', 'nama_kegiatan', 'kritik', 'saran', 'nilai_ikm',
    ];

    public function jawaban()
    {
        return $this->hasMany(EvaluasiJawaban::class, 'evaluasi_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function mou()
    {
        return $this->belongsTo(Mou::class, 'mou_id');
    }

    public function praPenelitian()
    {
        return $this->belongsTo(PraPenelitian::class, 'pra_penelitian_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /** Token link publik per pelatihan / penelitian (tidak bisa ditebak dari ID saja). */
    public static function token($jenis, $id)
    {
        return substr(hash_hmac('sha256', $jenis . ':' . $id, (string) config('app.key')), 0, 16);
    }
}
