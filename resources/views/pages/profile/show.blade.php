@extends('layouts.dashboard')

@php($roleNames = $user->getRoleNames())

@section('content')
<div class="container-fluid pb-4">
    <div class="row align-items-start g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card card-bx profile-card mb-4">
                <div class="card-body p-4">
                    <div class="author-profile text-center mb-4">
                        <div class="author-media mb-3">
                            <img src="{{ asset('dashboard/images/user.jpg') }}" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="author-info">
                            <h4 class="title text-white mb-1">{{ $user->name }}</h4>
                            <span class="text-muted">{{ $roleNames->implode(', ') ?: 'Pengguna' }}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Email</span>
                            <span class="text-white">{{ $user->email }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Username</span>
                            <span class="text-white">{{ $user->username ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Role</span>
                            <span class="text-white">{{ $roleNames->implode(', ') ?: '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Instansi</span>
                            <span class="text-white">{{ optional($user->institution)->name ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Divisi</span>
                            <span class="text-white">{{ optional($user->division)->name ?? '-' }}</span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">Edit Profil</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="d-flex flex-column gap-4">
                <div class="card profile-card card-bx">
                    <div class="card-header border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h4 class="card-title mb-1 text-white">Detail Profil</h4>
                            <span class="text-muted">Informasi akun Anda saat ini</span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit me-2"></i>Ubah Profil
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row gy-4 gx-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Nama Lengkap</small>
                                <h5 class="text-white mb-0">{{ $user->name }}</h5>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Username</small>
                                <h5 class="text-white mb-0">{{ $user->username ?? '-' }}</h5>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Email</small>
                                <h5 class="text-white mb-0">{{ $user->email }}</h5>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Role</small>
                                <h5 class="text-white mb-0">{{ $roleNames->implode(', ') ?: '-' }}</h5>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Instansi</small>
                                <h5 class="text-white mb-0">{{ optional($user->institution)->name ?? '-' }}</h5>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Divisi</small>
                                <h5 class="text-white mb-0">{{ optional($user->division)->name ?? '-' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-bx mb-4">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title text-white mb-0">Catatan Aktivitas</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-0">
                            Gunakan tombol "Edit Profil" untuk memperbarui informasi pribadi dan ubah password melalui menu yang tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
