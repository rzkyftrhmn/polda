@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow-sm border-0 report-detail-card">
                <div class="card-body report-detail-body">
                    @php
                        $incidentAt   = $report->incident_datetime?->format('d M Y H:i');
                        $finishedAt   = $report->finish_time?->format('d M Y H:i');
                        $categoryName = $report->category?->name;
                        $provinceName = $report->province?->name;
                        $cityName     = $report->city?->name;
                        $districtName = $report->district?->name;
                        $isCompleted = $report->status === \App\Enums\ReportJourneyType::COMPLETED->value;
                        $defaultFlow = $defaultFlow ?? ($showInspectionForm ? 'inspection' : 'investigation');
                    @endphp

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-detail" type="button" role="tab">
                                <i class="fa fa-file-alt me-2"></i>Detail Laporan
                            </button>
                        </li>
                        @if($showProgressTab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-progress" type="button" role="tab">
                                    <i class="fa fa-tasks me-2"></i>Update Progress
                                </button>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-timeline" type="button" role="tab">
                                <i class="fa fa-clock me-2"></i>Timeline
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">
                        <div class="tab-pane fade show active" id="tab-detail" role="tabpanel">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                                <div>
                                    <h4 class="mb-2">{{ $report->title }}</h4>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-body-secondary text-body">Kode: {{ $report->code ?? '-' }}</span>
                                        <span class="badge bg-primary-subtle text-primary text-uppercase">Status: {{ $statusLabel ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="fw-semibold">Data Identitas Pelapor</h6>
                                </div>
                                <div class="col-md-4"><p class="mb-1"><strong>Nama Pelapor:</strong> {{ $report->name_of_reporter ?? '-' }}</p></div>
                                <div class="col-md-4"><p class="mb-1"><strong>Alamat Pelapor:</strong> {{ $report->address_of_reporter ?? '-' }}</p></div>
                                <div class="col-md-4"><p class="mb-1"><strong>No Telepon Pelapor:</strong> {{ $report->phone_of_reporter ?? '-' }}</p></div>
                                <div class="col-12"><hr></div>
                                <div class="col-12">
                                    <h6 class="fw-semibold">Data Laporan</h6>
                                </div>
                                <div class="col-md-6"><p class="mb-1"><strong>Judul Laporan:</strong> {{ $report->title ?? '-' }}</p></div>
                                <div class="col-md-6"><p class="mb-1"><strong>Kategori Laporan:</strong> {{ $categoryName ?? '-' }}</p></div>
                                <div class="col-md-6"><p class="mb-1"><strong>Kronologi:</strong> {!! nl2br(e($report->description)) !!}</p></div>
                                <div class="col-md-6"><p class="mb-1"><strong>Tanggal:</strong> {{ $incidentAt ?? '-' }}</p></div>
                                <div class="col-md-6"><p class="mb-1"><strong>Kota:</strong> {{ $cityName ?? '-' }}</p></div>
                                <div class="col-12"><hr></div>
                                <div class="col-12 d-flex align-items-center justify-content-between">
                                    <h6 class="fw-semibold mb-0">Data Identitas Terlapor</h6>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th>Telepon</th>
                                                <th>Jenis Satuan</th>
                                                <th>Satker/Satwil</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($report->suspects ?? [] as $suspect)
                                                <tr>
                                                    <td>{{ $suspect->name ?? '-' }}</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>{{ $suspect->division?->type ?? '-' }}</td>
                                                    <td>{{ $suspect->division?->name ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data terlapor.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <a href="{{ route('pelaporan.index') }}" class="btn btn-warning mt-3">Kembali</a>
                        </div>

                        @if($showProgressTab)
                        <div class="tab-pane fade" id="tab-progress" role="tabpanel">
                            @if(!$hasAccess)
                                <div class="alert alert-warning mb-0">Anda tidak memiliki akses untuk mengupdate progress laporan ini.</div>
                            @elseif(!$showInspectionForm && !$showInvestigationForm)
                                <div class="alert alert-warning mb-0">Anda tidak memiliki akses untuk mengupdate progress laporan ini.</div>
                            @else
                                <form id="progressForm" action="{{ route('reports.progress.store', $report->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="action" id="progress-action" value="save">
                                    <input type="hidden" name="flow" id="progress-flow" value="{{ $defaultFlow }}">
                                    <input type="hidden" name="target_institution_id" id="target_institution_id">
                                    <input type="hidden" name="target_division_id" id="target_division_id">

                                    @if($showInspectionForm)
                                    <div class="row g-3">
                                        <div class="col-12"><h6 class="fw-semibold">Upload Dokumen Pemeriksaan</h6></div>
                                        <div class="col-md-4">
                                            <label class="form-label">No Dokumen Pemeriksaan</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="inspection_doc_number"
                                                placeholder="Masukkan nomor dokumen"
                                                value="{{ old('inspection_doc_number', $inspectionPrefill['doc_number'] ?? '') }}"
                                            >
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Dokumen Pemeriksaan</label>
                                            <input
                                                type="date"
                                                class="form-control"
                                                name="inspection_doc_date"
                                                value="{{ old('inspection_doc_date', $inspectionPrefill['doc_date'] ?? '') }}"
                                            >
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload File</label>
                                            <input type="file" class="form-control" name="inspection_files[]" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple>
                                            @if(!empty($inspectionEvidence))
                                                <div class="mt-2">
                                                    <small class="text-muted d-block">File tersimpan:</small>
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($inspectionEvidence as $ev)
                                                            @if(!empty($ev['url']))
                                                                <li><a href="{{ $ev['url'] }}" target="_blank">{{ $ev['name'] ?? 'Lampiran' }}</a></li>
                                                            @else
                                                                <li>{{ $ev['name'] ?? 'Lampiran' }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12"><h6 class="fw-semibold">Input Kesimpulan Gelar Perkara</h6></div>
                                        <div class="col-12">
                                            <textarea
                                                class="form-control"
                                                name="inspection_conclusion"
                                                rows="4"
                                                placeholder="Tuliskan kesimpulan"
                                            >{{ old('inspection_conclusion', $inspectionPrefill['conclusion'] ?? '') }}</textarea>
                                        </div>

                                        <div class="col-12 d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-primary mt-2 progress-action" data-action="save" data-flow="inspection">Simpan</button>
                                            <button type="button" class="btn btn-success mt-2 progress-action" data-action="complete" data-flow="inspection">Simpan dan Selesai</button>
                                            <button type="button" class="btn btn-info mt-2" id="transfer-btn" data-flow="inspection">Simpan dan Limpah</button>
                                            <a href="{{ route('pelaporan.index') }}" class="btn btn-warning mt-2">Kembali</a>
                                        </div>
                                    </div>
                                    @endif

                                    @if($showInvestigationForm)
                                    <div class="row g-3">
                                        <div class="col-12 d-flex align-items-center justify-content-between">
                                            <h6 class="fw-semibold mb-0">Administrasi Penyidikan</h6>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adminDocModal">Tambah Dokumen</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive admin-doc-scroll">
                                                <table class="table table-striped align-middle admin-doc-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Dokumen</th>
                                                        <th>No Dokumen</th>
                                                        <th>Tanggal Dokumen</th>
                                                        <th>Berkas</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="adminDocTableBody" data-count="{{ count($adminDocuments ?? []) }}">
                                                    @forelse($adminDocuments ?? [] as $idx => $doc)
                                                        <tr id="admin-doc-row-{{ $idx }}">
                                                            <td>{{ $doc['name'] ?? '-' }}</td>
                                                            <td>{{ $doc['number'] ?? '-' }}</td>
                                                            <td>{{ $doc['date'] ?? '-' }}</td>
                                                            <td>
                                                                @if(!empty($doc['file_url']))
                                                                    <a href="{{ $doc['file_url'] }}" target="_blank" class="text-decoration-underline">
                                                                        {{ $doc['file_name'] ?? basename($doc['file_url']) }}
                                                                    </a>
                                                                @else
                                                                    {{ $doc['file_name'] ?? '-' }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm" data-row="admin-doc-row-{{ $idx }}">Hapus</button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr class="admin-placeholder">
                                                            <td colspan="5" class="text-center">Belum ada dokumen administrasi</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                </table>
                                            </div>
                                            <div id="adminDocHiddenInputs" style="display:none">
                                                @foreach($adminDocuments ?? [] as $idx => $doc)
                                                    <div id="hidden-admin-doc-row-{{ $idx }}">
                                                        <input type="hidden" name="admin_documents[{{ $idx }}][name]" value="{{ $doc['name'] ?? '' }}">
                                                        <input type="hidden" name="admin_documents[{{ $idx }}][number]" value="{{ $doc['number'] ?? '' }}">
                                                        <input type="hidden" name="admin_documents[{{ $idx }}][date]" value="{{ $doc['date'] ?? '' }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-12"><h6 class="fw-semibold">Upload Dokumen Sidang</h6></div>
                                        <div class="col-md-4">
                                            <label class="form-label">No Dokumen Sidang</label>
                                            <input type="text" class="form-control" name="trial_doc_number" placeholder="Masukkan nomor dokumen">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Dokumen Sidang</label>
                                            <input type="date" class="form-control" name="trial_doc_date">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload File</label>
                                            <input type="file" class="form-control" name="trial_file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                        </div>

                                        <div class="col-12"><h6 class="fw-semibold">Putusan</h6></div>
                                        <div class="col-12">
                                            <textarea class="form-control" name="trial_decision" rows="4" placeholder="Tuliskan putusan"></textarea>
                                        </div>

                                        <div class="col-12 d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-primary mt-2 progress-action" data-action="save" data-flow="investigation">Simpan</button>
                                            <button type="button" class="btn btn-success mt-2 progress-action" data-action="complete" data-flow="investigation">Simpan dan Selesai</button>
                                            <a href="{{ route('pelaporan.index') }}" class="btn btn-warning mt-2">Kembali</a>
                                        </div>
                                    </div>
                                    @endif
                                </form>
                            @endif
                        </div>

                        @endif

                        <div class="tab-pane fade" id="tab-timeline" role="tabpanel">
                            <h5 class="mt-1 mb-3"><i class="fa fa-route me-2"></i>Timeline Penanganan</h5>
                            @include('components.timeline', ['items' => $journeys])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Pilih Tujuan Limpah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Institusi Tujuan</label>
                    <select id="transfer-institution" class="form-control">
                        <option value="">-- Pilih Institusi --</option>
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit/Sub-bagian Tujuan</label>
                    <select id="transfer-division" class="form-control">
                        <option value="">-- Pilih Unit/Sub-bagian --</option>
                        @foreach($investigationDivisions as $divisionOption)
                            <option value="{{ $divisionOption->id }}">{{ $divisionOption->parent ? $divisionOption->parent->name . ' - ' : '' }}{{ $divisionOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="alert alert-warning mb-0">Limpahkan hanya ke unit yang memiliki kewenangan penyidikan.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="transfer-confirm">Simpan dan Limpah</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminDocModal" tabindex="-1" aria-labelledby="adminDocModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adminDocModalLabel">Input Dokumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Dokumen</label>
          <input type="text" class="form-control" id="admin-doc-name">
        </div>
        <div class="mb-3">
          <label class="form-label">No Dokumen</label>
          <input type="text" class="form-control" id="admin-doc-number">
        </div>
        <div class="mb-3">
          <label class="form-label">Tanggal Dokumen</label>
          <input type="date" class="form-control" id="admin-doc-date">
        </div>
        <div class="mb-3" id="admin-doc-file-wrapper">
          <label class="form-label">Upload File</label>
          <input type="file" class="form-control" id="admin-doc-file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="admin-doc-save">Simpan</button>
      </div>
    </div>
  </div>
</div>
{{-- Journey Modal --}}
<div class="modal fade" id="journeyModal" tabindex="-1" aria-labelledby="journeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="journeyModalLabel">Tambah Tahapan Penanganan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('reports.journeys.store', $report->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="journey-type">Jenis Tahapan</label>
                            <select name="type" id="journey-type" class="form-control" required>
                                <option value="">-- Pilih Tahapan --</option>
                                @foreach($journeyTypes as $type)
                                    <option value="{{ $type->value }}" @selected(old('type') === $type->value)>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="limpah-institution-field" hidden>
                            <label class="form-label fw-semibold" for="institution-target">Institusi Tujuan</label>
                            <select name="institution_target_id" id="institution-target" class="form-control">
                                <option value="">-- Pilih Institusi --</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected((int) old('institution_target_id') === $institution->id)>
                                        {{ $institution->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="limpah-division-field" hidden>
                            <label class="form-label fw-semibold" for="subdivision-target">Unit/Sub-bagian Tujuan</label>
                            <select name="subdivision_target_id" id="subdivision-target" class="form-control">
                                <option value="">-- Pilih Unit/Sub-bagian --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">
                                        {{ $division->parent ? $division->parent->name . ' - ' : '' }}{{ $division->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="journey-description">Deskripsi Proses</label>
                            <textarea
                                name="description"
                                id="journey-description"
                                rows="3"
                                class="form-control"
                                placeholder="Tuliskan ringkasan tahapan penanganan..."
                                required
                            >{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="journey-files">Upload Bukti Pendukung</label>
                            <input
                                type="file"
                                name="files[]"
                                id="journey-files"
                                class="form-control"
                                multiple
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                            >
                            <small class="form-text">
                                *Bisa unggah lebih dari satu file (foto, dokumen, atau bukti lainnya).
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Simpan Tahapan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('components.file-viewer')
@endsection

@push('styles')
<style>
    .report-detail-card .card-header {
        border-bottom: 1px solid var(--bs-border-color);
    }

    .report-detail-body {
        color: var(--bs-body-color);
    }

    .report-detail-body .badge {
        color: inherit;
    }

    #journeyModal .modal-content {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
    }

    #journeyModal .modal-body,
    #journeyModal .modal-footer {
        background-color: inherit;
    }

    #journeyModal .form-label {
        color: var(--bs-body-color);
    }

    #journeyModal .form-text {
        color: var(--bs-secondary-color);
    }

    /* Admin doc table responsive */
    .admin-doc-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .admin-doc-table th,
    .admin-doc-table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .admin-doc-table th,
        .admin-doc-table td {
            white-space: normal;
        }

        .admin-doc-table .btn {
            padding: 4px 8px;
        }
    }

    /* Timeline dot behind sidebar */
    .cd-timeline::before {
        z-index: 0;
    }

    .cd-timeline-img {
        z-index: 1;
    }
</style>
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('journeyModal');
    const openModal = @json(session('open_modal'));

    if (openModal === 'journey' && modalEl) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    const typeSelect       = document.getElementById('journey-type');
    const institutionField = document.getElementById('limpah-institution-field');
    const divisionField    = document.getElementById('limpah-division-field');

    // NILAI LIMPAH BENAR SESUAI ENUM
    const limpahValue = '{{ \App\Enums\ReportJourneyType::TRANSFER->value }}';

    const toggle = () => {
        const isLimpah = typeSelect.value === limpahValue;

        institutionField.hidden = !isLimpah;
        divisionField.hidden    = !isLimpah;

        institutionField.querySelector('select').required = isLimpah;
        divisionField.querySelector('select').required    = isLimpah;
    };

    typeSelect.addEventListener('change', toggle);
    toggle();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var adminIndex = (function() {
    var table = document.getElementById('adminDocTableBody');
    var initial = table ? parseInt(table.getAttribute('data-count') || '0', 10) : 0;
    return isNaN(initial) ? 0 : initial;
  })();
  var bodyEl = document.getElementById('adminDocTableBody');
  var hiddenEl = document.getElementById('adminDocHiddenInputs');
  var saveBtn = document.getElementById('admin-doc-save');
  var adminDocModal = document.getElementById('adminDocModal');

  function removePlaceholder() {
    var placeholder = bodyEl ? bodyEl.querySelector('.admin-placeholder') : null;
    if (placeholder) {
      placeholder.remove();
    }
  }

  function addPlaceholderIfEmpty() {
    if (!bodyEl) return;
    var hasRow = bodyEl.querySelector('tr');
    if (hasRow) return;
    var placeholder = document.createElement('tr');
    placeholder.className = 'admin-placeholder';
    placeholder.innerHTML = '<td colspan="5" class="text-center">Belum ada dokumen administrasi</td>';
    bodyEl.appendChild(placeholder);
  }

  function freshFileInput() {
    var wrapper = document.getElementById('admin-doc-file-wrapper');
    if (!wrapper) return null;
    wrapper.querySelectorAll('input[type="file"]').forEach(function(el) { el.remove(); });
    var fresh = document.createElement('input');
    fresh.type = 'file';
    fresh.className = 'form-control';
    fresh.id = 'admin-doc-file';
    fresh.accept = '.jpg,.jpeg,.png,.pdf,.doc,.docx';
    wrapper.appendChild(fresh);
    return fresh;
  }

  function ensureFileInputVisible() {
    freshFileInput();
  }

  function appendAdminRow(data, fileInputEl) {
    var rowId = 'admin-doc-row-' + adminIndex;
    var fileName = fileInputEl && fileInputEl.files && fileInputEl.files[0] ? fileInputEl.files[0].name : '-';
    var tr = document.createElement('tr');
    tr.id = rowId;
    tr.innerHTML = '<td>' + (data.name || '-') + '</td>' +
                   '<td>' + (data.number || '-') + '</td>' +
                   '<td>' + (data.date || '-') + '</td>' +
                   '<td>' + fileName + '</td>' +
                   '<td><button type="button" class="btn btn-danger btn-sm" data-row="' + rowId + '">Hapus</button></td>';
    removePlaceholder();
    bodyEl.appendChild(tr);

    var hidden = document.createElement('div');
    hidden.id = 'hidden-' + rowId;
    hidden.innerHTML = '' +
      '<input type="hidden" name="admin_documents[' + adminIndex + '][name]" value="' + (data.name || '') + '">' +
      '<input type="hidden" name="admin_documents[' + adminIndex + '][number]" value="' + (data.number || '') + '">' +
      '<input type="hidden" name="admin_documents[' + adminIndex + '][date]" value="' + (data.date || '') + '">';
    if (fileInputEl) {
      fileInputEl.name = 'admin_documents[' + adminIndex + '][file]';
      fileInputEl.style.display = 'none';
      fileInputEl.id = '';
      if (fileInputEl.parentElement) {
        fileInputEl.parentElement.removeChild(fileInputEl);
      }
      hidden.appendChild(fileInputEl);
    }
    hiddenEl.appendChild(hidden);
    adminIndex++;
  }

  if (adminDocModal) {
    adminDocModal.addEventListener('shown.bs.modal', function() {
      freshFileInput();
    });
    adminDocModal.addEventListener('hidden.bs.modal', function() {
      freshFileInput();
    });
  }

  if (saveBtn) {
    saveBtn.addEventListener('click', function() {
      var nameEl = document.getElementById('admin-doc-name');
      var numberEl = document.getElementById('admin-doc-number');
      var dateEl = document.getElementById('admin-doc-date');
      var fileEl = document.getElementById('admin-doc-file');
      var data = {
        name: (nameEl.value || '').trim(),
        number: (numberEl.value || '').trim(),
        date: (dateEl.value || '').trim()
      };
      if (!data.name || !data.number || !data.date || !fileEl || !fileEl.files || !fileEl.files[0]) {
        alert('Isi nama, nomor, tanggal, dan pilih file dokumen administrasi.');
        return;
      }
      appendAdminRow(data, fileEl);
      var modalEl = document.getElementById('adminDocModal');
      bootstrap.Modal.getOrCreateInstance(modalEl).hide();
      nameEl.value = '';
      numberEl.value = '';
      dateEl.value = '';
      freshFileInput();
    });
  }

  if (bodyEl) {
    bodyEl.addEventListener('click', function(e) {
      var btn = e.target.closest('button.btn-danger');
      if (!btn) return;
      var rowId = btn.getAttribute('data-row');
      var row = document.getElementById(rowId);
      var hidden = document.getElementById('hidden-' + rowId);
      if (row) row.remove();
      if (hidden) hidden.remove();
      if (bodyEl && bodyEl.querySelectorAll('tr').length === 0) {
        addPlaceholderIfEmpty();
      }
    });
  }

  addPlaceholderIfEmpty();
  ensureFileInputVisible();
  
  var progressForm = document.getElementById('progressForm');
  var actionInput = document.getElementById('progress-action');
  var flowInput = document.getElementById('progress-flow');
  var targetInstitutionInput = document.getElementById('target_institution_id');
  var targetDivisionInput = document.getElementById('target_division_id');
  var transferModalEl = document.getElementById('transferModal');
  var transferInstitution = document.getElementById('transfer-institution');
  var transferDivision = document.getElementById('transfer-division');
  var transferConfirm = document.getElementById('transfer-confirm');
  var transferBtn = document.getElementById('transfer-btn');
  var actionButtons = document.querySelectorAll('.progress-action');

  function showConfirm(message, callback) {
    if (window.Swal) {
      Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.isConfirmed) callback();
      });
    } else if (confirm(message)) {
      callback();
    }
  }

  function submitProgress(action, flow) {
    if (!progressForm || !actionInput || !flowInput) return;
    actionInput.value = action;
    flowInput.value = flow;
    progressForm.submit();
  }

  actionButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
      var action = btn.getAttribute('data-action');
      var flow = btn.getAttribute('data-flow') || 'inspection';
      var messages = {
        save: 'Simpan data progres laporan?',
        complete: 'Simpan dan tandai laporan selesai?',
        transfer: 'Simpan dan limpahkan laporan?'
      };
      showConfirm(messages[action] || 'Lanjutkan proses?', function() {
        submitProgress(action, flow);
      });
    });
  });

  if (transferBtn && transferModalEl) {
    transferBtn.addEventListener('click', function() {
      var flow = transferBtn.getAttribute('data-flow') || 'inspection';
      flowInput.value = flow;
      bootstrap.Modal.getOrCreateInstance(transferModalEl).show();
    });
  }

  if (transferConfirm) {
    transferConfirm.addEventListener('click', function() {
      if (!transferDivision || !transferDivision.value) {
        transferDivision && transferDivision.focus();
        return;
      }
      targetInstitutionInput.value = transferInstitution ? transferInstitution.value : '';
      targetDivisionInput.value = transferDivision.value;
      showConfirm('Simpan dan limpahkan laporan?', function() {
        submitProgress('transfer', 'inspection');
      });
    });
  }

  // Reset file inputs secara manual
  function resetFileInputs() {
    var inspectionFilesInput = document.querySelector('input[name="inspection_files[]"]');
    var trialFileInput = document.querySelector('input[name="trial_file"]');
    if (inspectionFilesInput) {
      inspectionFilesInput.value = '';
    }
    if (trialFileInput) {
      trialFileInput.value = '';
    }
  }

  // Reset form setelah page selesai dimuat (jika ada redirect/reload)
  window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
      resetFileInputs();
    }
  });

  // Juga reset saat tab berubah
  document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function() {
      resetFileInputs();
    });
  });
});
</script>
@endsection
