@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📅 Jadwal Penghuni (Aktual)</h1>
        <button class="btn btn-secondary" disabled>Diatur Otomatis oleh Sistem</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Status</th>
                        <th>Mahasiswa</th>
                        <th>Ruangan Huni</th>
                        <th>Periode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        @php
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $start = \Carbon\Carbon::parse($schedule->start_date)->startOfDay();
                            $end = \Carbon\Carbon::parse($schedule->end_date)->endOfDay();
                            $isActive = $today->between($start, $end);
                        @endphp

                    <tr class="{{ $isActive ? 'table-success' : '' }}">
                        <td class="text-center">
                            @if($isActive)
                                <span class="badge bg-success">AKTIF</span>
                            @elseif($today->gt($end))
                                <span class="badge bg-secondary">SELESAI</span>
                            @else
                                <span class="badge bg-warning text-dark">AKAN DATANG</span>
                            @endif
                        </td>
                        <td>
                            {{ $schedule->mahasiswa->name ?? $schedule->mahasiswa->nm_mahasiswa ?? '-' }}
                        </td>
                        <td>
                            {{ $schedule->ruangan->nama_ruangan ?? $schedule->ruangan->nm_ruangan ?? '-' }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }} 
                            s/d 
                            {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
                        </td>
                        <td>
                            <form action="{{ route('room_schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada data. Coba jalankan command robot!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection