<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Absensi manual oleh admin: absenkan satu / banyak mahasiswa pada tanggal tertentu
 * dengan jam masuk & pulang yang bisa diatur (atau mengikuti jam shift).
 * Masuk dicatat dengan created_at = jam masuk (dibaca rekap per tanggal tap),
 * pulang dipasangkan lewat jam_masuk yang sama (shift malam aman).
 */
class AbsenManualController extends Controller
{
    /** Jadwal & jam shift mahasiswa pada tanggal tsb. */
    private function jadwal(Mahasiswa $m, Carbon $tgl)
    {
        $tanggal = $tgl->toDateString();
        $seq = $m->roomSequences->first(function ($s) use ($tanggal) { return $s->start_date <= $tanggal && $s->end_date >= $tanggal; });
        $ruangan = $seq ? $seq->ruangan : null;
        $shift = null;
        if ($ruangan && $ruangan->kategori === 'shift') {
            $shift = optional($m->shiftSchedules->first(function ($s) use ($tanggal) { return substr((string) $s->tanggal, 0, 10) === $tanggal; }))->shift_type;
        } elseif ($ruangan) {
            $dow = $tgl->dayOfWeekIso;
            $shift = $dow == 5 ? 'Jumat' : ($dow <= 4 ? 'Reguler' : 'Libur');
        }
        $jam = null;
        if ($ruangan && $shift && strtolower($shift) !== 'libur') {
            $t = app(AbsensiController::class)->getShiftTimes($shift, $tgl->copy(), $ruangan->id);
            if (!empty($t['start']) && !empty($t['end'])) $jam = ['start' => $t['start'], 'end' => $t['end']];
        }
        return ['ruangan' => $ruangan, 'shift' => $shift, 'jam' => $jam];
    }

    /** Absen masuk & pulang mahasiswa pada tanggal tsb (masuk = tanggal tap, pulang = pasangan sesi). */
    private function absenTanggal($mahasiswaId, $tanggal)
    {
        $masuk = Absensi::where('mahasiswa_id', $mahasiswaId)->where('type', 'masuk')->whereDate('created_at', $tanggal)->orderBy('created_at')->first();
        $keluar = $masuk ? Absensi::where('mahasiswa_id', $mahasiswaId)->where('type', 'keluar')->where('jam_masuk', $masuk->jam_masuk)->first() : null;
        return [$masuk, $keluar];
    }

    public function index(Request $request)
    {
        $tgl = $request->filled('tanggal') ? Carbon::parse($request->tanggal)->startOfDay() : today();
        $tanggal = $tgl->toDateString();
        $q = trim((string) $request->get('q', ''));
        $fRuangan = $request->get('ruangan');

        $mhs = Mahasiswa::with(['mou:id,nama_instansi,nama_universitas', 'roomSequences.ruangan', 'shiftSchedules' => function ($s) use ($tanggal) { $s->where('tanggal', $tanggal); }])
            ->where('status', 'aktif')
            ->whereHas('roomSequences', function ($s) use ($tanggal, $fRuangan) {
                $s->where('start_date', '<=', $tanggal)->where('end_date', '>=', $tanggal);
                if ($fRuangan) $s->where('ruangan_id', $fRuangan);
            })
            ->when($q !== '', function ($w) use ($q) { $w->where('nm_mahasiswa', 'like', "%{$q}%"); })
            ->orderBy('nm_mahasiswa')
            ->get();

        $rows = $mhs->map(function ($m) use ($tgl, $tanggal) {
            $j = $this->jadwal($m, $tgl);
            list($masuk, $keluar) = $this->absenTanggal($m->id, $tanggal);
            return [
                'm' => $m, 'ruangan' => optional($j['ruangan'])->nm_ruangan, 'shift' => $j['shift'],
                'jam_shift' => $j['jam'] ? $j['jam']['start']->format('H:i') . '–' . $j['jam']['end']->format('H:i') : null,
                'masuk' => $masuk ? Carbon::parse($masuk->jam_masuk) : null,
                'pulang' => $keluar ? Carbon::parse($keluar->jam_keluar) : null,
                'ket' => $masuk ? $masuk->keterangan : null,
            ];
        });

        $ruangans = Ruangan::orderBy('nm_ruangan')->pluck('nm_ruangan', 'id');
        return view('admin.absen_manual.index', compact('rows', 'tanggal', 'q', 'fRuangan', 'ruangans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'       => 'required|date',
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'integer|exists:mahasiswas,id',
            'mode_masuk'    => 'required|in:shift,jam',
            'jam_masuk'     => 'nullable|required_if:mode_masuk,jam|date_format:H:i',
            'mode_pulang'   => 'required|in:kosong,shift,jam',
            'jam_pulang'    => 'nullable|required_if:mode_pulang,jam|date_format:H:i',
            'alasan'        => 'nullable|string|max:200',
        ], [
            'mahasiswa_ids.required' => 'Centang minimal satu mahasiswa.',
            'jam_masuk.required_if'  => 'Isi jam masuk.',
            'jam_pulang.required_if' => 'Isi jam pulang.',
        ]);

        $tgl = Carbon::parse($data['tanggal'])->startOfDay();
        $tanggal = $tgl->toDateString();
        $alasan = trim((string) ($data['alasan'] ?? ''));
        $ok = []; $gagal = [];

        $mhs = Mahasiswa::with(['roomSequences.ruangan', 'shiftSchedules' => function ($s) use ($tanggal) { $s->where('tanggal', $tanggal); }])
            ->whereIn('id', $data['mahasiswa_ids'])->get();

        foreach ($mhs as $m) {
            $j = $this->jadwal($m, $tgl);

            // Jam masuk
            if ($data['mode_masuk'] === 'shift') {
                if (!$j['jam']) { $gagal[] = $m->nm_mahasiswa . ' (tidak ada jadwal shift / libur — isi jam manual)'; continue; }
                $jamMasuk = $j['jam']['start']->copy();
            } else {
                $jamMasuk = Carbon::parse($tanggal . ' ' . $data['jam_masuk']);
            }

            // Jam pulang
            $jamPulang = null;
            if ($data['mode_pulang'] === 'shift') {
                if (!$j['jam']) { $gagal[] = $m->nm_mahasiswa . ' (jam pulang shift tidak diketahui — isi jam manual)'; continue; }
                $jamPulang = $j['jam']['end']->copy();
                if ($jamPulang->lte($jamMasuk)) $jamPulang->addDay();
            } elseif ($data['mode_pulang'] === 'jam') {
                $jamPulang = Carbon::parse($tanggal . ' ' . $data['jam_pulang']);
                if ($jamPulang->lte($jamMasuk)) $jamPulang->addDay(); // shift malam: pulang keesokan hari
            }

            $label = ($j['shift'] && strtolower($j['shift']) !== 'libur' ? $j['shift'] : 'Manual') . ' (diinput admin' . ($alasan !== '' ? ': ' . $alasan : '') . ')';

            DB::transaction(function () use ($m, $tanggal, $jamMasuk, $jamPulang, $label) {
                list($masuk, $keluar) = $this->absenTanggal($m->id, $tanggal);
                $jamLama = $masuk ? $masuk->jam_masuk : null;
                if (!$masuk) $masuk = new Absensi(['mahasiswa_id' => $m->id]);
                $masuk->mahasiswa_id = $m->id;
                $masuk->type = 'masuk';
                $masuk->jam_masuk = $jamMasuk;
                $masuk->jam_keluar = null;
                $masuk->keterangan = $label;
                $masuk->created_at = $jamMasuk;
                $masuk->updated_at = now();
                $masuk->save();

                if ($keluar && $jamLama) {
                    // pasangan pulang lama ikut digeser ke jam masuk baru
                    $keluar->jam_masuk = $jamMasuk;
                }
                if ($jamPulang) {
                    if (!$keluar) $keluar = new Absensi(['mahasiswa_id' => $m->id]);
                    $keluar->mahasiswa_id = $m->id;
                    $keluar->type = 'keluar';
                    $keluar->jam_masuk = $jamMasuk;
                    $keluar->jam_keluar = $jamPulang;
                    $keluar->durasi_menit = (int) $jamMasuk->diffInMinutes($jamPulang, true);
                    $keluar->keterangan = $label;
                    $keluar->created_at = $jamPulang;
                    $keluar->updated_at = now();
                    $keluar->save();
                } elseif ($keluar) {
                    $keluar->durasi_menit = (int) $jamMasuk->diffInMinutes(Carbon::parse($keluar->jam_keluar), true);
                    $keluar->save();
                }
            });

            $ok[] = $m->nm_mahasiswa . ' (' . $jamMasuk->format('H:i') . ($jamPulang ? '–' . $jamPulang->format('H:i') . ($jamPulang->toDateString() !== $tanggal ? ' +1 hari' : '') : '') . ')';
        }

        $redir = redirect()->route('admin.absen_manual.index', array_filter(['tanggal' => $tanggal, 'ruangan' => $request->ruangan, 'q' => $request->q]));
        if ($ok) $redir->with('success', count($ok) . ' mahasiswa diabsenkan: ' . implode(', ', $ok) . '.');
        if ($gagal) $redir->with('error', 'Tidak diproses: ' . implode('; ', $gagal) . '.');
        return $redir;
    }

    /** Hapus absen masuk + pulang mahasiswa pada tanggal tsb. */
    public function destroy(Request $request, $mahasiswaId)
    {
        $tanggal = Carbon::parse($request->input('tanggal', today()))->toDateString();
        $m = Mahasiswa::findOrFail($mahasiswaId);
        list($masuk, $keluar) = $this->absenTanggal($m->id, $tanggal);
        if (!$masuk) return back()->with('error', 'Tidak ada absen ' . $m->nm_mahasiswa . ' pada tanggal itu.');
        DB::transaction(function () use ($masuk, $keluar) {
            if ($keluar) $keluar->delete();
            $masuk->delete();
        });
        return back()->with('success', 'Absen ' . $m->nm_mahasiswa . ' tanggal ' . Carbon::parse($tanggal)->isoFormat('D MMM YYYY') . ' dihapus.');
    }
}
