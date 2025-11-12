@once
<div class="modal fade" id="flipbookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="flipbookContainer" class="w-100 h-100" style="height: 80vh;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Pakai CDN full --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"
        integrity="sha512-6IJew7nmrBDW4Ka6vvsib9MBXuty0YFRJ7ke1+NetNUA8JvYPRd8mqF0oKm1DMncdSog0UbieiCO2vlF0pT1uA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('flipbookModal');
    const container = document.getElementById('flipbookContainer');

    function clearContainer() {
        if (container) container.innerHTML = '';
    }

    function openDocxPreview(url) {
        const encoded = encodeURIComponent(url);
        container.innerHTML = `
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=${encoded}"
                    style="width:100%;height:80vh;border:none;" allowfullscreen></iframe>`;
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    function openPdfPreview(url) {
        clearContainer();
        const src = new URL(url, window.location.origin).href;

        if (!window.pdfjsLib) {
            container.innerHTML = `
                <div class="p-4 text-center">
                    Gagal memuat PDF. <a href="${src}" target="_blank">Unduh file</a>.
                </div>`;
            return;
        }

        // Gunakan CDN worker
        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
            "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

        container.innerHTML = '<div class="p-4 text-center text-muted">Memuat pratinjau PDF...</div>';

        window.pdfjsLib.getDocument(src).promise.then(function (pdf) {
            container.innerHTML = '';
            for (let i = 1; i <= pdf.numPages; i++) {
                pdf.getPage(i).then(function (page) {
                    const viewport = page.getViewport({ scale: 1.2 });
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    page.render({ canvasContext: ctx, viewport: viewport }).promise.then(function () {
                        container.appendChild(canvas);
                    });
                });
            }
        }).catch(function () {
            container.innerHTML = `
                <div class="p-4 text-center">
                    Pratinjau PDF gagal dimuat. <a href="${src}" target="_blank">Unduh file</a>.
                </div>`;
        });

        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    document.addEventListener('click', function (event) {
        const btn = event.target.closest('.btn-preview-file');
        if (!btn) return;

        event.preventDefault();
        const url = btn.dataset.fileUrl;
        const ext = (btn.dataset.fileType || url.split('.').pop()).toLowerCase();

        if (['jpg','jpeg','png','gif','bmp','webp'].includes(ext)) {
            container.innerHTML = `<img src="${url}" class="img-fluid rounded d-block mx-auto my-3">`;
            bootstrap.Modal.getOrCreateInstance(modal).show();
            return;
        }

        if (ext === 'pdf') return openPdfPreview(url);
        if (['doc','docx'].includes(ext)) return openDocxPreview(url);

        container.innerHTML = `
            <div class="alert alert-info m-4 text-center">
                Pratinjau belum didukung untuk tipe file ini.
                <a href="${url}" class="btn btn-primary btn-sm mt-3" target="_blank">Unduh File</a>
            </div>`;
        bootstrap.Modal.getOrCreateInstance(modal).show();
    });

    modal.addEventListener('hidden.bs.modal', clearContainer);
});
</script>
@endonce
