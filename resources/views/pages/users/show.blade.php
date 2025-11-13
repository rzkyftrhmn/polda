@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-user me-1"></i> Detail User
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $detailUser->name ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $detailUser->email ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $detailUser->username ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Institusi</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $detailUser->institution->name ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sub Bagian</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $detailUser->division->name ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $roleNames->join(', ') ?: '-' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dibuat</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ backChangeFormatDate($detailUser->created_at) ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                            <a href="{{ route('users.edit', $detailUser->id) }}" class="btn btn-warning">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection