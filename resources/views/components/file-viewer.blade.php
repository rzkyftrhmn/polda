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
                <div class="ratio ratio-16x9 d-none" data-viewer-frame>
                    <iframe
                        src="about:blank"
                        title="File preview"
                        frameborder="0"
                        allowfullscreen
                        class="rounded shadow-sm"
                        referrerpolicy="no-referrer"
                        data-viewer-iframe
                    ></iframe>
                </div>
                <div class="text-center d-none" data-viewer-image>
                    <img src="" alt="Pratinjau file" class="img-fluid rounded shadow" data-viewer-image-el>
                </div>
                <div class="docx-preview-wrapper d-none" data-viewer-docx></div>
                <div class="d-none" data-viewer-message></div>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary d-none" data-open-flipbook>
                        <i class="fa fa-book-open me-1"></i> Mode Flipbook
                    </button>
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
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@once
    <style>
        #fileViewerModal .modal-body iframe {
            background-color: var(--bs-body-bg);
        }

        body.dark-version #fileViewerModal .modal-content,
        body[data-bs-theme="dark"] #fileViewerModal .modal-content {
            color: #f1f5f9;
            background-color: #0d1117;
        }

        body.dark-version #fileViewerModal .modal-body iframe,
        body[data-bs-theme="dark"] #fileViewerModal .modal-body iframe {
            background-color: #0d1117;
        }

        body.dark-version #fileViewerModal .docx-preview-wrapper,
        body[data-bs-theme="dark"] #fileViewerModal .docx-preview-wrapper {
            color: #f1f5f9;
        }

        #fileViewerModal .docx-preview-wrapper {
            max-height: 70vh;
            overflow-y: auto;
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
            const frameWrapper = modalEl.querySelector('[data-viewer-frame]');
            const iframeEl = modalEl.querySelector('[data-viewer-iframe]');
            const imageWrapper = modalEl.querySelector('[data-viewer-image]');
            const imageEl = modalEl.querySelector('[data-viewer-image-el]');
            const docxWrapper = modalEl.querySelector('[data-viewer-docx]');
            const messageWrapper = modalEl.querySelector('[data-viewer-message]');
            const downloadButton = modalEl.querySelector('[data-download-button]');
            const flipbookButton = modalEl.querySelector('[data-open-flipbook]');

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const officeExtensions = ['doc', 'docx'];
            const pdfExtension = 'pdf';

            const MAMMOTH_SRC = 'https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js';

            let currentUrl = null;
            let frameTimeout = null;
            let pendingFlipbookUrl = null;

            function resolveUrl(url) {
                if (!url) {
                    return '';
                }

                try {
                    return new URL(url, window.location.origin).href;
                } catch (error) {
                    return url;
                }
            }

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

            function resetState() {
                [loadingWrapper, frameWrapper, imageWrapper, messageWrapper, docxWrapper].forEach(function (element) {
                    if (!element) {
                        return;
                    }

                    element.classList.add('d-none');
                });

                if (iframeEl) {
                    iframeEl.removeAttribute('srcdoc');
                    iframeEl.src = 'about:blank';
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
                    downloadButton.classList.remove('disabled');
                    downloadButton.href = '#';
                }

                if (flipbookButton) {
                    flipbookButton.classList.add('d-none');
                    flipbookButton.removeAttribute('data-file-url');
                }

                if (frameTimeout) {
                    clearTimeout(frameTimeout);
                    frameTimeout = null;
                }
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

                const alertType = type === 'warning' ? 'alert-warning' : 'alert-info';
                const manualLink = currentUrl
                    ? `<a href="${currentUrl}" target="_blank" rel="noopener" class="btn btn-link p-0">Gunakan tombol unduh di bawah</a>`
                    : '';

                messageWrapper.innerHTML = `
                    <div class="alert ${alertType} text-center mb-0">${message}</div>
                    <div class="text-center mt-2">${manualLink}</div>
                `;

                messageWrapper.classList.remove('d-none');
            }

            function enableDownload(url) {
                if (!downloadButton) {
                    return;
                }

                downloadButton.href = url;
            }

            function setThemeColor(element) {
                if (!element) {
                    return;
                }

                const isDark = document.body.classList.contains('dark-version') || document.body.getAttribute('data-bs-theme') === 'dark';

                element.classList.toggle('text-white', isDark);
                element.classList.toggle('bg-dark', isDark);
            }

            function showImagePreview(url) {
                if (!imageWrapper || !imageEl) {
                    showMessage('Pratinjau gambar tidak tersedia.');
                    return;
                }

                showLoading();

                imageEl.onload = function () {
                    hideLoading();
                };

                imageEl.onerror = function () {
                    showMessage('Gagal memuat pratinjau gambar. Silakan unduh file.');
                };

                imageEl.src = url;
                imageWrapper.classList.remove('d-none');
            }

            function fetchAsBase64(url) {
                return fetch(url, { credentials: 'include' })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Gagal mengambil file');
                        }

                        return response.arrayBuffer();
                    })
                    .then(function (buffer) {
                        const bytes = new Uint8Array(buffer);
                        const chunkSize = 0x8000;
                        let binary = '';

                        for (let i = 0; i < bytes.length; i += chunkSize) {
                            const chunk = bytes.subarray(i, i + chunkSize);
                            binary += String.fromCharCode.apply(null, chunk);
                        }

                        return window.btoa(binary);
                    });
            }

            function showPdfPreview(url) {
                if (!iframeEl || !frameWrapper) {
                    showMessage('Pratinjau PDF tidak tersedia.');
                    return;
                }

                showLoading();
                frameWrapper.classList.remove('d-none');
                setThemeColor(frameWrapper);

                fetchAsBase64(url)
                    .then(function (base64) {
                        const dataUrl = 'data:application/pdf;base64,' + base64;
                        const viewerUrl = 'https://mozilla.github.io/pdf.js/web/viewer.html?file=' + encodeURIComponent(dataUrl);

                        iframeEl.src = viewerUrl;

                        iframeEl.addEventListener('load', function handleLoad() {
                            hideLoading();
                            iframeEl.removeEventListener('load', handleLoad);
                        }, { once: true });

                        frameTimeout = window.setTimeout(function () {
                            hideLoading();
                        }, 12000);
                    })
                    .catch(function (error) {
                        console.error('Gagal memuat PDF:', error);
                        hideLoading();
                        showMessage('Pratinjau PDF tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                    });

                if (flipbookButton) {
                    flipbookButton.classList.remove('d-none');
                    flipbookButton.setAttribute('data-file-url', url);
                }
            }

            function ensureMammoth() {
                if (window.mammoth) {
                    return Promise.resolve();
                }

                return loadScriptOnce(MAMMOTH_SRC);
            }

            function showDocxPreview(url) {
                if (!docxWrapper) {
                    showMessage('Pratinjau dokumen tidak tersedia.');
                    return;
                }

                showLoading();
                docxWrapper.classList.remove('d-none');
                setThemeColor(docxWrapper);

                ensureMammoth()
                    .then(function () {
                        return fetch(url, { credentials: 'include' });
                    })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Gagal mengambil dokumen');
                        }

                        return response.arrayBuffer();
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
                        hideLoading();
                        docxWrapper.classList.add('d-none');
                        showMessage('Pratinjau dokumen tidak dapat dimuat. Gunakan tombol unduh di bawah.', 'warning');
                    });
            }

            function detectExtension(url, provided) {
                if (provided) {
                    return provided.replace('.', '').toLowerCase();
                }

                const parts = url.split('?')[0].split('#')[0].split('.');
                return parts.length > 1 ? parts.pop().toLowerCase() : '';
            }

            function openPreview(url, extension) {
                const resolvedUrl = resolveUrl(url);
                currentUrl = resolvedUrl;

                resetState();
                showLoading();
                enableDownload(resolvedUrl);

                const normalizedExtension = (extension || '').toLowerCase();
                pendingFlipbookUrl = null;

                if (normalizedExtension === pdfExtension) {
                    pendingFlipbookUrl = resolvedUrl;
                    showPdfPreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (imageExtensions.includes(normalizedExtension)) {
                    showImagePreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (officeExtensions.includes(normalizedExtension)) {
                    showDocxPreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                hideLoading();
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

            if (flipbookButton) {
                flipbookButton.addEventListener('click', function () {
                    if (!currentUrl) {
                        return;
                    }

                    if (typeof window.ReportFlipbook === 'object' && typeof window.ReportFlipbook.open === 'function') {
                        pendingFlipbookUrl = currentUrl;
                        window.ReportFlipbook.open(currentUrl);
                        modalInstance.hide();
                    } else {
                        showMessage('Mode flipbook belum siap. Gunakan pratinjau standar atau unduh file.', 'warning');
                    }
                });
            }

            window.addEventListener('report-flipbook:failed', function (event) {
                if (!pendingFlipbookUrl) {
                    return;
                }

                const failedUrl = event.detail && event.detail.url ? resolveUrl(event.detail.url) : null;
                if (!failedUrl || failedUrl !== resolveUrl(pendingFlipbookUrl)) {
                    return;
                }

                pendingFlipbookUrl = null;
                showPdfPreview(failedUrl);
                modalInstance.show();
            });

            window.addEventListener('report-flipbook:opened', function (event) {
                const openedUrl = event.detail && event.detail.url ? resolveUrl(event.detail.url) : null;
                if (!openedUrl) {
                    return;
                }

                if (pendingFlipbookUrl && resolveUrl(pendingFlipbookUrl) === openedUrl) {
                    pendingFlipbookUrl = null;
                }
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                resetState();
                currentUrl = null;
            });
        });
    </script>
@endonce
