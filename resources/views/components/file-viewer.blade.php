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
                    <div class="ratio ratio-16x9">
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
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        body.dark-version #fileViewerModal .modal-content,
        body[data-bs-theme="dark"] #fileViewerModal .modal-content {
            background-color: #0d1117;
            color: #f1f5f9;
        }

        #fileViewerModal .modal-body {
            min-height: 40vh;
            background-color: transparent;
        }

        #fileViewerModal .viewer-image img {
            max-height: 70vh;
            object-fit: contain;
        }

        #fileViewerModal .viewer-pdf iframe {
            min-height: 70vh;
            background-color: var(--bs-body-bg);
        }

        #fileViewerModal .viewer-docx {
            max-height: 70vh;
            overflow-y: auto;
            padding: 1rem;
            border-radius: 0.75rem;
            background-color: var(--bs-body-bg);
            border: 1px solid rgba(148, 163, 184, 0.15);
        }

        body.dark-version #fileViewerModal .viewer-docx,
        body[data-bs-theme="dark"] #fileViewerModal .viewer-docx {
            background-color: rgba(15, 23, 42, 0.78);
            border-color: rgba(148, 163, 184, 0.35);
            color: #f1f5f9;
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

        #fileViewerModal .docx-scroll .docx-wrapper {
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.1);
        }

        body.dark-version #fileViewerModal .docx-scroll .docx-wrapper,
        body[data-bs-theme="dark"] #fileViewerModal .docx-scroll .docx-wrapper {
            background-color: #1f2937;
            color: #f8fafc;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.6);
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

            const PDF_VIEWER_URL = 'https://mozilla.github.io/pdf.js/web/viewer.html?file=';
            const DOCX_SCRIPT_SRC = 'https://cdn.jsdelivr.net/npm/docx-preview@0.3.1/dist/docx-preview.min.js';
            const DOCX_STYLE_HREF = 'https://cdn.jsdelivr.net/npm/docx-preview@0.3.1/dist/docx-preview.min.css';
            const JSZIP_SCRIPT_SRC = 'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js';
            const DOCX_FALLBACK_STYLE = `
                .docx-wrapper {
                    max-width: 900px;
                }

                .docx-wrapper * {
                    color: inherit !important;
                }

                .docx-wrapper table {
                    width: 100% !important;
                }

                .docx-wrapper p {
                    margin-bottom: 0.75rem !important;
                    line-height: 1.6;
                }
            `;
            let docxStyleInjected = false;

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

            function blobToDataUrl(blob) {
                return new Promise(function (resolve, reject) {
                    const reader = new FileReader();
                    reader.addEventListener('loadend', function () {
                        resolve(reader.result);
                    }, { once: true });
                    reader.addEventListener('error', function () {
                        reject(new Error('Gagal membaca file.'));
                    }, { once: true });
                    reader.readAsDataURL(blob);
                });
            }

            function openPdf(url) {
                startLoading('Memuat dokumen PDF...');
                setStatus('Menyiapkan viewer PDF.js (tampilan dokumen)');
                pdfIframe.src = 'about:blank';

                fetchBlob(url)
                    .then(function (blob) {
                        return blobToDataUrl(blob);
                    })
                    .then(function (dataUrl) {
                        pdfIframe.onload = function () {
                            pdfIframe.onload = null;
                            setStatus('');
                            showElement(pdfWrapper);
                        };
                        pdfIframe.onerror = function () {
                            pdfIframe.onerror = null;
                            showMessage('Pratinjau PDF tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                        };
                        pdfIframe.src = PDF_VIEWER_URL + encodeURIComponent(dataUrl);
                    })
                    .catch(function (error) {
                        console.error('Galat memuat PDF:', error);
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
                    pdfIframe.src = 'about:blank';
                }
                if (docxContainer) {
                    docxContainer.innerHTML = '';
                }
            });
        });
    </script>
@endonce
