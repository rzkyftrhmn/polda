@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-user-plus me-1"></i> {{ isset($editUser) ? 'Edit User' : 'Tambah User' }}
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <form method="POST" action="{{ isset($editUser) ? route('users.update', $editUser->id) : route('users.store') }}">
                            @csrf
                            @if(isset($editUser))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', isset($editUser) ? $editUser->name : '') }}" required>
                                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', isset($editUser) ? $editUser->email : '') }}" required>
                                    @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="{{ old('username', isset($editUser) ? $editUser->username : '') }}">
                                    @error('username')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" {{ isset($editUser) ? '' : 'required' }}>
                                    @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" {{ isset($editUser) ? '' : 'required' }}>
                                    @error('password_confirmation')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control default-select h-auto wide select2">
                                        <option value="">Pilih Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role', isset($currentRole) ? $currentRole : '') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Institusi</label>
                                    <select name="institution_id" class="form-control default-select h-auto wide select2">
                                        <option value="">Pilih Institusi</option>
                                        @foreach ($institutions as $ins)
                                            <option value="{{ $ins->id }}" {{ old('institution_id', isset($editUser) ? $editUser->institution_id : '') == $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('institution_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sub Bagian</label>
                                    <select name="division_id" class="form-control default-select h-auto wide select2">
                                        <option value="">Pilih Sub Bagian</option>
                                        @foreach ($divisions as $div)
                                            <option value="{{ $div->id }}" {{ old('division_id', isset($editUser) ? $editUser->division_id : '') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('division_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-success">{{ isset($editUser) ? 'Update' : 'Simpan' }}</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-warning">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection