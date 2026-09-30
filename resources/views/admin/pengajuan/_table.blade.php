<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        {{-- Header Merah Maroon --}}
        <thead style="background-color: #7c1316; color: white;">
            <tr>
                <th class="text-center" width="5%">
                    <input type="checkbox" class="form-check-input" id="selectAll" style="cursor: pointer;">
                </th>
                <th class="text-center" width="5%">No</th>
                <th>Pemohon</th>
                <th>Asal Universitas</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th class="text-center">Status</th>
                <th class="text-center" width="18%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $p)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" value="{{ $p->id }}" class="form-check-input select-item" name="ids[]" style="cursor: pointer;">
                    </td>

                    {{-- No --}}
                    <td class="text-center text-muted fw-bold">
                        {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                    </td>

                    {{-- Pemohon --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">{{ $p->user->name ?? 'User Terhapus' }}</span>
                            <small class="text-muted" style="font-size: 0.8rem;">{{ $p->user->email ?? '-' }}</small>
                        </div>
                    </td>

                    {{-- Universitas (Prioritas: Mahasiswa -> User) --}}
                    <td>
                        <span class="text-dark small fw-semibold">
                            @php
                                $univ = '-';
                                // 1. Cek dari profil mahasiswa terlebih dahulu
                                if (!empty($p->user->mahasiswa->univ_asal)) {
                                    $univ = $p->user->mahasiswa->univ_asal;
                                } 
                                // 2. Jika kosong, fallback ke relasi MoU di tabel User
                                elseif (!empty($p->user->mou)) {
                                    $univ = $p->user->mou->nama_instansi ?? $p->user->mou->nama_universitas;
                                }
                            @endphp
                            {{ $univ }}
                        </span>
                    </td>

                    {{-- KOLOM KATEGORI --}}
                    <td>
                        @if($p->jenis == 'magang')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                Pendidikan
                            </span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                Penelitian
                            </span>
                        @endif
                    </td>

                    {{-- KOLOM JENIS (Warna Dibedakan & Teks Dirapikan) --}}
                    <td>
                        @if($p->jenis == 'magang')
                            @php
                                $tipe = $p->user->mahasiswa->tipe_mahasiswa ?? 'Magang';
                                // Jika pkl -> PKL, jika magang -> Magang
                                $teksTipe = (strtolower($tipe) == 'pkl') ? 'PKL' : ucfirst($tipe);
                            @endphp
                            <!-- Warna Hijau untuk Jenis Pendidikan/Magang -->
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                {{ $teksTipe }}
                            </span>
                        @else
                            @php
                                // Ambil data PraPenelitian terbaru milik user
                                $pra = \App\Models\PraPenelitian::where('user_id', $p->user_id)->latest()->first();
                                $jenisPra = $pra ? ucfirst($pra->jenis_penelitian) : 'Penelitian';
                            @endphp
                            <!-- Warna Kuning/Orange untuk Jenis Penelitian -->
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50">
                                {{ $jenisPra }}
                            </span>
                        @endif
                    </td>

                    {{-- Tanggal --}}
                    <td>
                        <small class="text-muted">
                            {{ $p->created_at->format('d M Y') }}
                        </small>
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                        @if ($p->status === 'pending')
                            <span class="badge bg-warning text-dark border border-warning">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </span>
                        @elseif ($p->status === 'approved')
                            <span class="badge bg-success text-white">
                                <i class="bi bi-check-circle-fill"></i> Disetujui
                            </span>
                        @elseif ($p->status === 'rejected')
                            <span class="badge bg-danger text-white">
                                <i class="bi bi-x-circle-fill"></i> Ditolak
                            </span>
                        @elseif ($p->status === 'canceled')
                            <span class="badge bg-secondary text-white">
                                <i class="bi bi-x-circle"></i> Dibatalkan
                            </span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                            
                            {{-- 1. GROUP PENDING (Approve & Reject) --}}
                            @if ($p->status === 'pending')
                                <form action="{{ route('admin.pengajuan.approve', $p->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success text-white" style="width:32px; height:32px; border-radius:8px;" title="Setujui" onclick="return confirm('Yakin ingin menyetujui?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.pengajuan.reject', $p->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger text-white" style="width:32px; height:32px; border-radius:8px;" title="Tolak" onclick="return confirm('Yakin ingin menolak?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>

                            {{-- 2. GROUP APPROVED (Detail) --}}
                            @elseif ($p->status === 'approved')
                                <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn btn-sm btn-primary text-white" style="width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @endif

                            {{-- Tombol Cancel Satuan --}}
                            @if($p->status !== 'canceled')
                            <form action="{{ route('admin.pengajuan.cancel', $p->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary" style="width:32px; height:32px; border-radius:8px;" title="Batalkan Pengajuan" onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                            </form>
                            @endif
                            
                            {{-- 3. GROUP DELETE (Selalu Muncul) --}}
                            <form action="{{ route('admin.pengajuan.destroy', $p->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary text-white" style="width:32px; height:32px; border-radius:8px;" title="Hapus Data" onclick="return confirm('Hapus data ini secara permanen?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center opacity-50">
                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                            <h5 class="text-muted fw-bold">Data tidak ditemukan</h5>
                            <small>Coba ubah filter pencarian Anda.</small>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
@if($data->hasPages())
    <div class="d-flex justify-content-end p-3 border-top">
        {{ $data->links() }}
    </div>
@endif