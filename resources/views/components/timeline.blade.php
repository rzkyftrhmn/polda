@props(['items'])

@php
    $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
    $collection = $isPaginator ? $items->getCollection() : collect($items);
@endphp

@once
    @push('styles')
        <style>
            .journey-timeline {
                position: relative;
                padding: 2.5rem 0;
            }

            .journey-timeline::before {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                left: 50%;
                width: 4px;
                margin-left: -2px;
                background: linear-gradient(180deg, rgba(99, 102, 241, 0.25), rgba(59, 130, 246, 0.08));
            }

            .journey-timeline::after {
                content: '';
                display: block;
                clear: both;
            }

            .journey-timeline-item {
                position: relative;
                margin-bottom: 3rem;
                min-height: 140px;
            }

            .journey-timeline-item:last-child {
                margin-bottom: 0;
            }

            .journey-timeline-marker {
                position: absolute;
                top: 1.85rem;
                left: 50%;
                width: 20px;
                height: 20px;
                margin-left: -10px;
                border-radius: 50%;
                background: linear-gradient(135deg, #6366f1, #2563eb);
                box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.2);
                border: 2px solid #ffffff;
                z-index: 2;
            }

            body.dark-version .journey-timeline-marker,
            body[data-bs-theme="dark"] .journey-timeline-marker {
                border-color: rgba(15, 23, 42, 0.95);
                box-shadow: 0 0 0 6px rgba(165, 180, 252, 0.28);
            }

            .journey-timeline-card {
                position: relative;
                width: 46%;
                float: left;
                background: rgba(255, 255, 255, 0.92);
                border-radius: 1rem;
                border: 1px solid rgba(15, 23, 42, 0.08);
                box-shadow: 0 25px 45px rgba(15, 23, 42, 0.12);
                padding: 1.5rem 1.75rem;
            }

            body.dark-version .journey-timeline-card,
            body[data-bs-theme="dark"] .journey-timeline-card {
                background: rgba(15, 23, 42, 0.78);
                border-color: rgba(148, 163, 184, 0.18);
                box-shadow: 0 25px 55px rgba(15, 23, 42, 0.65);
            }

            .journey-timeline-card::before {
                content: '';
                position: absolute;
                top: 2rem;
                right: -18px;
                border-width: 10px 0 10px 18px;
                border-style: solid;
                border-color: transparent transparent transparent rgba(255, 255, 255, 0.92);
            }

            body.dark-version .journey-timeline-card::before,
            body[data-bs-theme="dark"] .journey-timeline-card::before {
                border-left-color: rgba(15, 23, 42, 0.78);
            }

            .journey-timeline-item.is-right .journey-timeline-card {
                float: right;
            }

            .journey-timeline-item.is-right .journey-timeline-card::before {
                left: -18px;
                right: auto;
                border-width: 10px 18px 10px 0;
                border-color: transparent rgba(255, 255, 255, 0.92) transparent transparent;
            }

            body.dark-version .journey-timeline-item.is-right .journey-timeline-card::before,
            body[data-bs-theme="dark"] .journey-timeline-item.is-right .journey-timeline-card::before {
                border-right-color: rgba(15, 23, 42, 0.78);
            }

            .journey-timeline-meta .badge {
                letter-spacing: 0.08em;
            }

            .journey-evidence-list .list-group-item {
                background: transparent;
                border-color: rgba(148, 163, 184, 0.2);
            }

            .journey-evidence-list .btn {
                white-space: nowrap;
            }

            .journey-limpah-pill {
                background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(37, 99, 235, 0.05));
                border-radius: 0.85rem;
                padding: 0.85rem 1rem;
            }

            body.dark-version .journey-limpah-pill,
            body[data-bs-theme="dark"] .journey-limpah-pill {
                background: rgba(37, 99, 235, 0.25);
            }

            @media (max-width: 991.98px) {
                .journey-timeline::before {
                    left: 1.25rem;
                    margin-left: -2px;
                }

                .journey-timeline-item {
                    margin-bottom: 2.5rem;
                }

                .journey-timeline-card,
                .journey-timeline-item.is-right .journey-timeline-card {
                    width: calc(100% - 3rem);
                    float: none;
                    margin-left: 3rem;
                }

                .journey-timeline-card::before,
                .journey-timeline-item.is-right .journey-timeline-card::before {
                    top: 1.9rem;
                    left: -18px;
                    right: auto;
                    border-width: 10px 18px 10px 0;
                    border-color: transparent rgba(255, 255, 255, 0.92) transparent transparent;
                }

                body.dark-version .journey-timeline-card::before,
                body[data-bs-theme="dark"] .journey-timeline-card::before,
                body.dark-version .journey-timeline-item.is-right .journey-timeline-card::before,
                body[data-bs-theme="dark"] .journey-timeline-item.is-right .journey-timeline-card::before {
                    border-right-color: rgba(15, 23, 42, 0.78);
                    border-left-color: transparent;
                }

                .journey-timeline-marker {
                    left: 1.25rem;
                    margin-left: -10px;
                }
            }
        </style>
    @endpush
@endonce

<div class="journey-timeline">
    @forelse($collection as $item)
        @php
            $targetInstitution = $item->target_institution ?? null;
            $targetDivision = $item->target_division ?? null;
            $isLeft = $loop->iteration % 2 === 1;
        @endphp
        <article class="journey-timeline-item {{ $isLeft ? 'is-left' : 'is-right' }}">
            <div class="journey-timeline-marker" aria-hidden="true"></div>

            <div class="journey-timeline-card">
                <div class="journey-timeline-meta d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <span class="badge {{ $item->badge_class ?? 'bg-secondary' }} text-uppercase small fw-semibold">
                        {{ $item->type_label ?? $item->type }}
                    </span>
                    <span class="text-muted small d-inline-flex align-items-center gap-2">
                        <i class="fa fa-clock text-primary"></i>
                        {{ optional($item->created_at)->format('d M Y H:i') }}
                    </span>
                </div>

                <p class="mb-3">{!! nl2br(e($item->description)) !!}</p>

                @if($targetInstitution || $targetDivision)
                    <div class="journey-limpah-pill mb-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa fa-share-square text-primary mt-1"></i>
                            <div>
                                <div class="fw-semibold text-primary text-uppercase small">Limpah</div>
                                @if($targetInstitution)
                                    <div class="text-body-secondary">{{ $targetInstitution->name }}</div>
                                @endif
                                @if($targetDivision)
                                    <div class="text-body-secondary">Unit/Sub-bagian: {{ $targetDivision->name }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($item->evidences->isNotEmpty())
                    <div>
                        <h6 class="fw-semibold mb-2">Bukti Pendukung</h6>
                        <ul class="list-group list-group-flush journey-evidence-list">
                            @foreach($item->evidences as $evidence)
                                <li class="list-group-item px-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                    <span class="small text-break">
                                        <i class="fa fa-paperclip me-2 text-primary"></i>{{ basename($evidence->file_url) }}
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary btn-preview-file"
                                            data-file-url="{{ $evidence->file_url }}"
                                            data-file-type="{{ strtolower($evidence->file_type ?? '') }}"
                                            data-file-name="{{ basename($evidence->file_url) }}"
                                        >
                                            <i class="fa fa-eye me-1"></i> Lihat File
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <div class="alert alert-info mb-0">Belum ada tahapan penanganan untuk laporan ini.</div>
    @endforelse
</div>

@if($isPaginator && $items->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
@endif
