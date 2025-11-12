<div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="fileViewerModalLabel">Pratinjau Bukti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body position-relative">
                <div class="d-flex justify-content-center align-items-center py-5 d-none" data-viewer-loading>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>
                <div class="viewer-scroll d-none" data-viewer-pdf></div>
                <div class="text-center d-none" data-viewer-image>
                    <img src="" alt="Pratinjau file" class="img-fluid rounded shadow" data-viewer-image-el referrerpolicy="no-referrer" />
                </div>
                <div class="docx-preview-wrapper d-none" data-viewer-docx></div>
                <div class="d-none" data-viewer-message></div>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                <a
                    href="#"
                    class="btn btn-primary"
                    target="_blank"
                    rel="noopener"
                    download
                    data-download-button
                >
                    <i class="fa fa-download me-1"></i> Unduh File
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@once
    <style>
        #fileViewerModal .modal-body {
            min-height: 40vh;
        }

        #fileViewerModal .viewer-scroll {
            max-height: 70vh;
            overflow-y: auto;
            background-color: var(--bs-body-bg);
            padding: 1rem;
        }

        #fileViewerModal .viewer-scroll canvas {
            display: block;
            width: 100%;
            height: auto !important;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
            border-radius: 0.5rem;
        }

        #fileViewerModal .docx-preview-wrapper {
            max-height: 70vh;
            overflow-y: auto;
            padding: 1rem;
            background-color: var(--bs-body-bg);
        }

        #fileViewerModal .docx-preview-wrapper p {
            margin-bottom: 0.75rem;
        }

        #fileViewerModal .docx-preview-wrapper h1,
        #fileViewerModal .docx-preview-wrapper h2,
        #fileViewerModal .docx-preview-wrapper h3,
        #fileViewerModal .docx-preview-wrapper h4,
        #fileViewerModal .docx-preview-wrapper h5,
        #fileViewerModal .docx-preview-wrapper h6 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        body.dark-version #fileViewerModal .modal-content,
        body[data-bs-theme="dark"] #fileViewerModal .modal-content {
            background-color: #0d1117;
            color: #f1f5f9;
        }

        body.dark-version #fileViewerModal .viewer-scroll,
        body[data-bs-theme="dark"] #fileViewerModal .viewer-scroll {
            background-color: #111;
        }

        body.dark-version #fileViewerModal .docx-preview-wrapper,
        body[data-bs-theme="dark"] #fileViewerModal .docx-preview-wrapper {
            background-color: #111;
            color: #f1f5f9;
        }

        body.dark-version #fileViewerModal .alert,
        body[data-bs-theme="dark"] #fileViewerModal .alert {
            background-color: rgba(59, 130, 246, 0.1);
            color: #93c5fd;
            border-color: rgba(59, 130, 246, 0.2);
        }
    </style>
@endonce

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('fileViewerModal');
            if (!modalEl) {
                return;
            }

            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            const loadingWrapper = modalEl.querySelector('[data-viewer-loading]');
            const pdfWrapper = modalEl.querySelector('[data-viewer-pdf]');
            const imageWrapper = modalEl.querySelector('[data-viewer-image]');
            const imageEl = modalEl.querySelector('[data-viewer-image-el]');
            const docxWrapper = modalEl.querySelector('[data-viewer-docx]');
            const messageWrapper = modalEl.querySelector('[data-viewer-message]');
            const downloadButton = modalEl.querySelector('[data-download-button]');

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const officeExtensions = ['doc', 'docx'];
            const pdfExtension = 'pdf';

            const PDFJS_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
            const PDFJS_WORKER_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            const MAMMOTH_SRC = 'https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js';

            let currentUrl = null;

            function loadScriptOnce(src) {
                return new Promise(function (resolve, reject) {
                    let script = document.querySelector('script[data-dynamic-src="' + src + '"]');

                    if (script) {
                        if (script.getAttribute('data-loaded') === 'true') {
                            resolve();
                            return;
                        }

                        script.addEventListener('load', function () { resolve(); }, { once: true });
                        script.addEventListener('error', function () { reject(new Error('Gagal memuat skrip: ' + src)); }, { once: true });
                        return;
                    }

                    script = document.createElement('script');
                    script.src = src;
                    script.async = true;
                    script.setAttribute('data-dynamic-src', src);
                    script.addEventListener('load', function () {
                        script.setAttribute('data-loaded', 'true');
                        resolve();
                    }, { once: true });
                    script.addEventListener('error', function () {
                        script.remove();
                        reject(new Error('Gagal memuat skrip: ' + src));
                    }, { once: true });

                    document.head.appendChild(script);
                });
            }

            function ensurePdfJs() {
                if (window.pdfjsLib) {
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_SRC;
                    return Promise.resolve();
                }

                return loadScriptOnce(PDFJS_SRC).then(function () {
                    if (!window.pdfjsLib) {
                        throw new Error('pdf.js tidak tersedia');
                    }

                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_SRC;
                });
            }

            function ensureMammoth() {
                if (window.mammoth) {
                    return Promise.resolve();
                }

                return loadScriptOnce(MAMMOTH_SRC).then(function () {
                    if (!window.mammoth) {
                        throw new Error('mammoth.js tidak tersedia');
                    }
                });
            }

            function resetState() {
                [loadingWrapper, pdfWrapper, imageWrapper, docxWrapper, messageWrapper].forEach(function (element) {
                    if (!element) {
                        return;
                    }

                    element.classList.add('d-none');
                });

                if (pdfWrapper) {
                    pdfWrapper.innerHTML = '';
                }

                if (imageEl) {
                    imageEl.src = '';
                }

                if (docxWrapper) {
                    docxWrapper.innerHTML = '';
                }

                if (messageWrapper) {
                    messageWrapper.innerHTML = '';
                }

                if (downloadButton) {
                    downloadButton.href = '#';
                }

                currentUrl = null;
            }

            function showLoading() {
                if (loadingWrapper) {
                    loadingWrapper.classList.remove('d-none');
                }
            }

            function hideLoading() {
                if (loadingWrapper) {
                    loadingWrapper.classList.add('d-none');
                }
            }

            function showMessage(message, type = 'info') {
                hideLoading();

                if (!messageWrapper) {
                    return;
                }

                const alertClass = type === 'warning' ? 'alert-warning' : 'alert-info';
                const downloadLink = currentUrl
                    ? `<a href="${currentUrl}" class="btn btn-link p-0" target="_blank" rel="noopener">Gunakan tombol unduh di bawah</a>`
                    : '';

                messageWrapper.innerHTML = `
                    <div class="alert ${alertClass} text-center mb-0">${message}</div>
                    <div class="text-center mt-2">${downloadLink}</div>
                `;

                messageWrapper.classList.remove('d-none');
            }

            function enableDownload(url) {
                if (!downloadButton) {
                    return;
                }

                downloadButton.href = url;
            }

            function fetchArrayBuffer(url) {
                return fetch(url, { credentials: 'include' }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil file');
                    }

                    return response.arrayBuffer();
                });
            }

            function showImagePreview(url) {
                if (!imageWrapper || !imageEl) {
                    showMessage('Pratinjau gambar tidak tersedia. Gunakan tombol unduh di bawah.', 'warning');
                    return;
                }

                showLoading();
                imageEl.onload = hideLoading;
                imageEl.onerror = function () {
                    imageWrapper.classList.add('d-none');
                    imageEl.src = '';
                    showMessage('Gagal memuat pratinjau gambar. Gunakan tombol unduh di bawah.', 'warning');
                };

                imageEl.src = url;
                imageWrapper.classList.remove('d-none');
            }

            function renderPdf(url) {
                if (!pdfWrapper) {
                    showMessage('Pratinjau PDF tidak tersedia. Gunakan tombol unduh di bawah.', 'warning');
                    return;
                }

                showLoading();
                pdfWrapper.innerHTML = '';
                pdfWrapper.classList.remove('d-none');

                ensurePdfJs()
                    .then(function () {
                        return fetchArrayBuffer(url);
                    })
                    .then(function (arrayBuffer) {
                        return window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                    })
                    .then(function (pdfDoc) {
                        const totalPages = pdfDoc.numPages;

                        const renderPage = function (pageNumber) {
                            return pdfDoc.getPage(pageNumber).then(function (page) {
                                const viewport = page.getViewport({ scale: 1.2 });
                                const canvas = document.createElement('canvas');
                                const context = canvas.getContext('2d');

                                canvas.width = viewport.width;
                                canvas.height = viewport.height;
                                canvas.classList.add('pdf-page-canvas');

                                pdfWrapper.appendChild(canvas);

                                return page.render({ canvasContext: context, viewport: viewport }).promise.then(function () {
                                    if (pageNumber < totalPages) {
                                        return renderPage(pageNumber + 1);
                                    }
                                });
                            });
                        };

                        return renderPage(1);
                    })
                    .then(function () {
                        hideLoading();
                    })
                    .catch(function (error) {
                        console.error('Gagal memuat PDF:', error);
                        pdfWrapper.innerHTML = '';
                        pdfWrapper.classList.add('d-none');
                        showMessage('Pratinjau PDF tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                    });
            }

            function renderDocx(url) {
                if (!docxWrapper) {
                    showMessage('Pratinjau dokumen tidak tersedia. Gunakan tombol unduh di bawah.', 'warning');
                    return;
                }

                showLoading();
                docxWrapper.innerHTML = '';
                docxWrapper.classList.remove('d-none');

                ensureMammoth()
                    .then(function () {
                        return fetchArrayBuffer(url);
                    })
                    .then(function (arrayBuffer) {
                        return window.mammoth.convertToHtml({ arrayBuffer: arrayBuffer });
                    })
                    .then(function (result) {
                        hideLoading();
                        docxWrapper.innerHTML = `<div class="docx-preview-content">${result.value}</div>`;
                    })
                    .catch(function (error) {
                        console.error('Gagal memuat DOCX:', error);
                        docxWrapper.innerHTML = '';
                        docxWrapper.classList.add('d-none');
                        showMessage('Pratinjau dokumen tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                    });
            }

            function detectExtension(url, provided) {
                if (provided) {
                    return provided.replace('.', '').toLowerCase();
                }

                const cleanedUrl = url.split('?')[0].split('#')[0];
                const segments = cleanedUrl.split('.');
                return segments.length > 1 ? segments.pop().toLowerCase() : '';
            }

            function openPreview(url, extension) {
                const resolvedUrl = (function () {
                    try {
                        return new URL(url, window.location.origin).href;
                    } catch (error) {
                        return url;
                    }
                })();

                resetState();
                currentUrl = resolvedUrl;
                showLoading();
                enableDownload(resolvedUrl);

                if (extension === pdfExtension) {
                    renderPdf(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (imageExtensions.includes(extension)) {
                    showImagePreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (officeExtensions.includes(extension)) {
                    renderDocx(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                showMessage('Pratinjau tidak tersedia untuk tipe file ini. Gunakan tombol unduh di bawah.', 'warning');
                modalInstance.show();
            }

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.btn-preview-file');
                if (!button) {
                    return;
                }

                event.preventDefault();

                const fileUrl = button.getAttribute('data-file-url');
                if (!fileUrl) {
                    return;
                }

                const providedType = (button.getAttribute('data-file-type') || '').toLowerCase();
                const extension = detectExtension(fileUrl, providedType);

                openPreview(fileUrl, extension);
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                resetState();
            });
        });
    </script>
@endonce
