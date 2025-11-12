@props(['items'])

@php
    $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
    $collection = $isPaginator ? $items->getCollection() : collect($items);
@endphp

<div class="position-relative">
    @forelse($collection as $item)
        <div class="d-flex align-items-start position-relative pb-4">
            <div class="d-flex flex-column align-items-center me-3">
                <span class="badge {{ $item->badge_class ?? 'bg-secondary' }} text-uppercase">{{ $item->type_label ?? $item->type }}</span>
                <small class="text-muted mt-2">{{ optional($item->created_at)->format('d M Y H:i') }}</small>
            </div>
            <div class="flex-grow-1 border-start border-2 ps-3">
                <div class="position-relative">
                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-primary border border-white p-2"></span>
                    <div class="ms-4">
                        <p class="mb-2">{!! nl2br(e($item->description)) !!}</p>
                        @if(!empty($item->target_institution) || !empty($item->target_division))
                            <div class="mb-2">
                                <span class="badge bg-light text-dark text-uppercase">Limpah</span>
                                <div class="small text-muted mt-1">
                                    @if(!empty($item->target_institution))
                                        {{ $item->target_institution->name }}
                                    @endif
                                    @if(!empty($item->target_division))
                                        <span class="d-block">Divisi: {{ $item->target_division->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($item->evidences->isNotEmpty())
                            <div class="mt-3">
                                <h6 class="fw-semibold mb-2">Bukti Pendukung</h6>
                                <ul class="list-group list-group-flush">
                                    @foreach($item->evidences as $evidence)
                                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                            <span class="small text-break"><i class="fa fa-paperclip me-2"></i>{{ basename($evidence->file_url) }}</span>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary btn-preview-file"
                                                data-file-url="{{ $evidence->file_url }}"
                                                data-file-type="{{ strtolower($evidence->file_type ?? '') }}"
                                            >
                                                Lihat File
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info mb-0">Belum ada tahapan penanganan untuk laporan ini.</div>
    @endforelse
</div>

@if($isPaginator && $items->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
@endif
