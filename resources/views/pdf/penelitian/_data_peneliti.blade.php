{{-- Tabel identitas peneliti (dipakai semua surat penelitian) --}}
<table class="data">
    <tr><td class="l">Nama</td><td class="s">:</td><td class="v"><strong>{{ optional($pra->user)->name ?? '-' }}</strong></td></tr>
    @php
        // Anggota selain pemohon (pemohon kadang ikut tercatat sebagai anggota)
        $namaPemohon = mb_strtolower(trim(optional($pra->user)->name ?? ''));
        $anggotaLain = collect($pra->anggotas ?? [])->filter(function ($a) use ($namaPemohon) { return mb_strtolower(trim($a->nama)) !== $namaPemohon; })->values();
        $pembimbing = collect([$pra->dosen1_nama, $pra->dosen2_nama])->filter()->unique(function ($n) { return mb_strtolower(trim($n)); })->values();
    @endphp
    @if($anggotaLain->count())
        <tr><td class="l">Anggota</td><td class="s">:</td><td class="v">
            @foreach($anggotaLain as $i => $a){{ $anggotaLain->count() > 1 ? ($i + 1) . '. ' : '' }}{{ $a->nama }}@if($a->jenjang) ({{ $a->jenjang }})@endif<br>@endforeach
        </td></tr>
    @endif
    <tr><td class="l">Program Studi</td><td class="s">:</td><td class="v">{{ $pra->prodi ?: (optional($pra->user)->program_studi ?: '-') }}</td></tr>
    <tr><td class="l">Institusi</td><td class="s">:</td><td class="v">{{ $institusi }}</td></tr>
    <tr><td class="l">Judul</td><td class="s">:</td><td class="v"><em>"{{ $pra->judul ?: '-' }}"</em></td></tr>
    @if(!empty($denganPembimbing) && $pembimbing->count())
        <tr><td class="l">Dosen Pembimbing</td><td class="s">:</td><td class="v">
            @foreach($pembimbing as $i => $nmPemb){{ $pembimbing->count() > 1 ? ($i + 1) . '. ' : '' }}{{ $nmPemb }}<br>@endforeach
        </td></tr>
    @endif
</table>
