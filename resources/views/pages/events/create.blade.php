@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-calendar-plus me-1"></i>{{ isset($event) ? 'Edit Event' : 'Tambah Event' }}</div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <form id="eventForm" action="{{ isset($event) ? route('events.update', $event) : route('events.store') }}" method="POST">
                            @csrf
                            @if(isset($event))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Event</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $event->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="location" class="form-control" value="{{ old('location', $event->location ?? '') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control">{{ old('description', $event->description ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waktu Mulai</label>
                                    <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at', isset($event->start_at) ? $event->start_at->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waktu Selesai</label>
                                    <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', isset($event->end_at) ? $event->end_at->format('Y-m-d\TH:i') : '') }}">
                                </div>

                                <div class="col-12"><hr></div>

                                <div class="col-12 mb-3 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-semibold mb-0">Peserta</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#participantModal">Tambah Peserta</button>
                                </div>

                                <div class="col-12 mb-2">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Unit</th>
                                                <th>Status Kehadiran</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="participants-table-body"></tbody>
                                    </table>
                                    <div id="participants-hidden-inputs" style="display:none"></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">{{ isset($event) ? 'Update' : 'Simpan' }}</button>
                            <a href="{{ route('events.index') }}" class="btn btn-warning mt-3">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="participantModalLabel">Input Peserta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Unit</label>
          <select class="form-select" id="part-division">
            <option value="">Pilih Unit</option>
            @foreach($divisions as $division)
              <option value="{{ $division->id }}">{{ $division->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Status Kehadiran</label>
          <select class="form-select" id="part-required">
            <option value="1">Wajib</option>
            <option value="0">Opsional</option>
          </select>
        </div>
        <div class="mb-3" id="part-note-field" style="display:none;">
          <label class="form-label">Keterangan (untuk Opsional)</label>
          <input type="text" class="form-control" id="part-note">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="part-save">Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var requiredSelect = document.getElementById('part-required');
  var noteField = document.getElementById('part-note-field');
  var noteInput = document.getElementById('part-note');
  requiredSelect.addEventListener('change', function() {
    noteField.style.display = requiredSelect.value === '0' ? 'block' : 'none';
  });

  document.getElementById('part-save').addEventListener('click', function() {
    var divisionSelect = document.getElementById('part-division');
    var divisionId = divisionSelect.value;
    var divisionName = divisionSelect.selectedOptions[0]?.text || '';
    var isRequired = requiredSelect.value === '1';
    var note = noteInput.value.trim();

    if (!divisionId) return;

    var tbody = document.getElementById('participants-table-body');
    var hiddenInputs = document.getElementById('participants-hidden-inputs');
    var index = tbody.children.length;
    var rowId = 'participant-row-' + index;

    var row = document.createElement('tr');
    row.setAttribute('id', rowId);
    row.innerHTML = '\n      <td>' + divisionName + '</td>\n      <td>' + (isRequired ? 'Wajib' : 'Opsional') + '</td>\n      <td>' + (note || '-') + '</td>\n      <td><button type="button" class="btn btn-danger btn-sm" onclick="removeParticipant(\'' + rowId + '\')">Hapus</button></td>';
    tbody.appendChild(row);

    hiddenInputs.innerHTML += '\n      <div id="inputs-' + rowId + '">\n        <input type="hidden" name="participants[' + index + '][division_id]" value="' + divisionId + '">\n        <input type="hidden" name="participants[' + index + '][is_required]" value="' + (isRequired ? 1 : 0) + '">\n        <input type="hidden" name="participants[' + index + '][note]" value="' + (note || '') + '">\n      </div>';

    bootstrap.Modal.getOrCreateInstance(document.getElementById('participantModal')).hide();
    divisionSelect.value = '';
    requiredSelect.value = '1';
    noteInput.value = '';
    noteField.style.display = 'none';
  });
});

window.removeParticipant = function(rowId) {
  document.getElementById(rowId)?.remove();
  document.getElementById('inputs-' + rowId)?.remove();
}

document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('eventForm');
  if (!form) return;
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var proceed = function() { form.submit(); };
    if (window.Swal) {
      Swal.fire({
        title: '{{ isset($event) ? 'Update event?' : 'Simpan event?' }}',
        text: 'Data akan disimpan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '{{ isset($event) ? 'Update' : 'Simpan' }}',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.isConfirmed) proceed();
      });
    } else {
      if (confirm('{{ isset($event) ? 'Update event?' : 'Simpan event?' }}')) proceed();
    }
  });
});
</script>
@endsection

