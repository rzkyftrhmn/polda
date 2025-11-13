<div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="fileViewerModalLabel">Pratinjau Bukti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body position-relative">
                <div class="viewer-loading d-flex flex-column align-items-center justify-content-center py-5 d-none" data-viewer-loading>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-3 mb-0 text-muted small">Sedang menyiapkan pratinjau...</p>
                </div>

                <div class="viewer-image text-center d-none" data-viewer-image>
                    <img src="" alt="Pratinjau file" class="img-fluid rounded shadow" data-viewer-image-el referrerpolicy="no-referrer" />
                </div>

                <div class="viewer-pdf d-none" data-viewer-pdf>
                    <div class="pdf-frame-wrapper">
                        <iframe
                            data-viewer-pdf-iframe
                            class="w-100 h-100 rounded shadow"
                            allowfullscreen
                            referrerpolicy="no-referrer"
                        ></iframe>
                    </div>
                </div>

                <div class="viewer-docx d-none" data-viewer-docx-wrapper>
                    <div class="docx-scroll" data-viewer-docx></div>
                </div>

                <div class="viewer-message d-none" data-viewer-message></div>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 small text-muted" data-viewer-status></div>
                <div class="d-flex gap-2">
                    <a
                        href="#"
                        class="btn btn-outline-primary"
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
</div>

@once
    <style>
        #fileViewerModal .modal-content {
            background: linear-gradient(145deg, rgba(99, 102, 241, 0.08), rgba(37, 99, 235, 0.05));
            color: var(--bs-body-color);
            border: 1px solid rgba(99, 102, 241, 0.25);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(4px);
        }

        body.dark-version #fileViewerModal .modal-content,
        body[data-bs-theme="dark"] #fileViewerModal .modal-content {
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.9));
            color: #f1f5f9;
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.65);
        }

        #fileViewerModal .modal-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95), rgba(59, 130, 246, 0.9));
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
        }

        body.dark-version #fileViewerModal .modal-header,
        body[data-bs-theme="dark"] #fileViewerModal .modal-header {
            border-bottom-color: rgba(99, 102, 241, 0.25);
        }

        #fileViewerModal .modal-footer {
            background: transparent;
            border-top: 1px solid rgba(99, 102, 241, 0.15);
        }

        body.dark-version #fileViewerModal .modal-footer,
        body[data-bs-theme="dark"] #fileViewerModal .modal-footer {
            border-top-color: rgba(99, 102, 241, 0.3);
        }

        #fileViewerModal .modal-body {
            min-height: 40vh;
            background-color: transparent;
        }

        #fileViewerModal .viewer-image img {
            max-height: 70vh;
            object-fit: contain;
        }

        #fileViewerModal .pdf-frame-wrapper {
            position: relative;
            padding-top: 65%;
            border-radius: 1rem;
            background: linear-gradient(160deg, rgba(148, 163, 184, 0.08), rgba(226, 232, 240, 0.15));
            border: 1px solid rgba(99, 102, 241, 0.25);
            overflow: hidden;
        }

        #fileViewerModal .viewer-pdf iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.98);
            border: none;
        }

        body.dark-version #fileViewerModal .pdf-frame-wrapper,
        body[data-bs-theme="dark"] #fileViewerModal .pdf-frame-wrapper {
            background: rgba(15, 23, 42, 0.82);
            border-color: rgba(99, 102, 241, 0.35);
        }

        body.dark-version #fileViewerModal .viewer-pdf iframe,
        body[data-bs-theme="dark"] #fileViewerModal .viewer-pdf iframe {
            background-color: rgba(30, 41, 59, 0.95);
        }

        #fileViewerModal .viewer-docx {
            max-height: 70vh;
            overflow-y: auto;
            padding: 1.5rem;
            border-radius: 1rem;
            background: linear-gradient(160deg, rgba(248, 250, 252, 0.9), rgba(226, 232, 240, 0.6));
            border: 1px solid rgba(148, 163, 184, 0.35);
        }

        body.dark-version #fileViewerModal .viewer-docx,
        body[data-bs-theme="dark"] #fileViewerModal .viewer-docx {
            background: rgba(15, 23, 42, 0.82);
            border-color: rgba(99, 102, 241, 0.35);
            color: #e2e8f0;
        }

        #fileViewerModal .viewer-message {
            max-height: 70vh;
        }

        #fileViewerModal .viewer-loading {
            min-height: 30vh;
        }

        #fileViewerModal [data-viewer-status] {
            min-height: 1.5rem;
        }

        #fileViewerModal .docx-scroll {
            scrollbar-color: rgba(99, 102, 241, 0.45) transparent;
        }

        #fileViewerModal .docx-scroll .docx-wrapper {
            margin: 0 auto;
            background-color: #ffffff;
            color: #1f2937;
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 25px 50px rgba(15, 23, 42, 0.18);
        }

        body.dark-version #fileViewerModal .docx-scroll .docx-wrapper,
        body[data-bs-theme="dark"] #fileViewerModal .docx-scroll .docx-wrapper {
            background-color: #0f172a;
            color: #f8fafc;
            border-color: rgba(148, 163, 184, 0.35);
            box-shadow: 0 25px 55px rgba(15, 23, 42, 0.65);
        }

        #fileViewerModal .viewer-message .alert {
            border-radius: 0.85rem;
            border: none;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
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
            const imageWrapper = modalEl.querySelector('[data-viewer-image]');
            const imageEl = modalEl.querySelector('[data-viewer-image-el]');
            const pdfWrapper = modalEl.querySelector('[data-viewer-pdf]');
            const pdfIframe = modalEl.querySelector('[data-viewer-pdf-iframe]');
            const docxWrapper = modalEl.querySelector('[data-viewer-docx-wrapper]');
            const docxContainer = modalEl.querySelector('[data-viewer-docx]');
            const messageWrapper = modalEl.querySelector('[data-viewer-message]');
            const statusWrapper = modalEl.querySelector('[data-viewer-status]');
            const downloadButton = modalEl.querySelector('[data-download-button]');

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const officeExtensions = ['doc', 'docx'];
            const pdfExtension = 'pdf';

            const DOCX_SCRIPT_SRC = 'https://cdn.jsdelivr.net/npm/docx-preview@0.3.1/dist/docx-preview.min.js';
            const DOCX_STYLE_HREF = 'https://cdn.jsdelivr.net/npm/docx-preview@0.3.1/dist/docx-preview.min.css';
            const JSZIP_SCRIPT_SRC = 'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js';
            const DOCX_FALLBACK_STYLE = `
                .docx-wrapper {
                    max-width: 900px;
                    background: #ffffff !important;
                    color: #1f2937 !important;
                    padding: 2rem !important;
                    border-radius: 1rem;
                    box-shadow: 0 25px 50px rgba(15, 23, 42, 0.18);
                }

                body.dark-version .docx-wrapper,
                body[data-bs-theme="dark"] .docx-wrapper {
                    background: #0f172a !important;
                    color: #f8fafc !important;
                }

                .docx-wrapper table {
                    width: 100% !important;
                    background: transparent !important;
                }

                .docx-wrapper p {
                    margin-bottom: 0.75rem !important;
                    line-height: 1.6;
                }
            `;
            let docxStyleInjected = false;
            let pdfBlobUrl = null;

            function resetPdfBlobUrl() {
                if (pdfBlobUrl) {
                    URL.revokeObjectURL(pdfBlobUrl);
                    pdfBlobUrl = null;
                }
            }

            function hideContent() {
                [imageWrapper, pdfWrapper, docxWrapper, messageWrapper].forEach(function (element) {
                    if (element) {
                        element.classList.add('d-none');
                    }
                });
            }

            function startLoading(message = 'Sedang menyiapkan pratinjau...') {
                hideContent();
                if (statusWrapper) {
                    statusWrapper.textContent = '';
                }
                if (loadingWrapper) {
                    const textEl = loadingWrapper.querySelector('p');
                    if (textEl) {
                        textEl.textContent = message;
                    }
                    loadingWrapper.classList.remove('d-none');
                }
            }

            function stopLoading() {
                if (loadingWrapper) {
                    loadingWrapper.classList.add('d-none');
                }
            }

            function showMessage(message, type = 'info') {
                stopLoading();
                hideContent();
                setStatus('');
                if (!messageWrapper) {
                    return;
                }
                const alertClass = type === 'danger' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info';
                messageWrapper.innerHTML = '<div class="alert ' + alertClass + ' text-center mb-0">' + message + '</div>';
                messageWrapper.classList.remove('d-none');
            }

            function showElement(element) {
                stopLoading();
                hideContent();
                element.classList.remove('d-none');
            }

            function setStatus(message) {
                if (statusWrapper) {
                    statusWrapper.textContent = message || '';
                }
            }

            function ensureScript(src) {
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
                    script.crossOrigin = 'anonymous';
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

            function ensureStylesheet(href) {
                return new Promise(function (resolve, reject) {
                    let link = document.querySelector('link[data-dynamic-href="' + href + '"]');
                    if (link) {
                        if (link.getAttribute('data-loaded') === 'true') {
                            resolve();
                            return;
                        }
                        link.addEventListener('load', function () { resolve(); }, { once: true });
                        link.addEventListener('error', function () { reject(new Error('Gagal memuat stylesheet: ' + href)); }, { once: true });
                        return;
                    }

                    link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = href;
                    link.crossOrigin = 'anonymous';
                    link.referrerPolicy = 'no-referrer';
                    link.setAttribute('data-dynamic-href', href);
                    link.addEventListener('load', function () {
                        link.setAttribute('data-loaded', 'true');
                        resolve();
                    }, { once: true });
                    link.addEventListener('error', function () {
                        link.remove();
                        reject(new Error('Gagal memuat stylesheet: ' + href));
                    }, { once: true });
                    document.head.appendChild(link);
                });
            }

            function fetchBlob(url) {
                return fetch(url, { cache: 'no-store' }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil file.');
                    }
                    return response.blob();
                });
            }

            function openPdf(url) {
                if (!pdfWrapper || !pdfIframe) {
                    showMessage('Pratinjau PDF tidak tersedia pada perangkat ini. Gunakan tombol unduh di bawah.', 'warning');
                    return;
                }

                startLoading('Memuat dokumen PDF...');
                setStatus('Menyiapkan pratinjau PDF');
                resetPdfBlobUrl();
                pdfIframe.removeAttribute('src');
                pdfIframe.removeAttribute('srcdoc');

                fetchBlob(url)
                    .then(function (blob) {
                        const objectUrl = URL.createObjectURL(blob);
                        pdfBlobUrl = objectUrl;

                        pdfIframe.onload = function () {
                            pdfIframe.onload = null;
                            setStatus('');
                            showElement(pdfWrapper);
                        };
                        pdfIframe.onerror = function () {
                            pdfIframe.onerror = null;
                            resetPdfBlobUrl();
                            showMessage('Pratinjau PDF tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                        };

                        setStatus('Menunggu pratinjau ditampilkan...');
                        pdfIframe.src = objectUrl + '#toolbar=0&view=fitH';
                    })
                    .catch(function (error) {
                        console.error('Galat memuat PDF:', error);
                        resetPdfBlobUrl();
                        showMessage('Pratinjau PDF tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                    });
            }

            function openImage(url) {
                startLoading('Memuat gambar...');
                imageEl.onload = function () {
                    imageEl.onload = null;
                    setStatus('');
                    showElement(imageWrapper);
                };
                imageEl.onerror = function () {
                    imageEl.onerror = null;
                    showMessage('Pratinjau gambar tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                };
                imageEl.src = url;
            }

            function ensureDocxAssets() {
                return ensureScript(JSZIP_SCRIPT_SRC)
                    .then(function () { return ensureScript(DOCX_SCRIPT_SRC); })
                    .then(function () {
                        return ensureStylesheet(DOCX_STYLE_HREF).catch(function () {
                            if (!docxStyleInjected) {
                                const style = document.createElement('style');
                                style.textContent = DOCX_FALLBACK_STYLE;
                                style.setAttribute('data-docx-fallback', 'true');
                                document.head.appendChild(style);
                                docxStyleInjected = true;
                            }
                        });
                    });
            }

            function openDocx(url) {
                startLoading('Memuat dokumen Word...');
                setStatus('Menyiapkan docx-preview (tampilan dokumen)');
                docxContainer.innerHTML = '';

                ensureDocxAssets()
                    .then(function () {
                        if (!window.docx || typeof window.docx.renderAsync !== 'function') {
                            throw new Error('docx-preview tidak tersedia.');
                        }
                        if (!window.JSZip) {
                            throw new Error('JSZip tidak tersedia.');
                        }
                        return fetchBlob(url);
                    })
                    .then(function (blob) {
                        return window.docx.renderAsync(blob, docxContainer, null, {
                            className: 'docx-wrapper',
                            inWrapper: true,
                            ignoreWidth: false,
                            ignoreHeight: false,
                            breakPages: true,
                        });
                    })
                    .then(function () {
                        setStatus('');
                        showElement(docxWrapper);
                    })
                    .catch(function (error) {
                        console.error('Galat memuat DOCX:', error);
                        showMessage('Pratinjau dokumen tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                    });
            }

            function getFileNameFromUrl(url) {
                if (!url) {
                    return 'unduhan';
                }
                try {
                    const withoutQuery = url.split('?')[0];
                    const decoded = decodeURIComponent(withoutQuery);
                    const segments = decoded.split('/');
                    return segments.pop() || 'unduhan';
                } catch (error) {
                    return 'unduhan';
                }
            }

            function openPreview(url, type, fileName) {
                if (!url) {
                    showMessage('URL file tidak ditemukan.', 'danger');
                    return;
                }

                let extension = (type || '').toLowerCase();
                if (extension.includes('/')) {
                    extension = extension.split('/').pop();
                }
                if (!extension) {
                    extension = (url.split('?')[0].split('.').pop() || '').toLowerCase();
                }
                const safeFileName = fileName || getFileNameFromUrl(url);

                if (downloadButton) {
                    downloadButton.href = url;
                    downloadButton.setAttribute('download', safeFileName);
                }

                resetPdfBlobUrl();

                if (imageExtensions.includes(extension)) {
                    openImage(url);
                    return;
                }

                if (extension === pdfExtension) {
                    openPdf(url);
                    return;
                }

                if (officeExtensions.includes(extension)) {
                    openDocx(url);
                    return;
                }

                showMessage('Pratinjau tidak tersedia. Gunakan tombol unduh di bawah.', 'info');
            }

            document.body.addEventListener('click', function (event) {
                const trigger = event.target.closest('.btn-preview-file');
                if (!trigger) {
                    return;
                }

                event.preventDefault();
                const url = trigger.getAttribute('data-file-url');
                const type = trigger.getAttribute('data-file-type');
                const fileName = trigger.getAttribute('data-file-name');

                openPreview(url, type, fileName);
                modalInstance.show();
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                hideContent();
                stopLoading();
                setStatus('');
                if (imageEl) {
                    imageEl.src = '';
                }
                if (pdfIframe) {
                    pdfIframe.removeAttribute('src');
                    pdfIframe.removeAttribute('srcdoc');
                }
                resetPdfBlobUrl();
                if (docxContainer) {
                    docxContainer.innerHTML = '';
                }
            });
        });
    </script>
@endonce
