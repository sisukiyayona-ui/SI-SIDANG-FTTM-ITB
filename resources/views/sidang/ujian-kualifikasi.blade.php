@extends('layouts.master')

@section('title', 'Ujian Kualifikasi - SI SIDANG FTTM ITB')
@section('page_title', 'Ujian Kualifikasi')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Ujian Kualifikasi</li>
    </ol>
@endsection

@section('content')
    @php $routeBase = 'sidang.ujian-kualifikasi'; @endphp
    @include('sidang._table', ['data' => $data, 'columns' => ['Mahasiswa', 'NIM', 'Prodi', 'Tanggal', 'Ruang', 'Status']])
@endsection
