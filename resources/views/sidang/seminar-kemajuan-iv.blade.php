@extends('layouts.master')

@section('title', 'Seminar Kemajuan IV - SI SIDANG FTTM ITB')
@section('page_title', 'Seminar Kemajuan IV')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Seminar Kemajuan IV</li>
    </ol>
@endsection

@section('content')
    @include('sidang._table', ['data' => $data, 'columns' => ['Mahasiswa', 'NIM', 'Prodi', 'Bab', 'Tanggal', 'Ruang', 'Status']])
@endsection
