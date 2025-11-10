@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ isset($subdivision) ? 'Edit Sub Divisi' : 'Tambah Sub Divisi' }}</h5>
                </div>
                <div class="card-body">
                    <form 
                        action="{{ isset($subdivision) ? route('subdivisions.update', $subdivision->id) : route('subdivisions.store') }}" 
                        method="POST"
                    >
                        @csrf
                        @if(isset($subdivision))
                            @method('PUT')
                        @endif

                        <!-- Parent Division -->
                        <div class="form-group mb-3">
                            <label>Divisi Induk</label>
                            <select name="parent_id" class="form-control" required>
                                <option value="">-- Pilih Divisi Induk --</option>
                                @foreach ($parentDivisions as $parent)
                                    <option value="{{ $parent->id }}" 
                                        {{ old('parent_id', $subdivision->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nama Sub Divisi -->
                        <div class="form-group mb-3">
                            <label>Nama Sub Divisi</label>
                            <input 
                                type="text" 
                                name="name" 
                                class="form-control" 
                                value="{{ old('name', $subdivision->name ?? '') }}" 
                                required
                            >
                        </div>

                        <!-- Jenis -->
                        <div class="form-group mb-3">
                            <label>Tipe</label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="polres" {{ old('type', $subdivision->type ?? '') == 'polres' ? 'selected' : '' }}>Polres</option>
                                <option value="polda" {{ old('type', $subdivision->type ?? '') == 'polda' ? 'selected' : '' }}>Polda</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">
                            {{ isset($subdivision) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('subdivisions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
