@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-file-plus me-1"></i>{{ isset($pelaporan) ? 'Edit Dugaan Pelanggaran' : 'Dugaan Pelanggaran' }}
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
                                <div class="col-12 mb-3">
                                    <h5 class="fw-semibold">Data Identitas Pelapor</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Pelapor</label>
                                    <input type="text" name="name_of_reporter" class="form-control" value="{{ old('name_of_reporter', $pelaporan->name_of_reporter ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No Telepon Pelapor</label>
                                    <input type="number" name="phone_of_reporter" class="form-control" value="{{ old('phone_of_reporter', $pelaporan->phone_of_reporter ?? '') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Alamat Pelapor</label>
                                    <textarea type="text" name="address_of_reporter" class="form-control" >{{ old('address_of_reporter', $pelaporan->address_of_reporter ?? '') }}</textarea>
                                </div>

                                <div class="col-12"><hr></div>

                                <div class="col-12 mb-3">
                                    <h5 class="fw-semibold">Data Laporan</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $pelaporan->title ?? '') }}" required>
                                    @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="incident_datetime" class="form-control" value="{{ old('incident_datetime', isset($pelaporan->incident_datetime) ? \Carbon\Carbon::parse($pelaporan->incident_datetime)->format('Y-m-d') : '') }}" required>
                                    @error('incident_datetime')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori Pelanggaran <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control select2" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $pelaporan->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kota <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control select2" required>
                                        <option value="">Pilih Kota</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $pelaporan->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <select name="district_id" id="district_id" class="form-control select2" required>
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}" {{ old('district_id', $pelaporan->district_id ?? '') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('district_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alamat Detail</label>
                                    <input type="text" name="address_detail" class="form-control" value="{{ old('address_detail', $pelaporan->address_detail ?? '') }}">
                                    @error('address_detail')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Kronologi <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" required>{{ old('description', $pelaporan->description ?? '') }}</textarea>
                                    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12"><hr></div>

                                <div class="col-12 mb-3 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-semibold mb-0">Data Identitas Terlapor</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#terlaporModal">Tambah Terlapor</button>
                                </div>
                                <div class="col-12 mb-2">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Jenis Satuan</th>
                                                <th>Satker/Satwil</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="suspects-table-body"></tbody>
                                    </table>
                                    <div id="suspects-hidden-inputs" style="display:none"></div>
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

<div class="modal fade" id="terlaporModal" tabindex="-1" aria-labelledby="terlaporModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="terlaporModalLabel">Input Data Terlapor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Terlapor</label>
          <input type="text" class="form-control" id="tlp-name">
        </div>
        <div class="mb-3">
          <label class="form-label">Jenis Satuan</label>
          <select class="form-select" id="tlp-unit-type">
            <option value="">Pilih Jenis</option>
            <option value="Satker">Satker</option>
            <option value="Satwil">Satwil</option>
          </select>
        </div>
        <div class="mb-3" id="tlp-satker-field" style="display:none;">
          <label class="form-label">Satker</label>
          <select class="form-select" id="tlp-satker">
            <option value="">Pilih Satker</option>
          </select>
        </div>
        <div class="mb-3" id="tlp-satwil-field" style="display:none;">
          <label class="form-label">Satwil</label>
          <select class="form-select" id="tlp-satwil">
            <option value="">Pilih Satwil</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="tlp-save">Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
var oldCityId = "{{ $pelaporan->city_id ?? '' }}";
var oldDistrictId = "{{ $pelaporan->district_id ?? '' }}";
$(document).ready(function () {
    loadCities(oldCityId);
    
    $('#city_id').on('change', function () {
        loadDistricts($(this).val(), null);
    });
});
    
function loadCities(selectedCityId = null) {
    const provinceId = 12; 
    
    $.get('/get-cities/' + provinceId, function (data) {
        $('#city_id').html('<option value="">Pilih Kota</option>');
        
        $.each(data, function (i, city) {
            let selected = city.id == selectedCityId ? 'selected' : '';
            $('#city_id').append(
                '<option value="' + city.id + '" ' + selected + '>' + city.name + '</option>'
            );
        });

        if (selectedCityId) {
            loadDistricts(selectedCityId, oldDistrictId);
        }
    });
}

function loadDistricts(cityId, selectedDistrictId = null) {
    if (!cityId) {
        $('#district_id').html('<option value="">Pilih Kota terlebih dahulu</option>');
        return;
    }

    $.get('/get-districts/' + cityId, function (data) {
        $('#district_id').html('<option value="">Pilih Kecamatan</option>');

        $.each(data, function (i, district) {
            let selected = district.id == selectedDistrictId ? 'selected' : '';
            $('#district_id').append(
                '<option value="' + district.id + '" ' + selected + '>' + district.name + '</option>'
            );
        });
    });
}

window.appendSuspectRow = function(data) {
    const tbody = document.getElementById('suspects-table-body');
    const hiddenInputs = document.getElementById('suspects-hidden-inputs');

    const index = tbody.children.length; 
    const rowId = "suspect-row-" + index;

    const unitName = data.unit_type === 'Satker' ? data.satker_name : data.satwil_name;

    const row = document.createElement('tr');
    row.setAttribute("id", rowId);

    row.innerHTML = `
        <td>${data.name}</td>
        <td>${data.unit_type}</td>
        <td>${unitName}</td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeSuspect('${rowId}')">Hapus</button>
        </td>
    `;

    tbody.appendChild(row);

    hiddenInputs.innerHTML += `
        <div id="inputs-${rowId}">
            <input type="hidden" name="suspects[${index}][name]" value="${data.name}">
            <input type="hidden" name="suspects[${index}][unit_type]" value="${data.unit_type}">
            <input type="hidden" name="suspects[${index}][division_id]" 
                value="${data.unit_type === 'Satker' ? data.satker_id : data.satwil_id}">
        </div>
    `;
};

window.removeSuspect = function(rowId) {
    document.getElementById(rowId)?.remove();

    document.getElementById("inputs-" + rowId)?.remove();
}


document.addEventListener('DOMContentLoaded', function() {
  const unitType = document.getElementById('tlp-unit-type');
  const satkerField = document.getElementById('tlp-satker-field');
  const satwilField = document.getElementById('tlp-satwil-field');
  const satkerSelect = document.getElementById('tlp-satker');
  const satwilSelect = document.getElementById('tlp-satwil');

  function loadDivisions(type) {
    fetch(`/api/divisions?type=${type}`)
      .then(res => res.json())
      .then(data => {
        const targetSelect = type === 'Satker' ? satkerSelect : satwilSelect;

        targetSelect.innerHTML = `<option value="">Pilih ${type}</option>`;

        data.forEach(item => {
          targetSelect.innerHTML += `
            <option value="${item.id}">${item.name}</option>
          `;
        });
      });
  }

  function toggleUnitFields() {
    const val = unitType.value;

    satkerField.style.display = val === 'Satker' ? 'block' : 'none';
    satwilField.style.display = val === 'Satwil' ? 'block' : 'none';

    if (val === 'Satker') {
      satwilSelect.innerHTML = '<option value="">Pilih Satwil</option>';
      loadDivisions('Satker');
    }

    if (val === 'Satwil') {
      satkerSelect.innerHTML = '<option value="">Pilih Satker</option>';
      loadDivisions('Satwil');
    }
  }

  unitType.addEventListener('change', toggleUnitFields);

  document.getElementById('tlp-save').addEventListener('click', function() {
    const data = {
      name: document.getElementById('tlp-name').value.trim(),
      unit_type: unitType.value,
      satker_id: satkerSelect.value,
      satwil_id: satwilSelect.value,
      satker_name: satkerSelect.selectedOptions[0]?.text || '',
      satwil_name: satwilSelect.selectedOptions[0]?.text || ''
    };

    if (!data.name) return;

    if (window.appendSuspectRow) {
      window.appendSuspectRow(data);
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('terlaporModal')).hide();

    document.getElementById('tlp-name').value = '';
  });
});

@isset($pelaporan)
    @foreach ($pelaporan->suspects as $suspect)
        window.appendSuspectRow({
            name: "{{ $suspect->name }}",
            unit_type: "{{ $suspect->division->type ?? '' }}",
            satker_id: "{{ $suspect->division_id }}",
            satwil_id: "{{ $suspect->division_id }}",
            satker_name: "{{ $suspect->division->name ?? '-' }}",
            satwil_name: "{{ $suspect->division->name ?? '-' }}"
        });
    @endforeach
@endisset
</script>
@endsection
