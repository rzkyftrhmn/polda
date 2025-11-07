@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ isset($institution) ? 'Edit Institusi' : 'Tambah Institusi' }}</h5>
                </div>
                <div class="card-body">
                    <form 
                        action="{{ isset($institution) ? route('institutions.update', $institution->id) : route('institutions.store') }}" 
                        method="POST"
                    >
                        @csrf
                        @if(isset($institution))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label>Nama Institusi</label>
                            <input 
                                type="text" 
                                name="name" 
                                class="form-control" 
                                value="{{ old('name', $institution->name ?? '') }}" 
                                required
                            >
                        </div>

                        <div class="form-group mb-3">
                            <label>Tipe Institusi</label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="polres" {{ (old('type', $institution->type ?? '') == 'polres') ? 'selected' : '' }}>Polres</option>
                                <option value="polda" {{ (old('type', $institution->type ?? '') == 'polda') ? 'selected' : '' }}>Polda</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            {{ isset($institution) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('institutions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
