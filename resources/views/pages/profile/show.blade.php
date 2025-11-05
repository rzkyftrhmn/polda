@extends('layouts.dashboard')

@php($roleNames = $user->getRoleNames())
@php($profilePhoto = $user->photo_url ?: asset('dashboard/images/user.jpg'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card card-bx profile-card m-b30 theme-bg-card">
                <div class="card-body p-4">
                    <div class="author-profile text-center mb-4">
                        <div class="author-media mb-3">
                            <img src="{{ $profilePhoto }}" alt="Avatar" class="rounded-circle img-fluid object-fit-cover">
                        </div>
                        <div class="author-info">
                            <h4 class="title mb-1 theme-text-main">{{ $user->name }}</h4>
                            <span class="theme-text-secondary">{{ $roleNames->implode(', ') ?: 'Pengguna' }}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-secondary">
                            <span class="theme-text-secondary">Email</span>
                            <span class="theme-text-main">{{ $user->email }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-secondary">
                            <span class="theme-text-secondary">Username</span>
                            <span class="theme-text-main">{{ $user->username ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-secondary">
                            <span class="theme-text-secondary">Role</span>
                            <span class="theme-text-main">{{ $roleNames->implode(', ') ?: '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-secondary">
                            <span class="theme-text-secondary">Instansi</span>
                            <span class="theme-text-main">{{ optional($user->institution)->name ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-secondary">
                            <span class="theme-text-secondary">Divisi</span>
                            <span class="theme-text-main">{{ optional($user->division)->name ?? '-' }}</span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">Edit Profil</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7">
            <div class="card profile-card card-bx m-b30 theme-bg-card">
                <div class="card-header border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h4 class="card-title mb-1 theme-text-main">Detail Profil</h4>
                        <span class="theme-text-secondary">Informasi akun Anda saat ini</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-2"></i>Ubah Profil
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row gy-4 gx-3">
                        <div class="col-md-6">
                            <small class="d-block theme-text-secondary">Nama Lengkap</small>
                            <h5 class="mb-0 theme-text-main">{{ $user->name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block theme-text-secondary">Username</small>
                            <h5 class="mb-0 theme-text-main">{{ $user->username ?? '-' }}</h5>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block theme-text-secondary">Email</small>
                            <h5 class="mb-0 theme-text-main">{{ $user->email }}</h5>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block theme-text-secondary">Role</small>
                            <h5 class="mb-0 theme-text-main">{{ $roleNames->implode(', ') ?: '-' }}</h5>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block theme-text-secondary">Instansi</small>
                            <h5 class="mb-0 theme-text-main">{{ optional($user->institution)->name ?? '-' }}</h5>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block theme-text-secondary">Divisi</small>
                            <h5 class="mb-0 theme-text-main">{{ optional($user->division)->name ?? '-' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bx theme-bg-card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0 theme-text-main">Catatan Aktivitas</h4>
                </div>
                <div class="card-body p-4">
                    <p class="theme-text-secondary mb-0">Gunakan tombol "Edit Profil" untuk memperbarui informasi pribadi dan ubah password melalui menu yang tersedia.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
