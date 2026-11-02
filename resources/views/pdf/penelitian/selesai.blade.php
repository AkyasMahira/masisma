@extends('pdf.penelitian._layout')

@section('isi')
    <p class="judul">SURAT KETERANGAN SELESAI</p>
    <p class="nomor-judul">Nomor: {{ $surat->nomor_surat }}</p>

    <p class="indent">Yang bertanda tangan di bawah ini, {{ $ttd['jabatan'] }}{{ stripos($ttd['jabatan'], 'RSUD') === false ? ' ' . $kop['singkat'] : ' Kabupaten Kediri' }}, dengan ini menerangkan bahwa:</p>

    @include('pdf.penelitian._data_peneliti', ['denganPembimbing' => true])

    @php
        $mulai = $pra->tanggal_mulai;
        $selesai = $presentasi && $presentasi->tanggal_presentasi ? \Carbon\Carbon::parse($presentasi->tanggal_presentasi) : $surat->tanggal_surat;
    @endphp

    <p class="indent">telah <strong>selesai melaksanakan {{ $kegiatan['nama'] }}</strong> di {{ $kop['singkat'] }}@if($ruangan), Ruang {{ $ruangan }}@endif,
        @if($mulai && $mulai->lte($selesai))
            terhitung sejak tanggal {{ $mulai->translatedFormat('d F Y') }} sampai dengan {{ $selesai->translatedFormat('d F Y') }}.
        @else
            pada tanggal {{ $selesai->translatedFormat('d F Y') }}.
        @endif
    </p>

    @if($pra->jenis_penelitian === 'Uji Validitas')
        <p class="indent">Kegiatan tersebut meliputi pengujian validitas dan reliabilitas instrumen penelitian kepada responden di lingkungan {{ $kop['singkat'] }}. Pengolahan dan interpretasi hasil uji sepenuhnya menjadi tanggung jawab peneliti, dan instrumen yang telah dinyatakan valid serta reliabel dapat digunakan untuk tahap penelitian selanjutnya.</p>
    @elseif($pra->jenis_penelitian === 'Data Awal')
        <p class="indent">Data awal yang diperoleh digunakan sebagai bahan studi pendahuluan dalam penyusunan proposal penelitian, dengan tetap menjaga kerahasiaan identitas dan data pasien.</p>
    @endif

    <p class="indent">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
@endsection
