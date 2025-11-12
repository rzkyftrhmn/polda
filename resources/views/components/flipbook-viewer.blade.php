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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js" integrity="sha512-6IJew7nmrBDW4Ka6vvsib9MBXuty0YFRJ7ke1+NetNUA8JvYPRd8mqF0oKm1DMncdSog0UbieiCO2vlF0pT1uA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js" integrity="sha512-fzbA+ofX6jDA0AnqvshxwEudbOi6JQm1nVpi18T4imv0uoXmmHQEuP4liAiRcL4MSkXCTU6YclzGiUcuUf3E4g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js" integrity="sha512-ZcpufY4majK9/2FwU+K20VOU2Asn0AmsCtU8/L1oQHZ30lVAOtnwBSuPYqx610LdNIujEHPuujSQtpItMzFrMg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flipbookModalEl = document.getElementById('flipbookModal');
            const flipbookContainer = document.getElementById('flipbookContainer');
            const fileViewerModalEl = document.getElementById('fileViewerModal');
            const imageWrapper = fileViewerModalEl ? fileViewerModalEl.querySelector('[data-viewer-image]') : null;
            const docWrapper = fileViewerModalEl ? fileViewerModalEl.querySelector('[data-viewer-doc]') : null;
            const fallbackWrapper = fileViewerModalEl ? fileViewerModalEl.querySelector('[data-viewer-fallback]') : null;

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const documentExtensions = ['doc', 'docx'];

            const resolveUrl = function (url) {
                try {
                    return new URL(url, window.location.origin).href;
                } catch (error) {
                    return url;
                }
            };

            function resetFileViewer() {
                [imageWrapper, docWrapper, fallbackWrapper].forEach(function (el) {
                    if (!el) {
                        return;
                    }
                    el.classList.add('d-none');
                    el.innerHTML = '';
                });
            }

            function destroyFlipbook() {
                if (window.jQuery && window.jQuery('#flipbookContainer').data('turn')) {
                    window.jQuery('#flipbookContainer').turn('destroy').removeClass('shadow');
                }
                if (flipbookContainer) {
                    flipbookContainer.classList.remove('d-flex', 'flex-column', 'align-items-center', 'gap-3', 'p-3');
                }
            }

            function openImagePreview(url) {
                if (!fileViewerModalEl || !imageWrapper) {
                    return;
                }

                const source = resolveUrl(url);

                resetFileViewer();
                imageWrapper.innerHTML = '<img src="' + source + '" alt="File preview" class="img-fluid rounded shadow">';
                imageWrapper.classList.remove('d-none');

                bootstrap.Modal.getOrCreateInstance(fileViewerModalEl).show();
            }

            function openDocumentPreview(url) {
                openFallbackPreview(
                    url,
                    'Pratinjau dokumen Word belum didukung di aplikasi ini. Gunakan tombol di bawah untuk mengunduh dan membuka file.'
                );
            }

            function openFallbackPreview(url, message) {
                if (!fileViewerModalEl || !fallbackWrapper) {
                    return;
                }

                const source = resolveUrl(url);

                resetFileViewer();
                const alertMessage = message || 'Pratinjau belum didukung untuk tipe file ini.';
                fallbackWrapper.innerHTML = '' +
                    '<div class="alert alert-info">' + alertMessage + '</div>' +
                    '<a href="' + source + '" target="_blank" rel="noopener" download class="btn btn-primary">Unduh File</a>';
                fallbackWrapper.classList.remove('d-none');

                bootstrap.Modal.getOrCreateInstance(fileViewerModalEl).show();
            }

            function renderFlipbook(url) {
                if (!flipbookModalEl || !flipbookContainer) {
                    openFallbackPreview(url);
                    return;
                }

                const source = resolveUrl(url);
                destroyFlipbook();
                flipbookContainer.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Memuat pratinjau...</div>';

                const modalInstance = bootstrap.Modal.getOrCreateInstance(flipbookModalEl);
                modalInstance.show();

                if (!window.pdfjsLib) {
                    flipbookContainer.innerHTML = '<div class="p-4 text-center">Pratinjau PDF tidak tersedia. <a href="' + source + '" target="_blank" rel="noopener">Unduh file</a>.</div>';
                    return;
                }

                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

                window.pdfjsLib.getDocument(source).promise.then(function (pdf) {
                    flipbookContainer.innerHTML = '';

                    const renderTasks = [];

                    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                        renderTasks.push(pdf.getPage(pageNumber).then(function (page) {
                            const viewport = page.getViewport({ scale: 1.1 });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            return page.render({ canvasContext: context, viewport: viewport }).promise.then(function () {
                                const wrapper = document.createElement('div');
                                wrapper.className = 'page d-flex justify-content-center align-items-center bg-white';
                                wrapper.appendChild(canvas);
                                flipbookContainer.appendChild(wrapper);
                            });
                        }));
                    }

                    return Promise.all(renderTasks).then(function () {
                        if (window.jQuery && typeof window.jQuery.fn.turn === 'function') {
                            const $container = window.jQuery('#flipbookContainer');
                            if ($container.data('turn')) {
                                $container.turn('destroy');
                            }

                            const modalBody = flipbookModalEl.querySelector('.modal-body');
                            const width = modalBody ? modalBody.clientWidth : $container.width();
                            const height = modalBody ? modalBody.clientHeight : 600;

                            $container.turn({
                                width: width || 960,
                                height: height || 600,
                                autoCenter: true,
                                gradients: true,
                            });
                        } else {
                            flipbookContainer.classList.add('d-flex', 'flex-column', 'align-items-center', 'gap-3', 'p-3');
                            const fallbackNotice = document.createElement('div');
                            fallbackNotice.className = 'alert alert-info w-100 text-center';
                            fallbackNotice.textContent = 'Mode flipbook tidak tersedia. Menampilkan halaman PDF secara berurutan.';
                            flipbookContainer.prepend(fallbackNotice);
                        }
                    });
                }).catch(function () {
                    flipbookContainer.innerHTML = '<div class="p-4 text-center">Gagal memuat pratinjau PDF. <a href="' + source + '" target="_blank" rel="noopener">Unduh file</a>.</div>';
                });
            }

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.btn-preview-file');
                if (!button) {
                    return;
                }

                event.preventDefault();

                const url = button.getAttribute('data-file-url');
                if (!url) {
                    return;
                }

                const typeAttr = (button.getAttribute('data-file-type') || '').toLowerCase();
                const extension = typeAttr || url.split('.').pop().toLowerCase();

                if (extension === 'pdf') {
                    renderFlipbook(url);
                    return;
                }

                if (imageExtensions.includes(extension)) {
                    openImagePreview(url);
                    return;
                }

                if (documentExtensions.includes(extension)) {
                    openDocumentPreview(url);
                    return;
                }

                openFallbackPreview(url);
            }, true);

            if (flipbookModalEl) {
                flipbookModalEl.addEventListener('hidden.bs.modal', function () {
                    destroyFlipbook();
                    if (flipbookContainer) {
                        flipbookContainer.innerHTML = '';
                    }
                });
            }
        });
    </script>
@endonce
