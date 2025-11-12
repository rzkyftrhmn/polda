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
          <div class="row g-3">
            <div class="col-md-6">
              <p class="mb-1"><b>Status Laporan:</b> {{ $report->status }}</p>
              <p class="mb-1"><b>Waktu Kejadian:</b> {{ optional($report->incident_datetime)->format('d M Y H:i') }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-1"><b>Kategori:</b> {{ optional($report->category)->name ?? '-' }}</p>
              <p class="mb-1"><b>Lokasi:</b> {{ $report->address_detail ?? '-' }}</p>
            </div>
          </div>
          <p class="mt-3">{{ $report->description }}</p>

          <hr>
          <h5 class="mt-4 mb-3"><i class="fa fa-route me-2"></i>Tahapan Penanganan</h5>

          @forelse ($journeys as $journey)
            <div class="card border mb-3">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                  <span class="badge bg-primary">{{ $journey->type }}</span>
                  <small class="text-muted">{{ optional($journey->created_at)->format('d M Y H:i') }}</small>
                </div>
                <p class="mt-3 mb-2">{{ $journey->description }}</p>

                @if ($journey->evidences->isNotEmpty())
                  <div class="mt-3">
                    <h6 class="fw-semibold mb-2">Bukti Pendukung</h6>
                    <ul class="list-unstyled mb-0">
                      @foreach ($journey->evidences as $evidence)
                        <li class="mb-1">
                          <a href="{{ $evidence->file_url }}" class="text-decoration-none" target="_blank" rel="noopener">
                            <i class="fa fa-paperclip me-2"></i>{{ basename($evidence->file_url) }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @endif
              </div>
            </div>
          @empty
            <div class="alert alert-info p-2 mb-4">
              Belum ada tahapan penanganan yang tercatat untuk laporan ini.
            </div>
          @endforelse

          @if($journeys instanceof \Illuminate\Contracts\Pagination\Paginator && $journeys->hasPages())
            <div class="d-flex justify-content-center mt-4">
              {{ $journeys->links('pagination::bootstrap-5') }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="journeyModal" tabindex="-1" aria-labelledby="journeyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="journeyModalLabel">Tambah Tahapan Penanganan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('reports.journeys.store', $report->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Jenis Tahapan</label>
              <select name="type" class="form-control" required>
                <option value="">-- Pilih Tahapan --</option>
                <option value="PEMERIKSAAN" @selected(old('type') === 'PEMERIKSAAN')>Pemeriksaan Awal</option>
                <option value="LIMPAH" @selected(old('type') === 'LIMPAH')>Pelimpahan Berkas</option>
                <option value="SIDANG" @selected(old('type') === 'SIDANG')>Sidang Kode Etik</option>
                <option value="SELESAI" @selected(old('type') === 'SELESAI')>Penyelesaian</option>
              </select>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-semibold">Deskripsi Proses</label>
              <textarea name="description" rows="3" class="form-control" placeholder="Tuliskan ringkasan tahapan penanganan..." required>{{ old('description') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-semibold">Upload Bukti Pendukung</label>
              <input
                type="file"
                name="files[]"
                class="form-control"
                multiple
                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
              >
              <small class="text-muted">
                *Bisa upload lebih dari satu file (foto, dokumen, atau bukti lainnya).
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
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (@json($errors->any())) {
            var modalEl = document.getElementById('journeyModal');

            if (modalEl && typeof bootstrap !== 'undefined') {
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }
    });
</script>
@endsection
@endsection
