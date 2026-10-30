{{-- Tabel identitas peneliti (dipakai semua surat penelitian) --}}
<table class="data">
    <tr><td class="l">Nama</td><td class="s">:</td><td class="v"><strong>{{ optional($pra->user)->name ?? '-' }}</strong></td></tr>
    @if($pra->anggotas && $pra->anggotas->count())
        <tr><td class="l">Anggota</td><td class="s">:</td><td class="v">
            @foreach($pra->anggotas as $i => $a){{ $i + 1 }}. {{ $a->nama }}@if($a->jenjang) ({{ $a->jenjang }})@endif<br>@endforeach
        </td></tr>
    @endif
    <tr><td class="l">Program Studi</td><td class="s">:</td><td class="v">{{ $pra->prodi ?: (optional($pra->user)->program_studi ?: '-') }}</td></tr>
    <tr><td class="l">Institusi</td><td class="s">:</td><td class="v">{{ $institusi }}</td></tr>
    <tr><td class="l">Judul</td><td class="s">:</td><td class="v"><em>"{{ $pra->judul ?: '-' }}"</em></td></tr>
    @if(!empty($denganPembimbing) && ($pra->dosen1_nama || $pra->dosen2_nama))
        <tr><td class="l">Dosen Pembimbing</td><td class="s">:</td><td class="v">
            @if($pra->dosen1_nama)1. {{ $pra->dosen1_nama }}<br>@endif
            @if($pra->dosen2_nama)2. {{ $pra->dosen2_nama }}@endif
        </td></tr>
    @endif
</table>
