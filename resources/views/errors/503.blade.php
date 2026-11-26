@extends('errors._layout')
@section('kode', '503')
@section('ikon', 'bi-tools')
@section('judul', 'Sedang Pemeliharaan')
@section('pesan', 'Sindikat sedang diperbarui sebentar. Silakan coba lagi dalam beberapa menit.')
@section('aksi')
    <a href="javascript:location.reload()" class="tbl utama"><i class="bi bi-arrow-clockwise"></i> Coba Lagi</a>
@endsection
