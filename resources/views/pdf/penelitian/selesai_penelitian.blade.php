@extends('pdf.penelitian._layout')

@section('isi')
    <style>
        .nilai { width: 100%; border-collapse: collapse; margin: 4px 0 10px; font-size: 10.5pt; }
        .nilai th { background: #7c1316; color: #fff; padding: 5px 6px; border: 1px solid #7c1316; font-weight: bold; }
        .nilai td { padding: 4px 6px; border: 1px solid #cbd5e1; }
        .nilai tr.rata td { background: #fdf6e3; font-weight: bold; }
        .nilai-akhir { width: 100%; border-collapse: collapse; margin: 2px 0 10px; }
        .nilai-akhir td { border: 1.5px solid #7c1316; padding: 6px 10px; text-align: center; }
        .nilai-akhir .lbl { font-size: 9pt; letter-spacing: 1px; text-transform: uppercase; color: #7c1316; font-weight: bold; }
        .nilai-akhir .huruf { font-size: 17pt; font-weight: bold; color: #7c1316; }
    </style>

    @php
        $details = $presentasi ? $presentasi->penilaianDetails : collect();
        $avgAngka = round($details->avg('skor_angka') ?? 0, 2);
        $hurufKolektif = \App\Models\Presentasi::getPredikat($avgAngka);
        $hurufAkhir = optional($presentasi)->nilai;
        $mulai = $pra->tanggal_mulai;
        $selesai = $presentasi && $presentasi->tanggal_presentasi ? \Carbon\Carbon::parse($presentasi->tanggal_presentasi) : $surat->tanggal_surat;
        $penerima = $namaPenerima ?: optional($pra->user)->name;
        $tim = collect([optional($pra->user)->name])->merge(collect($pra->anggotas ?? [])->pluck('nama'))
            ->filter()->unique(function ($n) { return mb_strtolower(trim($n)); })->values();
        $pembimbing = collect([$pra->dosen1_nama, $pra->dosen2_nama])->filter()->unique(function ($n) { return mb_strtolower(trim($n)); })->values();
    @endphp

    <p class="judul">SURAT KETERANGAN SELESAI PENELITIAN</p>
    <p class="nomor-judul">Nomor: {{ $surat->nomor_surat }}</p>

    <p class="indent">Yang bertanda tangan di bawah ini, {{ $ttd['jabatan'] }} {{ $kop['singkat'] }}, dengan ini menerangkan bahwa:</p>

    <table class="data">
        <tr><td class="l">Nama</td><td class="s">:</td><td class="v"><strong>{{ $penerima ?: '-' }}</strong></td></tr>
        @if($tim->count() > 1)
            <tr><td class="l">Tim Peneliti</td><td class="s">:</td><td class="v">{{ $tim->implode(', ') }}</td></tr>
        @endif
        <tr><td class="l">Program Studi</td><td class="s">:</td><td class="v">{{ $pra->prodi ?: (optional($pra->user)->program_studi ?: '-') }}</td></tr>
        <tr><td class="l">Institusi</td><td class="s">:</td><td class="v">{{ $institusi }}</td></tr>
        <tr><td class="l">Judul Penelitian</td><td class="s">:</td><td class="v"><em>"{{ $pra->judul ?: '-' }}"</em></td></tr>
        @if($pembimbing->count())
            <tr><td class="l">Dosen Pembimbing</td><td class="s">:</td><td class="v">{{ $pembimbing->implode('; ') }}</td></tr>
        @endif
    </table>

    <p class="indent">telah <strong>menyelesaikan seluruh rangkaian kegiatan penelitian</strong> di {{ $kop['singkat'] }}@if($ruangan), Ruang {{ $ruangan }}@endif,
        @if($mulai && $mulai->lte($selesai))
            terhitung sejak tanggal {{ $mulai->translatedFormat('d F Y') }} sampai dengan {{ $selesai->translatedFormat('d F Y') }},
        @else
            pada tanggal {{ $selesai->translatedFormat('d F Y') }},
        @endif
        termasuk presentasi hasil dan penyerahan laporan akhir, dengan rincian penilaian pembimbing lapangan sebagai berikut:</p>

    @if($details->count())
        <table class="nilai">
            <thead>
                <tr><th style="width:6%">No</th><th style="text-align:left">Pembimbing Lapangan (CI)</th><th style="width:16%">Skor</th><th style="width:12%">Huruf</th><th style="width:22%">Predikat</th></tr>
            </thead>
            <tbody>
                @foreach($details as $i => $d)
                    @php $h = \App\Models\Presentasi::getPredikat($d->skor_angka); @endphp
                    <tr>
                        <td align="center">{{ $i + 1 }}</td>
                        <td>{{ $d->nama_ci }}</td>
                        <td align="center">{{ number_format($d->skor_angka, 2) }}</td>
                        <td align="center">{{ $h }}</td>
                        <td align="center">{{ \App\Models\Presentasi::getKeterangan($h) }}</td>
                    </tr>
                @endforeach
                <tr class="rata">
                    <td colspan="2" align="right">Rata-rata</td>
                    <td align="center">{{ number_format($avgAngka, 2) }}</td>
                    <td align="center">{{ $hurufKolektif }}</td>
                    <td align="center">{{ \App\Models\Presentasi::getKeterangan($hurufKolektif) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if($hurufAkhir)
        <table class="nilai-akhir">
            <tr><td>
                <div class="lbl">Nilai Akhir Kelulusan</div>
                <div class="huruf">{{ $hurufAkhir }}</div>
                <div style="font-style:italic">({{ \App\Models\Presentasi::getKeterangan($hurufAkhir) }})</div>
            </td></tr>
        </table>
    @endif

    <p class="indent">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
@endsection
