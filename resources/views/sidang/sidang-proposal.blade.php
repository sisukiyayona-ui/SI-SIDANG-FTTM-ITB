@extends('layouts.master')

@section('title', 'Sidang Proposal - SI SIDANG FTTM ITB')
@section('page_title', 'Sidang Proposal')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Sidang Proposal</li>
    </ol>
@endsection

@section('content')
    @include('sidang._table', ['data' => $data, 'columns' => ['Mahasiswa', 'NIM', 'Prodi', 'Judul', 'Tanggal', 'Ruang', 'Status']])
@endsection
