@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-calendar me-1"></i>Detail Event</div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><p class="mb-1"><strong>Nama Event:</strong> {{ $event->name ?? '-' }}</p></div>
                            <div class="col-md-6"><p class="mb-1"><strong>Lokasi:</strong> {{ $event->location ?? '-' }}</p></div>
                            <div class="col-md-6"><p class="mb-1"><strong>Mulai:</strong> {{ $event->start_at?->format('d M Y H:i') ?? '-' }}</p></div>
                            <div class="col-md-6"><p class="mb-1"><strong>Selesai:</strong> {{ $event->end_at?->format('d M Y H:i') ?? '-' }}</p></div>
                            <div class="col-12"><p class="mb-1"><strong>Deskripsi:</strong><br>{{ $event->description ?? '-' }}</p></div>
                            <div class="col-12"><hr></div>
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-0">Peserta</h6>
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-warning btn-sm">Edit</a>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Unit</th>
                                            <th>Status Kehadiran</th>
                                            <th>Status Upload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($event->participants as $p)
                                            @php $hasUpload = in_array($p->division_id, $uploadedDivisionIds ?? []); @endphp
                                            <tr>
                                                <td>{{ $p->division?->name ?? '-' }}</td>
                                                <td>{{ $p->is_required ? 'Wajib' : 'Opsional' }}</td>
                                                <td>
                                                    @if($hasUpload)
                                                        <span class="badge bg-success">Sudah upload</span>
                                                    @else
                                                        <span class="badge bg-secondary">Belum upload</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada peserta.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('events.index') }}" class="btn btn-warning">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-xl-6">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-upload me-1"></i>Upload Bukti Kegiatan</div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <form id="proofForm" action="{{ route('events.proofs.store', $event) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">File Bukti</label>
                                <input type="file" name="proof_files[]" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple required>
                                <small class="text-muted">Bisa unggah lebih dari satu file.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">File Laporan (Opsional)</label>
                                <input type="file" name="report_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <small class="text-muted">Opsional, unggah dokumen laporan kegiatan.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Keterangan singkat"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Unggah</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-list me-1"></i>History Bukti</div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div><strong>Persentase Upload:</strong> {{ $percentageUploaded }}% ({{ $uploadedCount }}/{{ $totalParticipants }})</div>
                        </div>
                        <div class="list-group">
                            @forelse($proofGroups as $idx => $group)
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong>{{ $group['user_name'] ?? 'Unknown' }}</strong>
                                        <span class="text-muted">— {{ $group['division_name'] ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#proofModal" data-files='@json($group["files"])'>Lihat File ({{ count($group['files']) }})</button>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item">Belum ada bukti diunggah.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="proofModalLabel">Daftar Bukti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="proofModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById('proofModal');
  if (modal) {
    modal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var files = [];
      try { files = JSON.parse(button?.getAttribute('data-files') || '[]'); } catch (e) {}
      var body = modal.querySelector('#proofModalBody');
      var html = '';
  files.forEach(function(f) {
    var meta = '<small class="text-muted">' + (f.created_at || '') + '</small>';
    var desc = f.description ? ('<div class="mt-1">' + f.description + '</div>') : '';
    html += '<div class="mb-2">' + meta + desc + '<a href="' + f.path + '" target="_blank" class="text-decoration-underline">Lihat File</a></div>';
  });
      body.innerHTML = html || '<div class="text-muted">Tidak ada file.</div>';
    });
  }

  var form = document.getElementById('proofForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var proceed = function() { form.submit(); };
      if (window.Swal) {
        Swal.fire({
          title: 'Unggah bukti kegiatan?',
          text: 'File akan disimpan dan ditampilkan pada history.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Unggah',
          cancelButtonText: 'Batal'
        }).then(function(result) { if (result.isConfirmed) proceed(); });
      } else {
        if (confirm('Unggah bukti kegiatan?')) proceed();
      }
    });
  }
});
</script>
@endsection
