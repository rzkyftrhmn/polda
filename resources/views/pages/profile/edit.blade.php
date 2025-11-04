@extends('layouts.dashboard')

@php($roleNames = $user->getRoleNames())

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card card-bx profile-card m-b30">
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
                            <span class="text-muted">Instansi</span>
                            <span class="text-white">{{ optional($user->institution)->name ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Divisi</span>
                            <span class="text-white">{{ optional($user->division)->name ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-3 border-top border-dark">
                            <span class="text-muted">Username</span>
                            <span class="text-white">{{ $user->username ?? '-' }}</span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary w-100">
                            <i class="fa fa-arrow-left me-2"></i>Kembali ke Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7">
            <div class="card profile-card card-bx m-b30">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title text-white mb-1">Pengaturan Profil</h4>
                    <span class="text-muted">Perbarui informasi utama akun Anda</span>
                </div>
                <form class="profile-form" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">
                        <div class="row gy-4 gx-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Instansi</label>
                                <select name="institution_id" class="default-select form-control select2" required>
                                    <option value="" disabled {{ old('institution_id', $user->institution_id) ? '' : 'selected' }}>-- Pilih Instansi --</option>
                                    @foreach($institutions as $inst)
                                        <option value="{{ $inst->id }}" @selected(old('institution_id', $user->institution_id) == $inst->id)>
                                            {{ $inst->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('institution_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Divisi</label>
                                <select name="division_id" class="default-select form-control select2" required>
                                    <option value="" disabled {{ old('division_id', $user->division_id) ? '' : 'selected' }}>-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}" @selected(old('division_id', $user->division_id) == $div->id)>
                                            {{ $div->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-0 pt-0 px-4 pb-4 d-flex flex-wrap gap-3 justify-content-between">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-light">Batal</a>
                    </div>
                </form>
            </div>
            <div class="card card-bx">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title text-white mb-1">Ubah Password</h4>
                    <span class="text-muted">Pastikan password baru kuat dan unik</span>
                </div>
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">
                        <div class="row gy-4 gx-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Password Baru</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-white">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-0 pt-0 px-4 pb-4">
                        <button type="submit" class="btn btn-warning">Perbarui Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    jQuery(function ($) {
        function bindLoadingState(formSelector) {
            var $form = $(formSelector);
            if (!$form.length) {
                return;
            }

            $form.on('submit', function () {
                var $button = $form.find('button[type="submit"][data-loading="true"]').first();
                if (!$button.length) {
                    return;
                }
                $button.prop('disabled', true).addClass('disabled');
                $button.find('.spinner-border').removeClass('d-none');
            });
        }

        bindLoadingState('#profileUpdateForm');
        bindLoadingState('#passwordUpdateForm');
    });
</script>
@endsection
