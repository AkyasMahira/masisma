@extends('layouts.app')
@section('title', 'Setting Penilaian')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #fef1f2;
            --card-radius: 20px;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        .page-header { background: #fff; border-radius: var(--card-radius); padding: 25px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 25px; border-left: 5px solid var(--custom-maroon); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }

        .form-card { border: none; border-radius: var(--card-radius); box-shadow: 0 10px 40px rgba(0,0,0,0.03); background: #fff; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 25px; }
        .form-card-header { padding: 18px 25px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 10px; background: #f8fafc; }
        .form-card-body { padding: 25px; }

        .form-label-custom { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; }
        .form-control, .form-select { border-radius: 10px; padding: 12px 15px; border: 1px solid var(--border-color); font-size: 0.95rem; color: var(--text-dark); background: #f8fafc; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--custom-maroon); box-shadow: 0 0 0 4px var(--custom-maroon-light); background: #fff; }

        .btn-theme { background-color: var(--custom-maroon); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; transition: 0.3s; font-size: 0.9rem; box-shadow: 0 8px 15px rgba(124, 19, 22, 0.2); }
        .btn-theme:hover { background-color: #5a0d10; color: white; transform: translateY(-2px); box-shadow: 0 12px 20px rgba(124, 19, 22, 0.3); }
        .btn-outline-theme { background-color: #fff; color: var(--text-muted); border: 1px solid var(--border-color); padding: 12px 25px; border-radius: 10px; font-weight: 700; transition: 0.3s; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-outline-theme:hover { background-color: #f1f5f9; color: var(--text-dark); }
        .btn-danger-soft { background: #fee2e2; color: #dc2626; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; transition: 0.2s; }
        .btn-danger-soft:hover { background: #dc2626; color: #fff; }

        /* Daftar Fasilitator */
        .fasilitator-item { background: #f8fafc; border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .fasilitator-item a { color: var(--custom-maroon); font-weight: 600; font-size: 0.8rem; text-decoration: none; }
        .fasilitator-item a:hover { text-decoration: underline; }
        
        .btn-remove-row { background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.2s; flex-shrink: 0; }
        .btn-remove-row:hover { background: #dc2626; color: white; }
        
        .btn-edit-row { background: #fef08a; color: #854d0e; border: none; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.2s; flex-shrink: 0; }
        .btn-edit-row:hover { background: #eab308; color: white; }

        /* Table item checklist */
        .table-custom thead th { background-color: #f8fafc; color: var(--text-muted); font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 14px; border-bottom: 2px solid var(--border-color); border-top: none; white-space: nowrap; }
        .table-custom tbody td { padding: 14px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; font-weight: 600; color: var(--text-dark); }

        .badge-jenis { background: var(--custom-maroon-light); color: var(--custom-maroon); font-weight: 700; font-size: 0.7rem; padding: 5px 12px; border-radius: 50px; white-space: nowrap; }

        /* Accordion Materi */
        .materi-item { border: 1px solid var(--border-color) !important; border-radius: 14px !important; margin-bottom: 12px; overflow: hidden; }
        .materi-item .accordion-button { font-weight: 800; color: var(--text-dark); background: #f8fafc; gap: 10px; box-shadow: none; }
        .materi-item .accordion-button:not(.collapsed) { background: var(--custom-maroon-light); color: var(--custom-maroon); }
        .materi-item .accordion-button:focus { box-shadow: 0 0 0 4px var(--custom-maroon-light); }
        .materi-no { width: 28px; height: 28px; border-radius: 8px; background: var(--custom-maroon); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; }
        .materi-section-title { font-size: 0.8rem; font-weight: 800; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; margin: 24px 0 12px; padding-top: 20px; border-top: 1px dashed var(--border-color); }
    </style>

    <div class="row">
        <div class="col-12">

            <div class="page-header">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-gear-fill me-2 text-danger"></i> Setting Penilaian</h3>
                    <p class="mb-0 opacity-75 small fw-bold text-muted">{{ $kegiatan->nama_kegiatan }}</p>
                </div>
                <a href="{{ route('admin.kegiatan.index') }}" class="btn-outline-theme"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 mb-4 rounded-3"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
            @endif
            
            {{-- Peringatan Umum kalau ada error --}}
            @if($errors->any())
                <div class="alert alert-danger shadow-sm border-0 mb-4 rounded-3">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada inputan Anda:</div>
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <!-- Pengaturan Dasar & Fasilitator -->
                <div class="col-lg-3">
                    <div class="form-card">
                        <div class="form-card-header"><i class="bi bi-sliders text-danger"></i> Format Penilaian</div>
                        <div class="form-card-body">
                            <form action="{{ route('admin.kegiatan.penilaian.setting.store', $kegiatan->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label-custom">Jenis Penilaian</label>
                                    <select name="jenis_penilaian" class="form-select @error('jenis_penilaian') is-invalid @enderror">
                                        <option value="centang" {{ old('jenis_penilaian', $setting->jenis_penilaian) == 'centang' ? 'selected' : '' }}>Centang (Kompeten / Tidak)</option>
                                        <option value="skor" {{ old('jenis_penilaian', $setting->jenis_penilaian) == 'skor' ? 'selected' : '' }}>Skor Angka (0, 1, 2)</option>
                                    </select>
                                    @error('jenis_penilaian')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-custom">Batas Lulus Rata-rata (%)</label>
                                    <input type="number" name="batas_lulus_persen" class="form-control @error('batas_lulus_persen') is-invalid @enderror" value="{{ old('batas_lulus_persen', $setting->batas_lulus_persen) }}" min="1" max="100">
                                    @error('batas_lulus_persen')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn-theme w-100"><i class="bi bi-check-lg me-1"></i> Simpan Format</button>
                            </form>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card-header"><i class="bi bi-person-badge-fill text-danger"></i> Fasilitator / Penilai</div>
                        <div class="form-card-body">
                            <form action="{{ route('admin.kegiatan.penilaian.fasilitator.store', $kegiatan->id) }}" method="POST" class="mb-3">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="nama_fasilitator" class="form-control @error('nama_fasilitator') is-invalid @enderror" placeholder="Nama Fasilitator" value="{{ old('nama_fasilitator') }}" required>
                                    <button class="btn-theme" style="border-radius: 0 10px 10px 0;" type="submit"><i class="bi bi-plus-lg"></i></button>
                                </div>
                                @error('nama_fasilitator')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </form>

                            @forelse($fasilitatorList as $fas)
                                <div class="fasilitator-item">
                                    <div>
                                        <strong class="d-block" style="color: var(--text-dark);">{{ $fas->nama_fasilitator }}</strong>
                                        <span class="badge-jenis d-inline-block my-1">{{ $fas->materi->count() }} materi dinilai</span><br>
                                        <a href="{{ route('fasilitator.penilaian.index', $fas->token_fasilitator) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Link Penilaian</a>
                                    </div>
                                    <form action="{{ route('admin.kegiatan.penilaian.fasilitator.destroy', [$kegiatan->id, $fas->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-remove-row" onclick="return confirm('Hapus fasilitator ini?')"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-muted text-center small mb-0 py-3">Belum ada fasilitator</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Materi + Checklist -->
                <div class="col-lg-9">
                    <div class="form-card">
                        <div class="form-card-header"><i class="bi bi-collection-fill text-danger"></i> Materi Penilaian & Sertifikat</div>
                        <div class="form-card-body">

                            <form action="{{ route('admin.kegiatan.penilaian.materi.store', $kegiatan->id) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" name="nama_materi" class="form-control @error('nama_materi') is-invalid @enderror" placeholder="Nama Materi" value="{{ old('nama_materi') }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="butuh_penilaian" class="form-select" required>
                                            <option value="1">Di Nilai (Fasil)</option>
                                            <option value="0">Tdk Dinilai</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="tampil_di_sertifikat" class="form-select" required>
                                            <option value="1">Msk Sertifikat</option>
                                            <option value="0">Tdk Sertifikat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="nilai_teori" class="form-control" placeholder="JP Teori" value="{{ old('nilai_teori') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" name="nilai_praktik" class="form-control" placeholder="JP Prak" value="{{ old('nilai_praktik') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn-theme w-100"><i class="bi bi-plus-lg"></i> Tambah</button>
                                    </div>
                                </div>
                                @error('nama_materi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </form>

                            <div class="accordion" id="accMateri">
                                @forelse($materiList as $mi => $materi)
                                    @php $terbuka = (int) session('open_materi') === $materi->id; @endphp
                                    <div class="accordion-item materi-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $terbuka ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#materi-{{ $materi->id }}">
                                                <span class="materi-no">{{ $mi + 1 }}</span>
                                                <span class="flex-grow-1">
                                                    {{ $materi->nama_materi }}
                                                    @if(!$materi->butuh_penilaian) <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">TDK DINILAI</span> @endif
                                                    @if(!$materi->tampil_di_sertifikat) <span class="badge bg-danger ms-1" style="font-size:0.65rem;">TDK SERTIFIKAT</span> @endif
                                                </span>
                                                
                                                @if($materi->butuh_penilaian)
                                                    <span class="badge-jenis">{{ $materi->items->count() }} item</span>
                                                    <span class="badge-jenis me-2">{{ $materi->fasilitator->count() }} fasilitator</span>
                                                @endif
                                            </button>
                                        </h2>
                                        <div id="materi-{{ $materi->id }}" class="accordion-collapse collapse {{ $terbuka ? 'show' : '' }}" data-bs-parent="#accMateri">
                                            <div class="accordion-body">

                                                {{-- Edit Nama materi, status penilaian, status sertifikat, nilai --}}
                                                <form id="form-materi-{{ $materi->id }}" action="{{ route('admin.kegiatan.penilaian.materi.update', [$kegiatan->id, $materi->id]) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-md-3">
                                                            <label class="form-label-custom">Nama Materi</label>
                                                            <input type="text" name="nama_materi" class="form-control @error('nama_materi') is-invalid @enderror" value="{{ old('nama_materi', $materi->nama_materi) }}" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label-custom">Dinilai (Fasil)</label>
                                                            <select name="butuh_penilaian" class="form-select" required>
                                                                <option value="1" {{ $materi->butuh_penilaian == 1 ? 'selected' : '' }}>Ya</option>
                                                                <option value="0" {{ $materi->butuh_penilaian == 0 ? 'selected' : '' }}>Tidak</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label-custom">Tampil di Sertifikat</label>
                                                            <select name="tampil_di_sertifikat" class="form-select" required>
                                                                <option value="1" {{ $materi->tampil_di_sertifikat == 1 ? 'selected' : '' }}>Ya, Tampilkan</option>
                                                                <option value="0" {{ $materi->tampil_di_sertifikat == 0 ? 'selected' : '' }}>Tidak Ditampilkan</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label-custom">JP Teori</label>
                                                            <input type="number" name="nilai_teori" class="form-control" value="{{ old('nilai_teori', $materi->nilai_teori) }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label-custom">JP Praktik</label>
                                                            <input type="number" name="nilai_praktik" class="form-control" value="{{ old('nilai_praktik', $materi->nilai_praktik) }}">
                                                        </div>
                                                    </div>
                                                    @error('nama_materi')
                                                        <div class="text-danger small mb-3">{{ $message }}</div>
                                                    @enderror

                                                    @if($materi->butuh_penilaian)
                                                        <label class="form-label-custom mt-3">Fasilitator Pengampu</label>
                                                        @forelse($fasilitatorList as $fas)
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox" name="fasilitator_ids[]" value="{{ $fas->id }}" id="fas-{{ $materi->id }}-{{ $fas->id }}" {{ (is_array(old('fasilitator_ids')) && in_array($fas->id, old('fasilitator_ids'))) || (!old('fasilitator_ids') && $materi->fasilitator->contains('id', $fas->id)) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="fas-{{ $materi->id }}-{{ $fas->id }}">{{ $fas->nama_fasilitator }}</label>
                                                            </div>
                                                        @empty
                                                            <span class="text-muted small">Tambahkan fasilitator dulu di panel sebelah kiri.</span>
                                                        @endforelse
                                                    @else
                                                        <div class="alert alert-secondary py-2 border-0 small mt-2">
                                                            <i class="bi bi-info-circle me-1"></i> Materi ini disetting <b>Tidak Dinilai</b>, sehingga fasilitator tidak perlu dipilih dan form checklist ditiadakan.
                                                        </div>
                                                    @endif
                                                </form>

                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <button type="submit" form="form-materi-{{ $materi->id }}" class="btn-theme"><i class="bi bi-check-lg me-1"></i> Simpan Materi</button>
                                                    <form action="{{ route('admin.kegiatan.penilaian.materi.destroy', [$kegiatan->id, $materi->id]) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn-danger-soft" onclick="return confirm('Hapus materi ini beserta seluruh datanya? Tindakan ini tidak bisa dibatalkan.')"><i class="bi bi-trash3-fill me-1"></i> Hapus Materi</button>
                                                    </form>
                                                </div>

                                                {{-- Checklist item materi (Sembunyikan jika tidak butuh penilaian) --}}
                                                @if($materi->butuh_penilaian)
                                                    <div class="materi-section-title"><i class="bi bi-list-check me-1"></i> Item Checklist / Tindakan yang Dinilai</div>

                                                    <form action="{{ route('admin.kegiatan.penilaian.item.store', [$kegiatan->id, $materi->id]) }}" method="POST" class="mb-4">
                                                        @csrf
                                                        <div class="row g-2">
                                                            <div class="col-md-2">
                                                                <input type="number" name="urutan" class="form-control" placeholder="Urutan (ops)" value="{{ old('urutan') }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" name="kategori" class="form-control" placeholder="Kategori (Ops)" value="{{ old('kategori') }}">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" name="aspek_tindakan" class="form-control" placeholder="Aspek Tindakan / Observasi" value="{{ old('aspek_tindakan') }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="submit" class="btn-theme w-100"><i class="bi bi-plus-lg"></i> Tambah</button>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <div class="table-responsive">
                                                        <table class="table table-custom mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th width="10%">Urutan</th>
                                                                    <th width="20%">Kategori</th>
                                                                    <th>Aspek Tindakan</th>
                                                                    <th width="15%" class="text-center">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($materi->items->sortBy('urutan') as $item)
                                                                <tr>
                                                                    <td class="fw-bold">{{ $item->urutan }}</td>
                                                                    <td>@if($item->kategori)<span class="badge-jenis">{{ $item->kategori }}</span>@else <span class="text-muted">-</span> @endif</td>
                                                                    <td>{{ $item->aspek_tindakan }}</td>
                                                                    <td>
                                                                        <div class="d-flex justify-content-center gap-1">
                                                                            <!-- Tombol Edit Modal -->
                                                                            <button type="button" class="btn-edit-row" data-bs-toggle="modal" data-bs-target="#editItem{{ $item->id }}">
                                                                                <i class="bi bi-pencil-fill" style="font-size: 0.8rem;"></i>
                                                                            </button>
                                                                            
                                                                            <form action="{{ route('admin.kegiatan.penilaian.item.destroy', [$kegiatan->id, $item->id]) }}" method="POST">
                                                                                @csrf @method('DELETE')
                                                                                <button type="submit" class="btn-remove-row" onclick="return confirm('Hapus item ini?')"><i class="bi bi-trash3-fill" style="font-size: 0.8rem;"></i></button>
                                                                            </form>
                                                                        </div>

                                                                        <!-- Modal Edit Item -->
                                                                        <div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered">
                                                                                <form action="{{ route('admin.kegiatan.penilaian.item.update', [$kegiatan->id, $item->id]) }}" method="POST" class="modal-content">
                                                                                    @csrf @method('PUT')
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title fs-6 fw-bold">Edit Item Checklist</h5>
                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                    </div>
                                                                                    <div class="modal-body text-start">
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label-custom">Urutan</label>
                                                                                            <input type="number" name="urutan" class="form-control" value="{{ $item->urutan }}" required>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label-custom">Kategori (Opsional)</label>
                                                                                            <input type="text" name="kategori" class="form-control" value="{{ $item->kategori }}">
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label-custom">Aspek Tindakan</label>
                                                                                            <textarea name="aspek_tindakan" class="form-control" rows="3" required>{{ $item->aspek_tindakan }}</textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                                                        <button type="submit" class="btn-theme"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada item penilaian untuk materi ini.</td></tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-collection fs-1 d-block mb-2 opacity-50"></i>
                                        Belum ada materi. Tambahkan materi pertama di atas.
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection