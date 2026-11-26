@extends('errors._layout')
@section('kode', '419')
@section('ikon', 'bi-clock-history')
@section('judul', 'Sesi Berakhir')
@section('pesan', 'Halaman terlalu lama dibiarkan sehingga sesi Anda berakhir. Muat ulang halaman lalu ulangi lagi.')
@section('aksi')
    <a href="javascript:location.reload()" class="tbl utama"><i class="bi bi-arrow-clockwise"></i> Muat Ulang</a>
    <a href="{{ url('/login') }}" class="tbl kedua"><i class="bi bi-box-arrow-in-right"></i> Login</a>
@endsection
