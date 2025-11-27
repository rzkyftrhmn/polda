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
                                @if($isAdmin)
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endif
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
        <div class="col-xl-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-upload" type="button" role="tab">
                        <i class="fa fa-upload me-2"></i>Upload Bukti Kegiatan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-history" type="button" role="tab">
                        <i class="fa fa-list me-2"></i>History Upload Bukti
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-4">
                <div class="tab-pane fade show active" id="tab-upload" role="tabpanel">
                    <div class="filter cm-content-box box-primary">
                        <div class="content-title SlideToolHeader">
                            <div class="cpa"><i class="fa-solid fa-upload me-1"></i>Upload Bukti Kegiatan</div>
                        </div>
                        <div class="cm-content-body form excerpt">
                            <div class="card-body">
                                @if($isAdmin || $isParticipant)
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
                                @else
                                    <div class="alert alert-warning mb-0">Hanya peserta yang dapat mengunggah bukti kegiatan.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-history" role="tabpanel">
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
                                    <div class="me-3">
                                        <div><strong>{{ $group['user_name'] ?? 'Unknown' }}</strong> <span class="text-muted">— {{ $group['division_name'] ?? '-' }}</span></div>
                                        <div class="mt-1">{{ $group['description'] ?? '-' }}</div>
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            @foreach(array_slice(array_unique(array_map(function($f){ return $f['type'] ?? 'file'; }, $group['files'])),0,3) as $t)
                                                <span class="badge bg-body-secondary text-body text-uppercase">{{ $t }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-info d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#proofModal" data-files='@json($group["files"])'>
                                            <i class="fa fa-folder-open"></i>
                                            <span>Lihat File</span>
                                            <span class="badge rounded-pill bg-info-subtle text-info">{{ count($group['files']) }}</span>
                                        </button>
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
    </div>
</div>
<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center gap-2" id="proofModalLabel">
          <i class="fa fa-folder-open"></i>
          <span>Daftar Bukti</span>
          <span id="proofModalCount" class="badge bg-primary-subtle text-primary">0</span>
        </h5>
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
      var countEl = modal.querySelector('#proofModalCount');
      if (countEl) { countEl.textContent = (files || []).length; }
      var grid = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">';
      (files || []).forEach(function(f) {
        var name = (f.path || '').split('/').pop();
        var fileType = (f.type || '').toLowerCase();
        var lowerPath = (f.path || '').toLowerCase();
        var isImage = /\.(jpg|jpeg|png|gif|webp)$/.test(lowerPath);
        var icon = 'fa-file';
        if (!isImage) {
          if (/\.pdf$/.test(lowerPath)) icon = 'fa-file-pdf';
          else if (/\.docx?$/.test(lowerPath)) icon = 'fa-file-word';
        }
        var preview = '';
        if (isImage) {
          preview = '<img src="' + f.path + '" class="card-img-top" style="max-height:320px;object-fit:contain;background:#f8f9fa">';
        } else {
          preview = '<div class="d-flex align-items-center justify-content-center bg-body-secondary" style="height:180px"><i class="fa ' + icon + ' fa-3x"></i></div>';
        }
        var created = f.created_at ? ('<small class="text-muted">' + f.created_at + '</small>') : '';
        var desc = f.description ? ('<div class="mt-1">' + f.description + '</div>') : '';
        grid += '<div class="col">'
          +   '<div class="card h-100 shadow-sm">'
          +     preview
          +     '<div class="card-body">'
          +       '<div class="d-flex align-items-center justify-content-between">'
          +         '<div class="fw-semibold text-truncate" title="' + (name || '') + '">' + (name || 'Lampiran') + '</div>'
          +         '<span class="badge bg-body-secondary text-body text-uppercase">' + (fileType || 'file') + '</span>'
          +       '</div>'
          +       '<div class="mt-1">' + created + '</div>'
          +       desc
          +     '</div>'
          +     '<div class="card-footer bg-transparent"><a href="' + f.path + '" target="_blank" class="btn btn-sm btn-primary w-100">Buka</a></div>'
          +   '</div>'
          + '</div>';
      });
      grid += '</div>';
      body.innerHTML = (files && files.length) ? grid : '<div class="text-muted">Tidak ada file.</div>';
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
