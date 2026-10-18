<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pisahkan evaluasi menjadi 3 jenis: pelatihan, pendidikan (magang), penelitian.
 * - master_evaluasi: jenis + kegiatan_id (pertanyaan khusus per pelatihan; NULL = default/master)
 * - evaluasis: jenis + kegiatan_id / mou_id / pra_penelitian_id
 * Data lama dipetakan: tanggapan magang -> pendidikan, sisanya -> pelatihan.
 */
class PisahEvaluasiPerJenis extends Migration
{
    public function up()
    {
        Schema::table('master_evaluasi', function (Blueprint $t) {
            $t->string('jenis', 20)->default('pelatihan')->after('id')->index();
            $t->unsignedBigInteger('kegiatan_id')->nullable()->after('jenis')->index();
        });
        Schema::table('evaluasis', function (Blueprint $t) {
            $t->string('jenis', 20)->default('pelatihan')->after('id')->index();
            $t->unsignedBigInteger('kegiatan_id')->nullable()->after('jenis')->index();
            $t->unsignedBigInteger('mou_id')->nullable()->after('kegiatan_id')->index();
            $t->unsignedBigInteger('pra_penelitian_id')->nullable()->after('mou_id')->index();
        });

        $now = now();

        // Pertanyaan default (9 unsur standar Permenpan RB 14/2017) untuk pendidikan & penelitian
        $default = [
            'pendidikan' => [
                'Kesesuaian persyaratan praktik/magang dengan jenis layanan pendidikan',
                'Kemudahan prosedur pendaftaran dan administrasi praktik/magang',
                'Kecepatan waktu dalam memberikan pelayanan pendidikan',
                'Kewajaran biaya/tarif praktik/magang',
                'Kesesuaian kegiatan praktik dengan kompetensi yang dijanjikan',
                'Kompetensi/kemampuan pembimbing (CI) dalam membimbing',
                'Kesopanan dan keramahan petugas dan pembimbing',
                'Kualitas sarana dan prasarana praktik',
                'Penanganan pengaduan, saran, dan masukan',
            ],
            'penelitian' => [
                'Kesesuaian persyaratan izin penelitian dengan jenis layanannya',
                'Kemudahan prosedur pengajuan dan izin penelitian',
                'Kecepatan waktu proses izin dan pelaksanaan penelitian',
                'Kewajaran biaya/tarif layanan penelitian',
                'Kesesuaian layanan (data, akses, surat) dengan yang dijanjikan',
                'Kompetensi/kemampuan pembimbing dan penguji',
                'Kesopanan dan keramahan petugas',
                'Kualitas sarana dan prasarana penelitian',
                'Penanganan pengaduan, saran, dan masukan',
            ],
        ];
        $baru = []; // jenis => [kode => id]
        foreach ($default as $jenis => $list) {
            foreach ($list as $i => $p) {
                $id = DB::table('master_evaluasi')->insertGetId([
                    'jenis' => $jenis, 'kegiatan_id' => null, 'kode' => 'U' . ($i + 1), 'pertanyaan' => $p,
                    'tipe' => 'rating', 'urutan' => $i + 1, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
                $baru[$jenis]['U' . ($i + 1)] = $id;
            }
        }

        // Data lama: tanggapan magang -> pendidikan (+ mou_id dari mahasiswa)
        $pend = DB::table('evaluasis')->where(function ($w) {
            $w->whereNotNull('mahasiswa_id')->orWhere('nama_kegiatan', 'Magang/PKL');
        })->get(['id', 'mahasiswa_id']);
        $kodeLama = DB::table('master_evaluasi')->where('jenis', 'pelatihan')->pluck('kode', 'id')->all();
        foreach ($pend as $e) {
            $mou = $e->mahasiswa_id ? DB::table('mahasiswas')->where('id', $e->mahasiswa_id)->value('mou_id') : null;
            DB::table('evaluasis')->where('id', $e->id)->update(['jenis' => 'pendidikan', 'mou_id' => $mou]);
            // jawaban dipindah ke unsur pendidikan yang kodenya sama (U1..U9)
            foreach (DB::table('evaluasi_jawaban')->where('evaluasi_id', $e->id)->get(['id', 'master_evaluasi_id']) as $j) {
                $kode = $kodeLama[$j->master_evaluasi_id] ?? null;
                if ($kode && isset($baru['pendidikan'][$kode])) {
                    DB::table('evaluasi_jawaban')->where('id', $j->id)->update(['master_evaluasi_id' => $baru['pendidikan'][$kode]]);
                }
            }
        }

        // Data lama pelatihan: tautkan ke kegiatan bila nama kegiatannya persis sama
        $kegiatan = DB::table('kegiatan')->get(['id', 'nama_kegiatan']);
        $peta = [];
        foreach ($kegiatan as $k) $peta[mb_strtolower(trim(preg_replace('/\s+/', ' ', $k->nama_kegiatan)))] = $k->id;
        foreach (DB::table('evaluasis')->where('jenis', 'pelatihan')->whereNotNull('nama_kegiatan')->get(['id', 'nama_kegiatan']) as $e) {
            $key = mb_strtolower(trim(preg_replace('/\s+/', ' ', $e->nama_kegiatan)));
            if (isset($peta[$key])) DB::table('evaluasis')->where('id', $e->id)->update(['kegiatan_id' => $peta[$key]]);
        }
    }

    public function down()
    {
        // kembalikan jawaban pendidikan ke unsur pelatihan berkode sama, lalu hapus unsur baru
        $lama = DB::table('master_evaluasi')->where('jenis', 'pelatihan')->whereNull('kegiatan_id')->pluck('id', 'kode')->all();
        $baru = DB::table('master_evaluasi')->whereIn('jenis', ['pendidikan', 'penelitian'])->get(['id', 'kode']);
        foreach ($baru as $b) {
            if (isset($lama[$b->kode])) DB::table('evaluasi_jawaban')->where('master_evaluasi_id', $b->id)->update(['master_evaluasi_id' => $lama[$b->kode]]);
        }
        DB::table('master_evaluasi')->whereIn('jenis', ['pendidikan', 'penelitian'])->delete();
        DB::table('master_evaluasi')->whereNotNull('kegiatan_id')->delete();

        Schema::table('evaluasis', function (Blueprint $t) {
            $t->dropColumn(['jenis', 'kegiatan_id', 'mou_id', 'pra_penelitian_id']);
        });
        Schema::table('master_evaluasi', function (Blueprint $t) {
            $t->dropColumn(['jenis', 'kegiatan_id']);
        });
    }
}
