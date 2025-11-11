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
          <h4 class="mb-3">{{ $report['title'] }}</h4>
          <p><b>Jenis Pelanggaran:</b> {{ $report['category'] }}</p>
          <p><b>Lokasi Kejadian:</b> {{ $report['address'] }}</p>
          <p class="mt-3">{{ $report['description'] }}</p>

          <hr>
          <h5 class="mt-4 mb-3"><i class="fa fa-route me-2"></i>Tahapan Penanganan</h5>
          <div class="alert alert-info p-2 mb-4">
            Belum ada tahapan penanganan yang tercatat untuk laporan ini.
          </div>
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

      <form action="{{ route('journeys.store', $report['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Jenis Tahapan</label>
              <select name="type" class="form-control" required>
                <option value="">-- Pilih Tahapan --</option>
                <option value="PEMERIKSAAN">Pemeriksaan Awal</option>
                <option value="LIMPAH">Pelimpahan Berkas</option>
                <option value="SIDANG">Sidang Kode Etik</option>
                <option value="SELESAI">Penyelesaian</option>
              </select>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-semibold">Deskripsi Proses</label>
              <textarea name="description" rows="3" class="form-control" placeholder="Tuliskan ringkasan tahapan penanganan..." required></textarea>
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
@endsection
