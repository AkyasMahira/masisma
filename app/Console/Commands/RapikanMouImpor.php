<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Mou;
use App\Support\MouMatcher;

/**
 * Rapikan MOU duplikat yang terbentuk dari impor mapping PKL:
 * booking hasil impor dipindah ke MOU asli yang namanya cocok, lalu MOU impor
 * yang sudah tidak dipakai dihapus. Default: hanya tampilkan rencana (dry-run).
 */
class RapikanMouImpor extends Command
{
    protected $signature = 'pkl:rapikan-mou
        {--apply : Jalankan perubahan (tanpa ini hanya menampilkan rencana)}
        {--min=0.88 : Skor minimal untuk dicocokkan otomatis (0..1)}
        {--map=* : Pemetaan manual ID_MOU_IMPOR:ID_MOU_TUJUAN, boleh berulang}
        {--skip=* : ID MOU impor yang dibiarkan apa adanya, boleh berulang}';

    protected $description = 'Gabungkan MOU hasil impor mapping PKL ke MOU yang sudah ada';

    const KET_IMPOR = 'Dibuat dari impor mapping PKL';
    const TABEL_REF = ['booking_ruangans', 'mahasiswas', 'users', 'pra_penelitians', 'surat_balasan'];

    public function handle()
    {
        $min = (float) $this->option('min');
        $manual = [];
        foreach ((array) $this->option('map') as $m) {
            if (preg_match('/^(\d+):(\d+)$/', trim($m), $x)) $manual[(int) $x[1]] = (int) $x[2];
        }

        $skip = array_map('intval', (array) $this->option('skip'));
        $impor = Mou::where('keterangan', self::KET_IMPOR)->whereNotIn('id', $skip ?: [0])->get();
        if ($skip) $this->line('Dilewati: #' . implode(', #', $skip));
        $asli  = Mou::where(function ($q) { $q->whereNull('keterangan')->orWhere('keterangan', '!=', self::KET_IMPOR); })->get();
        $namaAsli = $asli->mapWithKeys(function ($m) { return [$m->id => ($m->nama_instansi ?: $m->nama_universitas)]; })->all();

        if ($impor->isEmpty()) { $this->info('Tidak ada MOU hasil impor. Sudah rapi.'); return 0; }

        // 1) Cocokkan tiap MOU impor ke MOU asli
        $rencana = [];   // id_impor => [id_tujuan, skor, sumber]
        $sisa = [];      // MOU impor tanpa pasangan asli
        foreach ($impor as $m) {
            $nama = $m->nama_universitas;
            if (isset($manual[$m->id])) { $rencana[$m->id] = [$manual[$m->id], 1.0, 'manual']; continue; }
            list($id, $sc) = MouMatcher::best($nama, $namaAsli);
            if ($id && $sc >= $min) $rencana[$m->id] = [$id, $sc, 'otomatis'];
            else $sisa[$m->id] = [$id, $sc];
        }

        // 2) MOU impor tanpa pasangan: gabungkan sesama impor yang namanya mirip (ke yang bookingnya terbanyak)
        $jumlahBooking = DB::table('booking_ruangans')->whereIn('mou_id', $impor->pluck('id'))
            ->select('mou_id', DB::raw('count(*) as n'))->groupBy('mou_id')->pluck('n', 'mou_id')->all();
        $namaImpor = $impor->pluck('nama_universitas', 'id')->all();
        $kepala = []; // id_impor yang dipertahankan sebagai MOU (tidak ada MOU asli)
        $idsSisa = array_keys($sisa);
        usort($idsSisa, function ($a, $b) use ($jumlahBooking) { return ($jumlahBooking[$b] ?? 0) - ($jumlahBooking[$a] ?? 0); });
        foreach ($idsSisa as $id) {
            $calon = array_intersect_key($namaImpor, array_flip($kepala));
            list($k, $sc) = $calon ? MouMatcher::best($namaImpor[$id], $calon) : [null, 0];
            if ($k && $sc >= $min) $rencana[$id] = [$k, $sc, 'gabung-impor'];
            else $kepala[] = $id;
        }

        // 3) Tampilkan rencana
        $rows = [];
        foreach ($impor->sortBy('nama_universitas') as $m) {
            $nb = $jumlahBooking[$m->id] ?? 0;
            if (isset($rencana[$m->id])) {
                list($tujuan, $sc, $src) = $rencana[$m->id];
                $namaTujuan = $namaAsli[$tujuan] ?? (($namaImpor[$tujuan] ?? '?') . ' [impor]');
                $rows[] = ["#{$m->id} {$m->nama_universitas}", $nb, "#{$tujuan} {$namaTujuan}", $sc, $src];
            } else {
                list($cid, $csc) = $sisa[$m->id] ?? [null, 0];
                $saran = $cid ? "saran: #{$cid} " . ($namaAsli[$cid] ?? '') . " ({$csc})" : '-';
                $rows[] = ["#{$m->id} {$m->nama_universitas}", $nb, 'TETAP (tidak ada MOU asli) — ' . $saran, '', 'cek'];
            }
        }
        $this->table(['MOU impor', 'Booking', 'Pindah ke', 'Skor', 'Cara'], $rows);
        $this->line(sprintf('Total MOU impor: %d · akan digabung: %d · tetap: %d', $impor->count(), count($rencana), count($kepala)));

        if (!$this->option('apply')) {
            $this->warn('Belum ada perubahan (mode rencana). Jalankan ulang dengan --apply untuk menerapkan.');
            $this->line('Pemetaan manual: --map=ID_IMPOR:ID_TUJUAN (bisa berulang). Ubah ambang: --min=0.85');
            return 0;
        }

        // 4) Terapkan
        $tabel = array_values(array_filter(self::TABEL_REF, function ($t) { return Schema::hasTable($t) && Schema::hasColumn($t, 'mou_id'); }));
        $dipindah = 0; $dihapus = 0;
        DB::transaction(function () use ($rencana, $tabel, &$dipindah, &$dihapus) {
            // ikuti rantai (impor -> impor -> asli)
            $akhir = function ($id) use ($rencana) {
                $guard = 0;
                while (isset($rencana[$id]) && $guard++ < 10) $id = $rencana[$id][0];
                return $id;
            };
            foreach ($rencana as $dari => $r) {
                $ke = $akhir($dari);
                if ($ke === $dari) continue;
                foreach ($tabel as $t) {
                    $dipindah += DB::table($t)->where('mou_id', $dari)->update(['mou_id' => $ke]);
                }
                $masihDipakai = false;
                foreach ($tabel as $t) { if (DB::table($t)->where('mou_id', $dari)->exists()) { $masihDipakai = true; break; } }
                if (!$masihDipakai) { Mou::where('id', $dari)->delete(); $dihapus++; }
            }
        });

        $this->info("Selesai. Baris dipindah: {$dipindah} · MOU duplikat dihapus: {$dihapus}");
        return 0;
    }
}
