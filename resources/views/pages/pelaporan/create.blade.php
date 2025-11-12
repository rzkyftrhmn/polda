@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-file-plus me-1"></i>{{ isset($pelaporan) ? 'Edit Laporan' : 'Tambah Laporan' }}
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <form action="{{ isset($pelaporan) ? route('pelaporan.update', $pelaporan->id) : route('pelaporan.store') }}" method="POST">
                            @csrf
                            @if(isset($pelaporan))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <!-- Judul Laporan -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $pelaporan->title ?? '') }}" required>
                                    @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Deskripsion <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" required>{{ old('description', $pelaporan->description ?? '') }}</textarea>
                                    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Tanggal Kejadian -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
                                    <input type="date" name="incident_datetime" class="form-control" 
                                        value="{{ old('incident_datetime', isset($pelaporan->incident_datetime) ? \Carbon\Carbon::parse($pelaporan->incident_datetime)->format('Y-m-d') : '') }}" 
                                        required>
                                    @error('incident_datetime')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Provinsi -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <select name="province_id" id="province_id" class="form-control select2" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" {{ old('province_id', $pelaporan->province_id ?? '') == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Kategori Laporan -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori Laporan <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control select2" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $pelaporan->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Kota -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kota <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control select2" required>
                                        <option value="">Pilih Kota</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $pelaporan->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Alamat Detail -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alamat Detail <span class="text-danger">*</span></label>
                                    <input type="text" name="address_detail" class="form-control" value="{{ old('address_detail', $pelaporan->address_detail ?? '') }}">
                                    @error('address_detail')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Kecamatan -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <select name="district_id" id="district_id" class="form-control select2" required>
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}" {{ old('district_id', $pelaporan->district_id ?? '') == $district->id ? 'selected' : '' }}>
                                                {{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('district_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Suspects</label>
                                    <div id="suspects-wrapper">
                                        @if(old('suspects', $pelaporan->suspects ?? false))
                                            @foreach(old('suspects', $pelaporan->suspects) as $index => $suspect)
                                                <div class="suspect-item mb-2">
                                                    <input type="text" name="suspects[{{ $index }}][name]" value="{{ $suspect['name'] ?? $suspect->name }}" placeholder="Nama Pelaku" class="form-control mb-1" required>
                                                    <input type="text" name="suspects[{{ $index }}][description]" value="{{ $suspect['description'] ?? $suspect->description }}" placeholder="Deskripsi (opsional)" class="form-control mb-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-suspect">Hapus</button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" id="add-suspect" class="btn btn-primary btn-sm mt-2">Tambah Suspect</button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">{{ isset($pelaporan) ? 'Update' : 'Simpan' }}</button>
                            <a href="{{ route('pelaporan.index') }}" class="btn btn-warning mt-3">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let suspectIndex = $('#suspects-wrapper .suspect-item').length;

    $('#add-suspect').click(function() {
        let html = `
        <div class="suspect-item mb-2">
            <input type="text" name="suspects[${suspectIndex}][name]" placeholder="Nama Pelaku" class="form-control mb-1" required>
            <input type="text" name="suspects[${suspectIndex}][description]" placeholder="Deskripsi (opsional)" class="form-control mb-1">
            <button type="button" class="btn btn-danger btn-sm remove-suspect">Hapus</button>
        </div>`;
        $('#suspects-wrapper').append(html);
        suspectIndex++;
    });

    $(document).on('click', '.remove-suspect', function() {
        $(this).closest('.suspect-item').remove();
    });
});

$(document).ready(function() {
    var oldProvinceId = '{{ old('province_id', $pelaporan->province_id ?? '') }}';
    var oldCityId = '{{ old('city_id', $pelaporan->city_id ?? '') }}';
    var oldDistrictId = '{{ old('district_id', $pelaporan->district_id ?? '') }}';

    function loadCities(provinceId, selectedCityId = null){
        if(!provinceId){
            $('#city_id').html('<option value="">Pilih Provinsi terlebih dahulu</option>');
            $('#district_id').html('<option value="">Pilih Kota terlebih dahulu</option>');
            return;
        }
        $.get('{{ url("get-cities") }}/' + provinceId, function(data){
            $('#city_id').html('<option value="">Pilih Kota</option>');
            $.each(data, function(i, city){
                var selected = city.id == selectedCityId ? 'selected' : '';
                $('#city_id').append('<option value="'+city.id+'" '+selected+'>'+city.name+'</option>');
            });
            if(selectedCityId){
                loadDistricts(selectedCityId, oldDistrictId);
            }
        });
    }

    function loadDistricts(cityId, selectedDistrictId = null){
        if(!cityId){
            $('#district_id').html('<option value="">Pilih Kota terlebih dahulu</option>');
            return;
        }
        $.get('{{ url("get-districts") }}/' + cityId, function(data){
            $('#district_id').html('<option value="">Pilih Kecamatan</option>');
            $.each(data, function(i, district){
                var selected = district.id == selectedDistrictId ? 'selected' : '';
                $('#district_id').append('<option value="'+district.id+'" '+selected+'>'+district.name+'</option>');
            });
        });
    }

    if(oldProvinceId){
        loadCities(oldProvinceId, oldCityId);
    }

    $('#province_id').change(function(){
        loadCities($(this).val());
    });

    $('#city_id').change(function(){
        loadDistricts($(this).val());
    });
});
</script>
@endsection
