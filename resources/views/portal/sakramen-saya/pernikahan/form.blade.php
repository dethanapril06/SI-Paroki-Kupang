@extends('layouts.portal')
@section('title', 'Edit Pernikahan')
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last"><h3>Edit Data Pernikahan</h3></div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.index') }}">Sakramen Saya</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.pernikahan') }}">Pernikahan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Edit Data Pernikahan</h5></div>
            <div class="card-body">
                @include('portal.sakramen-saya.pernikahan._form', ['action' => route('portal.sakramen-saya.pernikahan.update'), 'method' => 'PUT'])
            </div>
        </div>
    </section>
</div>
@endsection
