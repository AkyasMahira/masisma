@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi Hari Ini')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-4">
                    <select name="ruangan_id" class="form-control">
                        <option value="">-- All Ruangan --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nm_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-control">
                        <option value="">-- All Types --</option>
                        <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Reset</a>
                    <button class="btn btn-success ms-2" onclick="exportAbsensi()">Export Excel</button>
                </div>
            </form>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>Nama</th>
                        <th>Ruangan</th>
                        <th>Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensis as $a)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $a->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $a->mahasiswa->nm_mahasiswa }}</td>
                            <td>{{ $a->mahasiswa->ruangan ? $a->mahasiswa->ruangan->nm_ruangan : $a->mahasiswa->nm_ruangan }}
                            </td>
                            <td>{{ $a->type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportAbsensi() {
            const table = document.querySelector('table');
            const rows = Array.from(table.querySelectorAll('thead tr, tbody tr'));
            const ws_data = [];

            // headers
            const headers = Array.from(table.querySelectorAll('thead th')).map(h => h.innerText.trim());
            // We will use: Waktu, Nama, Ruangan, Tipe
            ws_data.push(['Waktu', 'Nama', 'Ruangan', 'Tipe']);

            table.querySelectorAll('tbody tr').forEach(tr => {
                const cells = Array.from(tr.querySelectorAll('td'));
                if (cells.length >= 5) {
                    const waktu = cells[1].innerText.trim();
                    const nama = cells[2].innerText.trim();
                    const ruangan = cells[3].innerText.trim();
                    const tipe = cells[4].innerText.trim();
                    ws_data.push([waktu, nama, ruangan, tipe]);
                }
            });

            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Absensi');
            XLSX.writeFile(wb, `Absensi_${new Date().toISOString().split('T')[0]}.xlsx`);
        }
    </script>
@endsection
@endsection
