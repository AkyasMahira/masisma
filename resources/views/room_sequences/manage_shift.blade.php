@extends('layouts.app')

@section('title', $isLocked ? 'Detail Shift' : 'Atur Shift Harian')

@section('content')
<style>
    .form-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff; overflow: hidden; }
    .card-header-custom {
        background: {{ $isLocked ? 'linear-gradient(135deg,#0d6efd,#0a58ca)' : 'linear-gradient(135deg,#7c1316,#5f0f12)' }};
        padding: 1.6rem 1.75rem; color: white; border-bottom: 4px solid rgba(0,0,0,0.1);
        position: relative; overflow: hidden;
    }
    .card-header-custom::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .btn-maroon { background-color: #7c1316; color: white; border: none; border-radius: 50px; padding: 0.8rem 2rem; font-weight: 600; }
    .btn-maroon:hover { background-color: #a3191d; color: white; transform: translateY(-2px); }
</style>
@php
    // Cek apakah ruangan ini mengandung kata "Gizi"
    $isGizi = \Illuminate\Support\Str::contains(strtolower($sequence->ruangan->nm_ruangan), 'gizi');
@endphp
<div class="">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="form-card animate-up">
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi {{ $isLocked ? 'bi-eye-fill' : 'bi-pencil-square' }} me-2"></i> 
                                {{ $isLocked ? 'Detail Shift Harian (Read Only)' : 'Atur Shift Harian' }}
                            </h5>
                            <p class="mb-0 opacity-75 small">
                                @if(isset($sequence->mahasiswa))Mahasiswa: <strong>{{ $sequence->mahasiswa->nm_mahasiswa }}</strong> | @endif
                                Ruangan: <strong>{{ $sequence->ruangan->nm_ruangan }}</strong> |
                                Periode: {{ \Carbon\Carbon::parse($sequence->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($sequence->end_date)->format('d M Y') }}
                            </p>
                        </div>
                        @if($isLocked)
                            <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Terkunci</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    @if(!empty($canRentang))
                        <div class="p-3 mb-4 rounded-3" style="background:#fcf0f1;border:1px solid #f0d3d5;">
                            <div class="fw-bold mb-2" style="color:#7c1316;"><i class="bi bi-calendar-range me-2"></i>Ubah Rentang Tanggal (Periode di ruangan ini)</div>
                            <form action="{{ route('shift_schedule.rentang', $sequence->id) }}" method="POST" class="row g-2 align-items-end"
                                  onsubmit="return confirm('Ubah rentang tanggal mahasiswa ini? Daftar tanggal shift akan menyesuaikan.');">
                                @csrf
                                <div class="col-sm-5">
                                    <label class="form-label small mb-1 fw-semibold">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::parse($sequence->start_date)->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label small mb-1 fw-semibold">Tanggal Selesai</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::parse($sequence->end_date)->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-sm-2 d-grid">
                                    <button type="submit" class="btn fw-bold text-white" style="background:#7c1316;"><i class="bi bi-check2-circle me-1"></i>Simpan</button>
                                </div>
                            </form>
                            <small class="text-muted d-block mt-2">Setelah rentang diubah, periksa & isi shift untuk tanggal yang baru.</small>
                        </div>
                    @endif

                    @if(!empty($semuaSequences) && count($semuaSequences))
                        @php $adaBentrok = $semuaSequences->contains(function($s){ return $s->is_bentrok; }); @endphp
                        <div class="mb-4">
                            <div class="fw-bold mb-2 text-dark"><i class="bi bi-signpost-split me-2" style="color:#7c1316;"></i>Rotasi / Jadwal Mahasiswa Ini</div>
                            @if($adaBentrok)
                                <div class="alert alert-danger py-2 small mb-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i><strong>Rentang bentrok terdeteksi!</strong>
                                    Ada rotasi ruangan lain yang tanggalnya beririsan dengan rentang ini. Mohon minta mahasiswa memperbaiki rentang yang bertabrakan.
                                </div>
                            @endif
                            <div class="table-responsive border rounded-3">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-light"><tr><th class="ps-3 py-2">Ruangan</th><th>Rentang</th><th class="text-center pe-3">Status</th></tr></thead>
                                    <tbody>
                                        @foreach($semuaSequences as $s)
                                        <tr class="{{ $s->is_current ? 'table-warning' : ($s->is_bentrok ? 'table-danger' : '') }}">
                                            <td class="ps-3 fw-semibold">{{ optional($s->ruangan)->nm_ruangan ?? '-' }}</td>
                                            <td class="small">{{ \Carbon\Carbon::parse($s->start_date)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}</td>
                                            <td class="text-center pe-3">
                                                @if($s->is_current)
                                                    <span class="badge bg-warning text-dark">Ruangan Ini</span>
                                                @elseif($s->is_bentrok)
                                                    <span class="badge bg-danger">Bentrok</span>
                                                @else
                                                    <span class="badge bg-light text-secondary border">Lain</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($isLocked)
                        <div class="alert alert-success border-0 d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Data Sudah Final.</strong><br>
                                Jadwal shift Anda sudah tersimpan dan terkunci. Anda hanya dapat melihat data ini.
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info border-0 d-flex align-items-center mb-4 bg-light text-dark">
                            <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                            <div>
                                <strong>Petunjuk Pengisian:</strong><br>
                                Silakan tentukan shift Anda untuk setiap tanggal. <br>
                         @if($isGizi)
                                    <em class="small text-danger fw-bold">
                                        (Khusus Gizi - Pagi: 04:30, Siang: 11:20, Reguler: 07:15)
                                    </em>
                                @else
                                    <em class="small text-muted">
                                        (Pagi: 07-14, Siang: 14-21, Malam: 21-07 atau 20-07 untuk Merak)
                                    </em>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('shift_schedule.store', $sequence->id) }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3" style="width: 30%">Tanggal</th>
                                        <th style="width: 20%">Hari</th>
                                        <th class="pe-4">Shift</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($period as $date)
                                    @php 
                                        $d = $date->format('Y-m-d');
                                        $val = $existingShifts[$d] ?? ''; 
                                        $isToday = $date->isToday();
                                    @endphp
                                    <tr class="{{ $isToday ? 'table-warning' : '' }}">
                                        <td class="ps-4 fw-bold">
                                            {{ $date->translatedFormat('d F Y') }}
                                            @if($isToday) <span class="badge bg-danger ms-2">HARI INI</span> @endif
                                        </td>
                                        <td class="text-muted">{{ $date->translatedFormat('l') }}</td>
                                        <!--<td class="pe-4">-->
                                        <!--    <select name="shifts[{{ $d }}]" class="form-select border-primary" -->
                                        <!--        required -->
                                        <!--        {{ $isLocked ? 'disabled' : '' }} {{-- MATIKAN INPUT JIKA LOCKED --}}-->
                                        <!--    >-->
                                        <!--        <option value="" disabled {{ $val == '' ? 'selected' : '' }}>-- Pilih --</option>-->
                                        <!--  @if($isGizi)-->
                                        <!--            {{-- OPSI KHUSUS GIZI --}}-->
                                        <!--            <option value="Pagi" {{ $val == 'Pagi' ? 'selected' : '' }}>Pagi (04:30 - 12:40)</option>-->
                                        <!--            <option value="Siang" {{ $val == 'Siang' ? 'selected' : '' }}>Siang (11:20 - 19:30)</option>-->
                                        <!--               <option value="Libur" {{ $val == 'Libur' ? 'selected' : '' }}>Libur</option>-->
                                        <!--            {{-- Di Gizi biasanya tidak ada Malam, tapi jika ada biarkan saja --}}-->
                                        <!--            <option value="Reguler" {{ $val == 'Reguler' || $val == 'Non-Shift' ? 'selected' : '' }}>Reguler (07:15 - 15:25)</option>-->
                                        <!--        @else-->
                                        <!--            {{-- OPSI STANDARD --}}-->
                                        <!--            <option value="Pagi" {{ $val == 'Pagi' ? 'selected' : '' }}>Pagi</option>-->
                                        <!--            <option value="Siang" {{ $val == 'Siang' ? 'selected' : '' }}>Siang</option>-->
                                        <!--            <option value="Malam" {{ $val == 'Malam' ? 'selected' : '' }}>Malam</option>-->
                                        <!--               <option value="Libur" {{ $val == 'Libur' ? 'selected' : '' }}>Libur</option>-->
                                        <!--        @endif-->
                                        <!--    </select>-->
                                        <!--</td>-->
                                        <td class="pe-4">
    <select name="shifts[{{ $d }}]" class="form-select border-primary" required {{ $isLocked ? 'disabled' : '' }}>
        <option value="" disabled {{ $val == '' ? 'selected' : '' }}>-- Pilih --</option>
        
        @foreach($availableShifts as $shiftOption)
            <option value="{{ $shiftOption }}" {{ $val == $shiftOption ? 'selected' : '' }}>
                {{ $shiftOption }}
            </option>
        @endforeach
        
    </select>
</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ $backUrl ?? route('room_sequences.index') }}" class="btn btn-light border shadow-sm px-4 rounded-pill">
                                <i class="bi bi-arrow-left me-2"></i> {{ $isLocked ? 'Kembali' : 'Batal' }}
                            </a>
                            
                            @if(!$isLocked)
                                <button type="submit" class="btn btn-maroon shadow-sm">
                                    Simpan Jadwal <i class="bi bi-check-lg ms-2"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection