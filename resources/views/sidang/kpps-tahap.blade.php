@extends('layouts.master')

@php
    $tahapLabel = [
        'tahap I' => 'Ujian Kualifikasi',
        'tahap II' => 'Ujian Proposal',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
    ];
    $tahapLabel = $tahapLabel[$tahapan] ?? $tahapan;
@endphp

@section('title', 'Detail Ajuan Sidang ' . $strata . ' - SI SIDANG FTTM ITB')
@section('page_title', 'Form Tahapan: ' . $tahapLabel . ' (' . $strata . ')')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sidang.approve-ajuan.index', $strata) }}">Approve Ajuan Sidang {{ $strata }}</a></li>
        <li class="breadcrumb-item active">{{ $tahapLabel }}</li>
    </ol>
@endsection

@section('content')
<div class="card">
    @include('sidang.kpps-tahap-content')
</div>
@endsection