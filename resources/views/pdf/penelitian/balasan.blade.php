@extends('pdf.penelitian._layout')

@section('isi')
    <table class="meta">
        <tr>
            <td>
                <table class="meta" style="margin:0">
                    <tr><td class="l">Nomor</td><td class="s">:</td><td>{{ $surat->nomor_surat }}</td></tr>
                    <tr><td class="l">Sifat</td><td class="s">:</td><td>Biasa</td></tr>
                    <tr><td class="l">Lampiran</td><td class="s">:</td><td>-</td></tr>
                    <tr><td class="l">Perihal</td><td class="s">:</td><td><strong>Balasan Permohonan Izin {{ $kegiatan['nama'] }}</strong></td></tr>
                </table>
            </td>
            <td class="tanggal-kanan">{{ $kop['kota'] }}, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div class="tujuan">
        <p>Kepada</p>
        <p>Yth. Pimpinan {{ $institusi }}</p>
        @if($mou && $mou->alamat_instansi)<p>{{ $mou->alamat_instansi }}</p>@endif
        <p>di –</p>
        <p style="padding-left:28px">Tempat</p>
    </div>

    <p class="indent">Menindaklanjuti surat permohonan dari {{ $institusi }} perihal izin <strong>{{ $kegiatan['singkat'] }}</strong> di {{ $kop['singkat'] }}, bersama ini kami sampaikan bahwa pada prinsipnya kami <strong>MENYETUJUI</strong> permohonan tersebut untuk mahasiswa berikut:</p>

    @include('pdf.penelitian._data_peneliti', ['denganPembimbing' => true])

    <table class="data" style="margin-top:-6px">
        <tr><td class="l">Jenis Kegiatan</td><td class="s">:</td><td class="v"><span class="badge-jenis">{{ $kegiatan['nama'] }}</span></td></tr>
        <tr><td class="l">Tempat</td><td class="s">:</td><td class="v">{{ $kop['singkat'] }}@if($ruangan) — Ruang {{ $ruangan }}@endif</td></tr>
        @if($pra->tanggal_mulai)
            <tr><td class="l">Rencana Mulai</td><td class="s">:</td><td class="v">{{ $pra->tanggal_mulai->translatedFormat('d F Y') }}</td></tr>
        @endif
    </table>

    <p>Dengan ketentuan sebagai berikut:</p>
    <ol class="ketentuan">
        <li>Menyelesaikan administrasi dan biaya {{ $kegiatan['singkat'] }} sesuai tagihan (invoice) yang diterbitkan Unit Diklat sebelum kegiatan dimulai.</li>
        <li>Mematuhi tata tertib, standar prosedur operasional, serta peraturan yang berlaku di {{ $kop['singkat'] }}.</li>
        <li>Menjaga kerahasiaan identitas dan data pasien; data hanya digunakan untuk kepentingan akademik sesuai judul di atas.</li>
        <li>Pelaksanaan kegiatan didampingi oleh pembimbing lapangan (Clinical Instructor) yang ditunjuk oleh Unit Diklat.</li>
        @if($pra->jenis_penelitian === 'Data Awal')
            <li>Data awal yang diperoleh hanya digunakan untuk penyusunan latar belakang dan proposal penelitian.</li>
        @elseif($pra->jenis_penelitian === 'Uji Validitas')
            <li>Instrumen (kuesioner/lembar observasi) yang diujikan telah disetujui dosen pembimbing, dan responden uji validitas tidak termasuk sampel penelitian utama.</li>
            <li>Menyampaikan rekapitulasi hasil uji validitas dan reliabilitas instrumen kepada Unit Diklat setelah kegiatan selesai.</li>
        @else
            <li>Melampirkan surat kelaikan etik (<em>ethical clearance</em>) yang masih berlaku sebelum pengambilan data dimulai.</li>
            <li>Mempresentasikan hasil penelitian dan menyerahkan laporan akhir (softcopy) kepada Unit Diklat.</li>
        @endif
        <li>Setelah kegiatan selesai, peneliti akan mendapatkan surat keterangan selesai dari Unit Diklat.</li>
    </ol>

    <p class="indent">Demikian surat balasan ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
@endsection

@section('tembusan')
    <p><u>Tembusan</u>:</p>
    <p>1. Yth. Koordinator Unit Diklat {{ $kop['singkat'] }}</p>
    <p>2. Yth. Mahasiswa yang bersangkutan</p>
    <p>3. Arsip</p>
@endsection
