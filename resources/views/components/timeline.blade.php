@props(['items'])

@php
    $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
    $collection = $isPaginator ? $items->getCollection() : collect($items);
@endphp

@once
<style>
    .cd-container {
        position: relative;
        z-index: 0;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 2rem 0;
    }

    .cd-timeline {
        position: relative;
        z-index: 0;
        padding: 2rem 0;
        margin: 0;
        --cd-timeline-height: 100%;
    }

    .cd-timeline::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 3px;
        height: 100%;
        background-color: var(--bs-primary, #0d6efd);
        transition: background-color 0.3s ease;
        z-index: 0;
    }

    .cd-timeline.is-empty::before {
        display: none;
    }

    .cd-timeline::after {
        content: "";
        display: table;
        clear: both;
    }

    .cd-timeline-block {
        position: relative;
        z-index: 0;
        margin: 2rem 0;
        min-height: 60px;
    }

    .cd-timeline-block:first-child {
        margin-top: 0;
    }

    .cd-timeline-block:last-child {
        margin-bottom: 0;
    }

    .cd-timeline-img {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translate(-50%, 0);
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 4px solid var(--bs-body-bg);
        background-color: var(--bs-primary);
        box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        z-index: 1;
    }

    .cd-timeline-img::after {
        content: "";
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: var(--bs-white);
        border: 2px solid var(--bs-primary);
        display: block;
    }

    
    .cd-timeline-content {
        position: relative;
        z-index: 1;
        margin-left: 0;
        padding: 1.5rem;
        background-color: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 0.75rem;
        box-shadow: 0 18px 40px rgba(var(--bs-body-color-rgb), 0.08);
        color: var(--bs-body-color);
        width: 45%;
        float: left;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .cd-timeline-content:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 48px rgba(var(--bs-body-color-rgb), 0.12);
    }

    .cd-timeline-content::before {
        content: "";
        position: absolute;
        top: 28px;
        right: -18px;
        height: 0;
        width: 0;
        border-style: solid;
        border-width: 9px 0 9px 18px;
        border-color: transparent transparent transparent var(--bs-body-bg);
    }

    .cd-timeline-block:nth-child(even) .cd-timeline-content {
        float: right;
    }

    .cd-timeline-block:nth-child(even) .cd-timeline-content::before {
        right: auto;
        left: -18px;
        border-width: 9px 18px 9px 0;
        border-color: transparent var(--bs-body-bg) transparent transparent;
    }

    .cd-timeline-block:nth-child(even) .cd-timeline-img {
        transform: translate(-50%, 0);
    }

    .cd-timeline-content h6 {
        font-size: 0.95rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--bs-primary);
    }

    .cd-timeline-content .timeline-meta {
        color: var(--bs-secondary-color, var(--bs-body-color));
        font-size: 0.85rem;
    }

    .cd-timeline-content p {
        margin-bottom: 1rem;
    }

    .cd-timeline-content .journey-limpah-pill {
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.18), rgba(var(--bs-primary-rgb), 0.05));
        border-radius: 0.85rem;
        padding: 0.85rem 1rem;
    }

    .cd-timeline-content .journey-evidence-list .list-group-item {
        background-color: transparent;
        border: none;
        border-top: 1px solid var(--bs-border-color);
        padding-left: 0;
        padding-right: 0;
    }

    .cd-timeline-content .journey-evidence-list .list-group-item:first-child {
        border-top: none;
    }

    .cd-timeline-content .journey-evidence-list .btn {
        white-space: nowrap;
    }

    .cd-timeline-content .badge {
        letter-spacing: 0.05em;
    }

    .cd-timeline-block::after {
        content: "";
        display: table;
        clear: both;
    }

    .cd-timeline-img.is-hidden,
    .cd-timeline-content.is-hidden {
        visibility: hidden;
    }

    .cd-timeline-img.is-hidden {
        transform: translate(-50%, 0) scale(0.7);
    }

    .cd-timeline-content.is-hidden {
        opacity: 0;
        transform: translateY(20px);
    }

    .cd-timeline-img.bounce-in {
        visibility: visible;
        animation: cd-bounce-1 0.6s;
    }

    .cd-timeline-content.bounce-in {
        visibility: visible;
        animation: cd-bounce-2 0.6s;
    }

    @keyframes cd-bounce-1 {
        0% {
            opacity: 0;
            transform: translate(-50%, 0) scale(0.6);
        }
        60% {
            opacity: 1;
            transform: translate(-50%, 0) scale(1.05);
        }
        100% {
            transform: translate(-50%, 0) scale(1);
        }
    }

    @keyframes cd-bounce-2 {
        0% {
            opacity: 0;
            transform: translateY(40px);
        }
        60% {
            opacity: 1;
            transform: translateY(-8px);
        }
        100% {
            transform: translateY(0);
        }
    }

    @media (max-width: 991.98px) {
        .cd-container {
            width: 95%;
        }

        .cd-timeline-content {
            width: 48%;
        }
    }

    @media (max-width: 767.98px) {
        .cd-container {
            width: 100%;
            padding: 1.5rem 0 1rem;
        }

        .cd-timeline {
            padding-top: 1rem;
        }

        .cd-timeline::before {
            left: 20px;
            transform: none;
            background-color: var(--bs-primary, #0d6efd);
        }

        .cd-timeline-block {
            margin: 1.5rem 0;
        }

        .cd-timeline-img {
            left: 22px;
            transform: translate(-50%, 0);
            width: 36px;
            height: 36px;
        }

        .cd-timeline-content,
        .cd-timeline-block:nth-child(even) .cd-timeline-content {
            float: none;
            width: calc(100% - 70px);
            margin-left: 70px;
        }

        .cd-timeline-content::before,
        .cd-timeline-block:nth-child(even) .cd-timeline-content::before {
            top: 24px;
            right: auto;
            left: -18px;
            border-width: 9px 18px 9px 0;
            border-color: transparent var(--bs-body-bg) transparent transparent;
        }
    }

    @media (max-width: 575.98px) {
        .cd-timeline-content {
            width: calc(100% - 60px);
            margin-left: 60px;
            padding: 1.25rem 1.15rem;
        }

        .cd-timeline-img {
            left: 20px;
        }
    }
</style>
@endonce

@once
<script>
    (function() {
        const raf = window.requestAnimationFrame || function(cb) { return setTimeout(cb, 16); };
        const $ = window.jQuery;
        if (!$) return;

        $(function() {
            const $timeline = $('.cd-timeline');
            if (!$timeline.length) {
                return;
            }

            const $blocks = $timeline.find('.cd-timeline-block');
            const offset = 0.8;

            const hideBlocks = function(blocks, offsetValue) {
                blocks.each(function() {
                    const $block = $(this);
                    const blockTop = $block.offset().top;
                    if (blockTop > $(window).scrollTop() + $(window).height() * offsetValue) {
                        $block.find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');
                    }
                });
            };

            const showBlocks = function(blocks, offsetValue) {
                blocks.each(function() {
                    const $block = $(this);
                    if (
                        $block.offset().top <= $(window).scrollTop() + $(window).height() * offsetValue &&
                        $block.find('.cd-timeline-img').hasClass('is-hidden')
                    ) {
                        $block.find('.cd-timeline-img, .cd-timeline-content')
                            .removeClass('is-hidden')
                            .addClass('bounce-in');
                    }
                });
            };

            const adjustTimelineHeight = function() {
                raf(function() {
                    const timelineEl = $timeline.get(0);
                    if (!timelineEl) {
                        return;
                    }
                    const $visibleBlocks = $timeline.find('.cd-timeline-block');
                    if (!$visibleBlocks.length) {
                        timelineEl.style.removeProperty('--cd-timeline-height');
                        return;
                    }
                    const $last = $visibleBlocks.last();
                    const timelineTop = $timeline.offset().top;
                    const lastBottom = $last.offset().top + $last.outerHeight(true) - timelineTop;
                    timelineEl.style.setProperty('--cd-timeline-height', `${Math.max(lastBottom, 0)}px`);
                });
            };

            hideBlocks($blocks, offset);
            showBlocks($blocks, offset);
            adjustTimelineHeight();

            $(window).on('scroll', function() {
                raf(function() {
                    showBlocks($blocks, offset);
                });
            });

            $(window).on('load resize orientationchange', function() {
                adjustTimelineHeight();
            });
        });
    })();
</script>
@endonce

<div class="cd-container">
    <section class="cd-timeline{{ $collection->isEmpty() ? " is-empty" : "" }}">
        @forelse($collection as $item)
            @php
                $targetInstitution = $item->target_institution ?? null;
                $targetDivision = $item->target_division ?? null;
                $evidences = method_exists($item, 'evidences') ? $item->evidences : ($item->evidences ?? []);
                $evidences = $evidences instanceof \Illuminate\Support\Collection ? $evidences : collect($evidences);
                $payload = $item->description_payload ?? [];
                $docKind = $payload['doc_kind'] ?? null;
                $decision = $payload['decision'] ?? null;
            @endphp
            <div class="cd-timeline-block">
                <div class="cd-timeline-img"></div>
                <div class="cd-timeline-content">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                        <span class="badge {{ $item->badge_class ?? 'bg-secondary' }} text-uppercase fw-semibold">
                            {{ $item->type_label ?? $item->type }}
                        </span>
                        <small class="timeline-meta text-muted">
                            {{ optional($item->created_at)->format('d M Y H:i') }}
                        </small>
                    </div>

                    <p class="mb-3">{!! nl2br(e($item->description)) !!}</p>

                    @if($decision && ($item->type === \App\Enums\ReportJourneyType::TRIAL->value || ($item->type === \App\Enums\ReportJourneyType::COMPLETED->value && $docKind === 'sidang')))
                        <div class="mb-3"><strong>Putusan:</strong> {{ $decision }}</div>
                    @endif

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

                    @if($evidences->isNotEmpty())
                        <div class="journey-evidence">
                            <h6 class="fw-semibold mb-2">Bukti Pendukung</h6>
                            <ul class="list-group list-group-flush journey-evidence-list">
                                @foreach($evidences as $evidence)
                                    @php
                                        $fileUrl = $evidence->file_url ?? $evidence->url ?? null;
                                        $fileType = strtolower($evidence->file_type ?? $evidence->ext ?? '');
                                        $fileName = $evidence->file_name ?? $evidence->name ?? ($fileUrl ? basename($fileUrl) : 'Lampiran');
                                    @endphp
                                    <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                        <span class="small text-break">
                                            <i class="fa fa-paperclip me-2 text-primary"></i>{{ $fileName }}
                                        </span>
                                        <div class="d-flex gap-2">
                                            @if($fileUrl)
                                                <a
                                                    href="{{ $fileUrl }}"
                                                    class="btn btn-sm btn-outline-primary btn-preview-file"
                                                    data-file-url="{{ $fileUrl }}"
                                                    data-file-type="{{ $fileType }}"
                                                    data-file-name="{{ $fileName }}"
                                                >
                                                    <i class="fa fa-eye me-1"></i> Lihat File
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fa fa-inbox fa-2x mb-2"></i><br>
                Belum ada tahapan penanganan untuk laporan ini.
            </div>
        @endforelse
    </section>
</div>


@if($isPaginator && $items->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $items->links('pagination::bootstrap-5') }}
</div>
@endif
