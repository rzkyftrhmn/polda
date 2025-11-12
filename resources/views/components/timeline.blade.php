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
                padding-left: 1.5rem;
            }

            .journey-timeline::before {
                content: '';
                position: absolute;
                inset: 0 auto 0 0.65rem;
                width: 3px;
                background: linear-gradient(180deg, rgba(59, 130, 246, 0.4), rgba(59, 130, 246, 0));
            }

            .journey-timeline-item {
                position: relative;
                display: flex;
                gap: 1.25rem;
                padding-bottom: 1.5rem;
            }

            .journey-timeline-item:last-child {
                padding-bottom: 0;
            }

            .journey-timeline-marker {
                position: relative;
                flex: 0 0 auto;
                width: 6rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .journey-timeline-marker::after {
                content: '';
                position: absolute;
                top: 12px;
                right: -1.4rem;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background-color: #2563eb;
                box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            }

            .journey-timeline-card {
                position: relative;
                flex: 1 1 auto;
                background-color: rgba(255, 255, 255, 0.95);
                border-radius: 1rem;
                border: 1px solid rgba(15, 23, 42, 0.06);
                box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
                padding: 1.35rem 1.75rem;
            }

            body.dark-version .journey-timeline-card,
            body[data-bs-theme="dark"] .journey-timeline-card {
                background-color: rgba(15, 23, 42, 0.75);
                border-color: rgba(148, 163, 184, 0.12);
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.55);
            }

            .journey-timeline-card h6 {
                font-size: 0.95rem;
                letter-spacing: 0.03em;
                text-transform: uppercase;
                color: var(--bs-primary);
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

            @media (max-width: 767.98px) {
                .journey-timeline {
                    padding-left: 1rem;
                }

                .journey-timeline::before {
                    left: 0.5rem;
                }

                .journey-timeline-marker {
                    width: 4.5rem;
                }

                .journey-timeline-card {
                    padding: 1.1rem 1.25rem;
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
        @endphp
        <article class="journey-timeline-item">
            <div class="journey-timeline-marker">
                <span class="badge {{ $item->badge_class ?? 'bg-secondary' }} text-uppercase small fw-semibold">
                    {{ $item->type_label ?? $item->type }}
                </span>
                <small class="text-muted mt-2">
                    {{ optional($item->created_at)->format('d M Y H:i') }}
                </small>
            </div>
            <div class="journey-timeline-card">
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
