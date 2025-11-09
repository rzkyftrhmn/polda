@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ isset($editPermission) ? 'Edit Permission' : 'Tambah Permission' }}</h5>
                </div>
                <div class="card-body">
                    <form 
                        action="{{ isset($editPermission) ? route('permissions.update', $editPermission->id) : route('permissions.store') }}" 
                        method="POST"
                    >
                        @csrf
                        @if(isset($editPermission))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label>Nama Permission</label>
                            <input 
                                type="text" 
                                name="name" 
                                class="form-control" 
                                value="{{ old('name', $editPermission->name ?? '') }}" 
                                required
                            >
                        </div>

                        <div class="form-group mb-3">
                            <label>Guard Name</label>
                            <input 
                                type="text" 
                                name="guard_name" 
                                class="form-control" 
                                value="{{ old('guard_name', $editPermission->guard_name ?? 'web') }}" 
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            {{ isset($editPermission) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
