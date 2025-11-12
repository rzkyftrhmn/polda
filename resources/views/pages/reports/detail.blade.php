@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Laporan Pelanggaran</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#journeyModal">
                        <i class="fa fa-plus me-1"></i> Tambah Tahapan Penanganan
                    </button>
                </div>

                <div class="card-body">
                    <h4 class="mb-3">{{ $report->title }}</h4>
                    @php($statusLabel = \App\Enums\ReportJourneyType::tryFrom($report->status)?->label() ?? $report->status)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Status Laporan:</strong> {{ $statusLabel }}</p>
                            <p class="mb-1"><strong>Waktu Kejadian:</strong> {{ optional($report->incident_datetime)->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Kategori:</strong> {{ optional($report->category)->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Lokasi:</strong> {{ $report->address_detail ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="mt-3">{{ $report->description }}</p>

                    <hr>
                    <h5 class="mt-4 mb-3"><i class="fa fa-route me-2"></i>Timeline Penanganan</h5>

                    @include('components.timeline', ['items' => $journeys])

                    <hr class="my-5">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fa fa-sticky-note me-2"></i>Catatan Tindak Lanjut</h5>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#followUpModal">
                            <i class="fa fa-plus me-1"></i> Tambah Catatan
                        </button>
                    </div>

                    @forelse($followUps as $followUp)
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <span class="fw-semibold">{{ optional($followUp->user)->name ?? 'Petugas' }}</span>
                                    <small class="text-muted">{{ optional($followUp->created_at)->format('d M Y H:i') }}</small>
                                </div>
                                <p class="mt-3 mb-0">{{ $followUp->notes }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info p-2 mb-0">
                            Belum ada catatan tindak lanjut untuk laporan ini.
                        </div>
                    @endforelse

                    @if($followUps->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $followUps->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
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
                                    <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
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
                            <label class="form-label fw-semibold" for="division-target">Divisi Tujuan</label>
                            <select name="division_target_id" id="division-target" class="form-select">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisions as $division)
                                    <option
                                        value="{{ $division->id }}"
                                        data-institution="{{ $division->institution_id }}"
                                        @selected((int) old('division_target_id') === $division->id)
                                    >
                                        {{ $division->name }}
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
                            <small class="text-muted">*Bisa unggah lebih dari satu file (foto, dokumen, atau bukti lainnya).</small>
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

{{-- Follow Up Modal --}}
<div class="modal fade" id="followUpModal" tabindex="-1" aria-labelledby="followUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="followUpModalLabel">Tambah Catatan Tindak Lanjut</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('reports.followups.store', $report->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-semibold" for="followup-notes">Catatan</label>
                    <textarea
                        name="notes"
                        id="followup-notes"
                        rows="4"
                        class="form-control"
                        placeholder="Tuliskan tindak lanjut internal..."
                        required
                    >{{ old('notes') }}</textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('components.file-viewer')
@include('components.flipbook-viewer')
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfobject@2.2.8/pdfobject.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let openModal = @json(session('open_modal'));
        const hasErrors = @json($errors->any());
        const journeyModalEl = document.getElementById('journeyModal');
        const followUpModalEl = document.getElementById('followUpModal');

        if (!openModal && hasErrors) {
            openModal = 'journey';
        }

        if (openModal) {
            const targetModal = openModal === 'followup' ? followUpModalEl : journeyModalEl;
            if (targetModal) {
                bootstrap.Modal.getOrCreateInstance(targetModal).show();
            }
        }

        const typeSelect = document.getElementById('journey-type');
        const institutionField = document.getElementById('limpah-institution-field');
        const divisionField = document.getElementById('limpah-division-field');
        const divisionSelect = document.getElementById('division-target');
        const institutionSelect = document.getElementById('institution-target');
        const limpahValue = '{{ \App\Enums\ReportJourneyType::TRANSFER->value }}';

        const toggleLimpahFields = function () {
            const isLimpah = typeSelect && typeSelect.value === limpahValue;
            [institutionField, divisionField].forEach(function (field) {
                if (!field) { return; }
                field.hidden = !isLimpah;
                Array.prototype.forEach.call(field.querySelectorAll('select'), function (select) {
                    select.required = isLimpah;
                });
            });
            if (!isLimpah && divisionSelect) {
                divisionSelect.value = '';
            }
        };

        const filterDivisions = function () {
            if (!divisionSelect || !institutionSelect) {
                return;
            }

            const selectedInstitution = institutionSelect.value;
            Array.prototype.forEach.call(divisionSelect.options, function (option) {
                if (!option.dataset.institution) {
                    option.hidden = false;
                    return;
                }

                option.hidden = selectedInstitution && option.dataset.institution !== selectedInstitution;
            });

            if (divisionSelect.selectedOptions.length && divisionSelect.selectedOptions[0].hidden) {
                divisionSelect.value = '';
            }
        };

        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                toggleLimpahFields();
                filterDivisions();
            });
        }

        if (institutionSelect) {
            institutionSelect.addEventListener('change', filterDivisions);
        }

        toggleLimpahFields();
        filterDivisions();

    });
</script>
@endsection
    