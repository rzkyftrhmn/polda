@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-8 offset-xl-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $role ? 'Edit Role' : 'Tambah Role' }}</h5>
                </div>
                <div class="card-body">
                    <form 
                        action="{{ $role ? route('roles.update', $role->id) : route('roles.store') }}" 
                        method="POST"
                    >
                        @csrf
                        @if($role)
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label>Nama Role</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $role->name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="d-block mb-2 fw-bold">Pilih Permissions</label>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="clearAll">Clear</button>
                            </div>

                            <div class="row">
                                @foreach($permissions as $perm)
                                    <div class="col-md-3">
                                        <label class="form-check-label d-block">
                                            <input 
                                                type="checkbox" 
                                                name="permissions[]" 
                                                class="form-check-input permission-checkbox"
                                                value="{{ $perm->name }}"
                                                {{ isset($rolePermissions) && in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                                            {{ $perm->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            {{ $role ? 'Update' : 'Simpan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('selectAll');
        const clearAllBtn = document.getElementById('clearAll');
        const checkboxes = document.querySelectorAll('.permission-checkbox');

        selectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = true);
        });

        clearAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
        });
    });
</script>
@endsection
