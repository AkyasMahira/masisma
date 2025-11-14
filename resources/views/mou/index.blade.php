@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'List MOU')
@section('page-title', 'Data MOU') {{-- Sesuaikan dengan section Anda --}}

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <a href="{{ route('mou.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah MOU Baru
        </a>
    </div>
    <div class="card-body">

        {{-- Tampilkan pesan sukses jika ada --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabelMOU">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Universitas</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th>
                        <th>File MOU</th>
                        <th>Surat Keterangan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mous as $index => $mou)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $mou->nama_universitas }}</td>
                            <td>{{ $mou->tanggal_masuk->format('d M Y') }}</td>
                            <td>{{ $mou->tanggal_keluar->format('d M Y') }}</td>
                            <td>
                                {{-- Gunakan Storage::url() untuk mengambil link publik --}}
                                <a href="{{ Storage::url($mou->file_mou) }}" target="_blank">Lihat File</a>
                            </td>
                            <td>
                                <a href="{{ Storage::url($mou->surat_keterangan) }}" target="_blank">Lihat Surat</a>
                            </td>
                            <td>{{ $mou->keterangan ?? '-' }}</td>
                            <td>
                                {{-- Tambahkan tombol Edit & Hapus di sini nanti --}}
                                {{-- <a href="{{ route('mou.edit', $mou->id) }}" class="btn btn-sm btn-warning">Edit</a> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                Belum ada data MOU.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Opsional: Jika ingin pakai DataTables --}}
{{-- @section('scripts')
<script>
    // $(document).ready(function() {
    //     $('#tabelMOU').DataTable();
    // });
</script>
@endsection --}}
@endsection