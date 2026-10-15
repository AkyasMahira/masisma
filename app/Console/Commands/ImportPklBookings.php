<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Mou;
use App\Models\Ruangan;
use App\Models\BookingRuangan;

class ImportPklBookings extends Command
{
    protected $signature = 'pkl:import {--fresh : Hapus hasil impor sebelumnya lalu impor ulang} {--undo : Hapus semua booking hasil impor}';
    protected $description = 'Impor data mapping PKL (bookings_import.csv + ruangan_master.csv) menjadi BookingRuangan (status approved).';

    const TAG = '[IMPOR-MAPPING]';

    public function handle()
    {
        $dir = database_path('data');

        if ($this->option('undo')) {
            $n = BookingRuangan::where('keterangan', 'like', '%' . self::TAG . '%')->delete();
            $this->info("Dihapus {$n} booking hasil impor.");
            return 0;
        }

        $sudah = BookingRuangan::where('keterangan', 'like', '%' . self::TAG . '%')->count();
        if ($sudah > 0) {
            if (!$this->option('fresh')) {
                $this->error("Sudah ada {$sudah} booking hasil impor. Pakai --fresh untuk impor ulang, atau --undo untuk hapus.");
                return 1;
            }
            $this->warn("Menghapus {$sudah} booking impor lama (--fresh)...");
            BookingRuangan::where('keterangan', 'like', '%' . self::TAG . '%')->delete();
        }

        // 1) Ruangan master
        $rmap = $this->importRuangan($dir . '/ruangan_master.csv');

        // 2) Bookings
        $this->importBookings($dir . '/bookings_import.csv', $rmap);

        $this->info('Selesai.');
        return 0;
    }

    private function normRuangan($n)
    {
        $n = strtoupper(trim((string) $n));
        $n = preg_replace('/\(.*?\)/', '', $n);       // buang ( ... )
        $n = preg_replace('/\s+/', ' ', $n);
        return trim($n);
    }

    /** Buat/cocokkan ruangan, kembalikan map norm->Ruangan. */
    private function importRuangan($path)
    {
        $existing = [];
        foreach (Ruangan::all() as $r) $existing[$this->normRuangan($r->nm_ruangan)] = $r;

        if (is_file($path)) {
            foreach ($this->readCsv($path) as $row) {
                $nama = trim($row['nm_ruangan'] ?? '');
                if ($nama === '') continue;
                $key = $this->normRuangan($nama);
                $kuota = (int) ($row['kapasitas'] ?? 0);
                if ($kuota <= 0) $kuota = 10;
                if (isset($existing[$key])) {
                    // update kuota bila kosong/0
                    $r = $existing[$key];
                    if ((int) $r->kuota_ruangan <= 0) { $r->kuota_ruangan = $kuota; $r->save(); }
                } else {
                    $existing[$key] = Ruangan::create([
                        'nm_ruangan'    => $nama,
                        'kuota_ruangan' => $kuota,
                        'kategori'      => 'non_shift',
                    ]);
                }
            }
        }
        $this->info('Ruangan tersedia: ' . count($existing));
        return $existing;
    }

    private function importBookings($path, $rmap)
    {
        if (!is_file($path)) { $this->error("File tidak ada: $path"); return; }

        $mouCache = [];
        $ok = 0; $skipRuangan = 0;

        DB::beginTransaction();
        try {
            foreach ($this->readCsv($path) as $row) {
                $ruanganRaw = trim($row['ruangan'] ?? '');
                $prodi = trim($row['prodi'] ?? '');
                $univ  = trim($row['univ'] ?? '');
                $komp  = trim($row['kompetensi'] ?? '');
                $jenj  = trim($row['jenjang'] ?? '');
                $jml   = max(1, (int) ($row['jumlah'] ?? 1));
                $mulai = trim($row['mulai'] ?? '');
                $selesai = trim($row['selesai'] ?? '');
                if ($ruanganRaw === '' || $mulai === '' || $selesai === '') continue;

                $rkey = $this->normRuangan($ruanganRaw);
                if (!isset($rmap[$rkey])) {
                    $rmap[$rkey] = Ruangan::create(['nm_ruangan' => $ruanganRaw, 'kuota_ruangan' => 10, 'kategori' => 'non_shift']);
                }
                $ruangan = $rmap[$rkey];

                // Nama instansi/Mou: utamakan univ, lalu prodi
                $namaInstansi = $univ !== '' ? $univ : $prodi;
                if ($namaInstansi === '') $namaInstansi = 'Instansi PKL';
                $mkey = mb_strtolower($namaInstansi);
                if (!isset($mouCache[$mkey])) {
                    $mouCache[$mkey] = Mou::firstOrCreate(
                        ['nama_universitas' => $namaInstansi],
                        [
                            'nama_instansi' => $namaInstansi,
                            'tanggal_masuk' => '2026-01-01',
                            'tanggal_keluar' => '2030-12-31',
                            'keterangan'    => 'Dibuat dari impor mapping PKL',
                        ]
                    );
                }
                $mou = $mouCache[$mkey];

                $ket = trim(($komp !== '' ? "Kompetensi: $komp. " : '') . self::TAG);

                BookingRuangan::create([
                    'mou_id'          => $mou->id,
                    'user_id'         => null,
                    'ruangan_id'      => $ruangan->id,
                    'jenjang'         => $jenj ?: null,
                    'prodi'           => $prodi ?: null,
                    'jumlah_peserta'  => $jml,
                    'tanggal_mulai'   => $mulai,
                    'tanggal_selesai' => $selesai,
                    'keterangan'      => $ket,
                    'status'          => 'approved',
                    'catatan_admin'   => 'Impor data mapping manual.',
                ]);
                $ok++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('GAGAL: ' . $e->getMessage());
            return;
        }

        $this->info("Booking diimpor: {$ok}");
    }

    private function readCsv($path)
    {
        $rows = [];
        if (($h = fopen($path, 'r')) !== false) {
            // lewati BOM
            $bom = fread($h, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($h);
            $header = fgetcsv($h);
            while (($d = fgetcsv($h)) !== false) {
                if (count($d) === 1 && trim($d[0]) === '') continue;
                $rows[] = array_combine($header, array_pad($d, count($header), null));
            }
            fclose($h);
        }
        return $rows;
    }
}
