<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr>
            <th width="4%">No</th><th>Nama</th><th>Prodi</th><th>Periode</th>
            <th class="text-center">Status</th><th class="text-center">Nilai</th>
            <th class="text-center">Orientasi</th><th class="text-center" width="24%">Unduh / Detail</th>
        </tr></thead>
        <tbody>
            @forelse($list as $i => $m)
            <tr>
                <td class="text-muted">{{ $i+1 }}</td>
                <td class="fw-semibold">{{ $m->nm_mahasiswa }}</td>
                <td class="small">{{ $m->prodi ?: '-' }}</td>
                <td class="small">{{ optional($m->tanggal_mulai)->format('d/m/y') }} - {{ optional($m->tanggal_berakhir)->format('d/m/y') }}</td>
                <td class="text-center"><span class="pill {{ $m->is_selesai ? 'st-selesai' : 'st-jalan' }}">{{ $m->is_selesai ? 'Selesai' : 'Berjalan' }}</span></td>
                <td class="text-center"><span class="nilai-badge" style="color:var(--maroon);">{{ $m->nilai_akhir ?: '-' }}</span></td>
                <td class="text-center">
                    @if($m->orientasi && $m->orientasi->status === 'lulus_orientasi')
                        <span class="pill st-selesai">Lulus</span>
                    @elseif($m->orientasi)
                        <span class="pill st-jalan">Proses</span>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                        <a href="{{ route('sertifikat.download', $m->share_token) }}" target="_blank" class="btn-sertif b-magang"><i class="bi bi-award"></i> Magang</a>
                        @if($m->orientasi && $m->orientasi->status === 'lulus_orientasi')
                            <a href="{{ route('instansi.sertifikat.orientasi', $m->id) }}" target="_blank" class="btn-sertif b-orient"><i class="bi bi-patch-check"></i> Orientasi</a>
                        @endif
                        <button class="btn-sertif b-detail" data-bs-toggle="collapse" data-bs-target="#det-{{ $m->id }}"><i class="bi bi-eye"></i> Nilai</button>
                    </div>
                </td>
            </tr>
            <tr class="collapse" id="det-{{ $m->id }}">
                <td colspan="8" class="bg-light">
                    <div class="row g-3 py-2">
                        <div class="col-md-6">
                            <div class="fw-bold small mb-2" style="color:var(--maroon);"><i class="bi bi-door-open me-1"></i>Nilai per Ruangan</div>
                            @php $nr = is_array($m->nilai_ruangan_json) ? $m->nilai_ruangan_json : []; @endphp
                            @if(count($nr))
                                @foreach($m->roomSequences->pluck('ruangan')->unique('id')->filter() as $rg)
                                    <div class="d-flex justify-content-between border-bottom py-1 small">
                                        <span>{{ $rg->nm_ruangan }}</span>
                                        <strong>{{ $nr[$rg->id] ?? '-' }}</strong>
                                    </div>
                                @endforeach
                            @else <span class="text-muted small">Belum ada nilai ruangan.</span> @endif
                        </div>
                        <div class="col-md-6">
                            <div class="fw-bold small mb-2" style="color:var(--maroon);"><i class="bi bi-chat-left-text me-1"></i>Catatan / Evaluasi</div>
                            @php
                                $ev = is_array($m->nilai_evaluasi) ? $m->nilai_evaluasi : [];
                                $flat = [];
                                $walk = function($arr, $prefix = '') use (&$walk, &$flat) {
                                    foreach ($arr as $k => $v) {
                                        $label = $prefix ? ($prefix . ' – ' . $k) : $k;
                                        if (is_array($v)) { $walk($v, ucwords(str_replace('_',' ',$k))); }
                                        else { $flat[ucwords(str_replace('_',' ', $label))] = $v; }
                                    }
                                };
                                $walk($ev);
                            @endphp
                            @if(count($flat))
                                @foreach($flat as $label => $val)
                                    <div class="d-flex justify-content-between border-bottom py-1 small">
                                        <span class="text-muted">{{ $label }}</span>
                                        <strong>{{ $val }}</strong>
                                    </div>
                                @endforeach
                            @else <span class="text-muted small">Belum ada catatan evaluasi.</span> @endif
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4 d-block mb-2"></i>Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
