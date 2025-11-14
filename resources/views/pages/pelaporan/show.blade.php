@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow-sm border-0 report-detail-card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Laporan Pelanggaran</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#journeyModal">
                        <i class="fa fa-plus me-1"></i> Tambah Tahapan Penanganan
                    </button>
                </div>

                <div class="card-body report-detail-body">
                    @php
                        // dd($report);
                        $incidentAt   = $report->incident_datetime?->format('d M Y H:i');
                        $finishedAt   = $report->finish_time?->format('d M Y H:i');
                        $categoryName = $report->category?->name;
                        $provinceName = $report->province?->name;
                        $cityName     = $report->city?->name;
                        $districtName = $report->district?->name;
                        $suspectName  = $report->suspects?->pluck('name')->join(', ');
                        $description   = $report->suspects?->pluck('description')->join(', ');
                    @endphp

                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                        <div>
                            <h4 class="mb-2">{{ $report->title }}</h4>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-light text-dark">Kode: {{ $report->code ?? '-' }}</span>
                                <span class="badge bg-secondary text-uppercase">Status: {{ $statusLabel ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="text-md-end">
                            <p class="mb-1"><strong>Kategori:</strong> {{ $categoryName ?? '-' }}</p>
                            <p class="mb-0"><strong>Lokasi:</strong> {{ $report->address_detail ?? '-' }}</p>
                        </div>
                    </div>

                    @php
                        $metadata = [
                            ['label' => 'Nama Terlapor', 'value' => $suspectName ?? '-'],
                            ['label' => 'Deskripsi Terlapor', 'value' => $description ?? '-'],
                            ['label' => 'Status', 'value' => $report->status ?? '-'],
                            ['label' => 'Provinsi', 'value' => $provinceName ?? '-'],
                            ['label' => 'Waktu Kejadian', 'value' => $incidentAt ?? '-'],
                            ['label' => 'Kota/Kabupaten', 'value' => $cityName ?? '-'],
                            ['label' => 'Waktu Selesai', 'value' => $finishedAt ?? '-'],
                            ['label' => 'Kecamatan', 'value' => $districtName ?? '-'],
                        ];
                    @endphp

                    <div class="row g-3">
                        @foreach($metadata as $item)
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>{{ $item['label'] }}:</strong> {{ $item['value'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <h6 class="fw-semibold mb-2">Deskripsi Laporan</h6>
                        <p class="mb-0">{!! nl2br(e($report->description)) !!}</p>
                    </div>

                    <hr>
                    <h5 class="mt-4 mb-3">
                        <i class="fa fa-route me-2"></i>Timeline Penanganan
                    </h5>

                    @include('components.timeline', ['items' => $journeys])

                </div>
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
                            <select name="type" id="journey-type" class="form-select" required>
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
                            <select name="institution_target_id" id="institution-target" class="form-select">
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
                            <select name="subdivision_target_id" id="subdivision-target" class="form-select">
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
                            <small class="text-muted">
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
    .report-detail-card .card-header h5 {
        color: #ffffff;
    }

    .report-detail-body {
        color: var(--bs-body-color);
        background-color: transparent;
    }

    .report-detail-body h4,
    .report-detail-body p,
    .report-detail-body .badge,
    .report-detail-body .list-group-item,
    .report-detail-body .list-group-item .small,
    .report-detail-body .list-group-item span,
    .report-detail-body .list-group-item button,
    .report-detail-body .text-muted,
    .report-detail-body .text-body {
        color: inherit;
    }

    .report-detail-body .list-group-item {
        background-color: transparent;
    }

    [data-theme-version="dark"] .report-detail-body {
        color: #f1f5f9;
    }

    [data-theme-version="dark"] .report-detail-body .text-muted {
        color: rgba(241, 245, 249, 0.7) !important;
    }

    [data-theme-version="dark"] .report-detail-body .list-group-item {
        background-color: rgba(15, 23, 42, 0.35);
    }

    [data-theme-version="dark"] .report-detail-body .badge.bg-light {
        color: #0f172a;
    }
</style>
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const modalEl = document.getElementById('journeyModal');
    const openModal = @json(session('open_modal') ?? ($errors->any() ? 'journey' : null));

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

@endsection
