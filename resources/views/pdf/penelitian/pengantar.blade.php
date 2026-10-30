@extends('pdf.penelitian._layout')

@section('isi')
    <table class="meta">
        <tr>
            <td>
                <table class="meta" style="margin:0">
                    <tr><td class="l">Nomor</td><td class="s">:</td><td>{{ $surat->nomor_surat }}</td></tr>
                    <tr><td class="l">Sifat</td><td class="s">:</td><td>Biasa</td></tr>
                    <tr><td class="l">Lampiran</td><td class="s">:</td><td>-</td></tr>
                    <tr><td class="l">Perihal</td><td class="s">:</td><td><strong>Pengantar {{ $kegiatan['nama'] }}</strong></td></tr>
                </table>
            </td>
            <td class="tanggal-kanan">{{ $kop['kota'] }}, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div class="tujuan">
        <p>Kepada</p>
        <p>Yth. Kepala Ruangan {{ $ruangan ?: '..........' }}</p>
        <p>{{ $kop['singkat'] }}</p>
        <p>di –</p>
        <p style="padding-left:28px">Tempat</p>
    </div>

    <p class="indent">Dengan hormat, bersama ini kami hadapkan mahasiswa yang telah memperoleh izin dan menyelesaikan administrasi untuk melaksanakan <strong>{{ $kegiatan['singkat'] }}</strong> di ruangan Saudara, dengan data sebagai berikut:</p>

    @include('pdf.penelitian._data_peneliti', ['denganPembimbing' => false])

    @php
        $ciList = collect([optional($pengajuan)->ci, optional($pengajuan)->ci2])->filter();
    @endphp
    <table class="data" style="margin-top:-6px">
        <tr><td class="l">Jenis Kegiatan</td><td class="s">:</td><td class="v"><span class="badge-jenis">{{ $kegiatan['nama'] }}</span></td></tr>
        @if($ciList->count())
            <tr><td class="l">Pembimbing Lapangan</td><td class="s">:</td><td class="v">
                @foreach($ciList as $i => $ci){{ $ciList->count() > 1 ? ($i + 1) . '. ' : '' }}{{ $ci->nama }}@if($ci->bidang) ({{ $ci->bidang }})@endif<br>@endforeach
            </td></tr>
        @endif
        @if($pra->tanggal_mulai)
            <tr><td class="l">Rencana Mulai</td><td class="s">:</td><td class="v">{{ $pra->tanggal_mulai->translatedFormat('d F Y') }}</td></tr>
        @endif
    </table>

    <p class="indent">Mohon kiranya Saudara berkenan memberikan bantuan dan kesempatan kepada mahasiswa tersebut untuk melaksanakan {{ $kegiatan['singkat'] }} sesuai judul di atas, dengan tetap memperhatikan kenyamanan pelayanan serta menjaga kerahasiaan identitas dan data pasien.</p>
    <p class="indent">Demikian surat pengantar ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
@endsection

@section('tembusan')
    <p><u>Tembusan</u>:</p>
    <p>1. Yth. Pembimbing Lapangan yang bersangkutan</p>
    <p>2. Arsip</p>
@endsection
