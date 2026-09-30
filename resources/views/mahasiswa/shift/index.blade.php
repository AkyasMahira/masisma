@extends('layouts.app')

@section('title', 'Atur Jadwal')

@section('content')
<div class="container py-4">
    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Header Card --}}
        <div class="card-header bg-primary text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-calendar-week me-2"></i> Atur Jadwal </h5>
                    <p class="mb-0 opacity-75 small">
                        Ruangan: <strong>{{ $sequence->ruangan->nm_ruangan }}</strong> <br>
                        Periode: {{ \Carbon\Carbon::parse($sequence->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($sequence->end_date)->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            
            <div class="alert alert-warning border-0 d-flex align-items-start mb-4">
                <i class="bi bi-exclamation-circle-fill fs-4 me-3 mt-1"></i>
                <div>
                    <strong>Penting!</strong><br>
                    Anda wajib menentukan shift (Pagi/Siang/Malam/Libur) untuk setiap tanggal di bawah ini agar bisa melakukan absensi.
                    <br><small class="text-muted">*Pastikan sesuai dengan jadwal dari Kepala Ruangan.</small>
                </div>
            </div>

            <form action="{{ route('shift.store') }}" method="POST">
                @csrf
                <input type="hidden" name="ruangan_id" value="{{ $sequence->ruangan->id }}">

                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 40%;">Tanggal</th>
                                <th>Pilih Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($period as $date)
                                @php
                                    $dateStr = $date->toDateString();
                                    $val = $existingShifts[$dateStr] ?? ''; // Ambil nilai lama jika ada
                                    $isPast = $date->isPast() && !$date->isToday(); // Cek tanggal lewat
                                    
                                    // Warna baris: Kuning jika hari ini
                                    $rowClass = $date->isToday() ? 'table-warning' : '';
                                @endphp

                                <tr class="{{ $rowClass }}">
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $date->translatedFormat('d F Y') }}</div>
                                        <small class="text-muted">{{ $date->translatedFormat('l') }}</small>
                                        
                                        @if($date->isToday())
                                            <span class="badge bg-danger ms-2">HARI INI</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="shifts[{{ $dateStr }}]" class="form-select" required 
                                            {{-- Opsional: Disable jika tanggal sudah lewat agar tidak bisa curang ganti sejarah --}}
                                            {{-- {{ $isPast ? 'disabled' : '' }} --}} 
                                        >
                                            <option value="" disabled {{ $val == '' ? 'selected' : '' }}>-- Pilih Shift --</option>
                                            <option value="Pagi" {{ $val == 'Pagi' ? 'selected' : '' }}>Pagi (07:00 - 14:00)</option>
                                            <option value="Siang" {{ $val == 'Siang' ? 'selected' : '' }}>Siang (14:00 - 21:00)</option>
                                            
                                            {{-- Cek Nama Ruangan untuk label jam malam --}}
                                            @php
                                                $isMerak = \Illuminate\Support\Str::contains(strtolower($sequence->ruangan->nm_ruangan), 'merak');
                                                $jamMalam = $isMerak ? '20:00' : '21:00';
                                            @endphp
                                            <option value="Malam" {{ $val == 'Malam' ? 'selected' : '' }}>Malam ({{ $jamMalam }} - 07:00)</option>
                                            
                                            <option value="Libur" {{ $val == 'Libur' ? 'selected' : '' }}>Libur</option>
                                        </select>

                                        {{-- Jika disabled, butuh input hidden agar data tetap terkirim (opsional) --}}
                                        {{-- @if($isPast) <input type="hidden" name="shifts[{{ $dateStr }}]" value="{{ $val }}"> @endif --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow">
                        <i class="bi bi-save me-2"></i> Simpan Jadwal
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection