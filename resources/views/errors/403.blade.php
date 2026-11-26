@extends('errors._layout')
@section('kode', '403')
@section('ikon', 'bi-shield-lock')
@section('judul', 'Akses Ditolak')
@section('pesan')
    @php $msg = isset($exception) ? trim((string) $exception->getMessage()) : ''; @endphp
    {{ $msg !== '' && !in_array($msg, ['Forbidden', 'This action is unauthorized.', 'Unauthorized', 'Unauthorized action.']) ? $msg : 'Anda tidak memiliki izin untuk membuka halaman ini.' }}
@endsection
