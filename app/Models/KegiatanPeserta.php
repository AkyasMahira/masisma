<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\NilaiTeori;
class KegiatanPeserta extends Model
{
    protected $table = 'kegiatan_peserta';
protected $fillable = [
    'kegiatan_id', 'nama_lengkap_gelar', 'profesi', 'instansi_id', 'ruangan_id',
    'email_pj', 'no_hp_pj', 'alamat_instansi', 'tempat_lahir', 'tanggal_lahir',
    'nik', 'jabatan', 'email_plataran_sehat', 'no_hp_peserta', 'pendidikan_terakhir',
    'status_pegawai', 'nip', 'pangkat_golongan', 'metode_pelatihan', 'ukuran_kaos',
    'punya_akun_lms', 'bukti_bayar', 'status_pendaftaran'
];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function instansi()
    {
        return $this->belongsTo(MasterInstansi::class, 'instansi_id');
    }
    public function nilaiTeori()
    {
        // 1 peserta hanya punya 1 baris nilai teori (unique di kegiatan_peserta_id)
        return $this->hasOne(NilaiTeori::class, 'kegiatan_peserta_id');
    }

    public function nilaiSkill()
    {
        return $this->hasMany(\App\Models\NilaiSkill::class, 'kegiatan_peserta_id');
    }

    // Relasi: tanda tangan digital fasilitator/instruktur untuk peserta ini
    public function ttdPenilaian()
    {
        return $this->hasMany(\App\Models\TtdPenilaian::class, 'kegiatan_peserta_id');
    }
    public function ruangan()
    {
        return $this->belongsTo(MasterRuangan::class, 'ruangan_id');
    }

    // SPASI SUDAH DIHILANGKAN DI SINI
    public function absensiAktual()
    {
        return $this->hasOne(AbsensiKegiatan::class, 'kegiatan_peserta_id');
    }
}