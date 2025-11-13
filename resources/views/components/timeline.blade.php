@once
<style>
/* ======== Timeline Container ======== */
.timeline {
  position: relative;
  padding: 2rem 0;
}

/* Vertical line */
.timeline::before {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 3px;
  background-color: var(--bs-border-color);
  transform: translateX(-50%);
  transition: background-color .3s ease;
}

/* Timeline block */
.timeline-item {
  position: relative;
  margin-bottom: 3rem;
}

/* Dots */
.timeline-dot {
  position: absolute;
  top: 18px;
  left: 50%;
  transform: translateX(-50%);
  width: 16px;
  height: 16px;
  background-color: var(--bs-primary);
  border: 3px solid var(--bs-body-bg);
  border-radius: 50%;
  z-index: 2;
  box-shadow: 0 0 0 2px var(--bs-border-color);
}

/* Card styling */
.timeline-card {
  position: relative;
  width: 46%;
  background-color: var(--bs-body-bg);
  color: var(--bs-body-color);
  border: none;
  border-radius: .5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: all .3s ease;
  padding: 1.25rem;
}

.timeline-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

/* Odd-even positioning (alternate sides) */
.timeline-item:nth-child(odd) .timeline-card {
  float: left;
  clear: both;
  transform-origin: right center;
}
.timeline-item:nth-child(even) .timeline-card {
  float: right;
  clear: both;
  transform-origin: left center;
}

/* Connector arrows */
.timeline-item:nth-child(odd) .timeline-card::before {
  content: "";
  position: absolute;
  top: 24px;
  right: -10px;
  border-width: 8px 0 8px 10px;
  border-style: solid;
  border-color: transparent transparent transparent var(--bs-body-bg);
}
.timeline-item:nth-child(even) .timeline-card::before {
  content: "";
  position: absolute;
  top: 24px;
  left: -10px;
  border-width: 8px 10px 8px 0;
  border-style: solid;
  border-color: transparent var(--bs-body-bg) transparent transparent;
}

/* Animations (appear on scroll) */
.timeline-item.visible .timeline-card {
  opacity: 1;
  transform: none;
}
.timeline-item .timeline-card {
  opacity: 0;
  transform: scale(0.95);
  transition: opacity .5s ease, transform .5s ease;
}

/* Responsive: line left + all boxes right */
@media (max-width: 768px) {
  .timeline::before {
    left: 12px;
    transform: none;
  }
  .timeline-dot {
    left: 12px;
    transform: none;
  }
  .timeline-card {
    width: calc(100% - 40px);
    margin-left: 32px;
    float: none !important;
  }
  .timeline-item .timeline-card::before {
    left: -10px;
    right: auto;
    border-width: 8px 10px 8px 0;
    border-color: transparent var(--bs-body-bg) transparent transparent;
  }
}

/* Dark theme fix */
body[data-bs-theme="dark"] .timeline::before {
  background-color: rgba(255,255,255,0.2);
}
body[data-bs-theme="dark"] .timeline-dot {
  border-color: #0d1117;
  box-shadow: 0 0 0 2px rgba(255,255,255,0.1);
}
</style>
@endonce

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  }, { threshold: 0.2 });

  document.querySelectorAll('.timeline-item').forEach(item => observer.observe(item));
});
</script>
@endonce

<!-- ======== Timeline Markup ======== -->
<div class="timeline">
  @forelse($journeys as $j)
  <div class="timeline-item">
    <div class="timeline-dot"></div>
    <div class="timeline-card">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 fw-semibold text-primary">{{ $j->type_label }}</h6>
        <small class="text-muted">{{ $j->created_at->format('d M Y H:i') }}</small>
      </div>
      <p class="mb-2">{{ $j->description }}</p>

      @if(!empty($j->files))
        <div class="d-flex flex-wrap gap-2">
          @foreach($j->files as $f)
          <button class="btn btn-sm btn-outline-primary btn-preview-file"
                  data-file-url="{{ $f->url }}" data-file-type="{{ $f->ext }}">
            <i class="fa fa-file me-1"></i> {{ $f->name }}
          </button>
          @endforeach
        </div>
      @endif
    </div>
  </div>
  @empty
  <div class="text-center text-muted py-5">
    <i class="fa fa-inbox fa-2x mb-2"></i><br>
    Belum ada tahapan laporan.
  </div>
  @endforelse
</div>
