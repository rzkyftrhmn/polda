{{-- resources/views/components/file-viewer.blade.php --}}
@once
<div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="fileViewerModalLabel">Pratinjau Bukti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="text-center d-none" data-viewer-image></div>
                <div class="ratio ratio-16x9 mb-3 d-none" data-viewer-doc></div>
                <div class="text-center d-none" data-viewer-fallback></div>
            </div>

            <div class="modal-footer justify-content-between">
                <a href="#" target="_blank" id="downloadFileBtn" class="btn btn-primary d-none">
                    <i class="fa fa-download me-1"></i> Unduh File
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('fileViewerModal');
    const imgEl = modalEl.querySelector('[data-viewer-image]');
    const docEl = modalEl.querySelector('[data-viewer-doc]');
    const fallbackEl = modalEl.querySelector('[data-viewer-fallback]');
    const downloadBtn = document.getElementById('downloadFileBtn');

    const resetView = () => {
        [imgEl, docEl, fallbackEl].forEach(el => {
            el.classList.add('d-none');
            el.innerHTML = '';
        });
        downloadBtn.classList.add('d-none');
        downloadBtn.href = '#';
    };

    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-preview-file');
        if (!btn) return;

        e.preventDefault();
        const url = btn.dataset.fileUrl;
        const type = (btn.dataset.fileType || url.split('.').pop()).toLowerCase();
        resetView();

        downloadBtn.href = url;
        downloadBtn.classList.remove('d-none');

        if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(type)) {
            imgEl.innerHTML = `<img src="${url}" class="img-fluid rounded shadow">`;
            imgEl.classList.remove('d-none');
        } else if (['pdf'].includes(type)) {
            docEl.innerHTML = `<iframe src="https://mozilla.github.io/pdf.js/web/viewer.html?file=${encodeURIComponent(url)}"
                width="100%" height="600" frameborder="0"></iframe>`;
            docEl.classList.remove('d-none');
        } else if (['doc', 'docx'].includes(type)) {
            docEl.innerHTML = `<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(url)}"
                width="100%" height="600" frameborder="0"></iframe>`;
            docEl.classList.remove('d-none');
        } else {
            fallbackEl.innerHTML = `<div class="alert alert-info">Pratinjau belum didukung.</div>`;
            fallbackEl.classList.remove('d-none');
        }

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
});
</script>
@endonce
