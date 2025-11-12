<div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="fileViewerModalLabel">Pratinjau Bukti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                        data-viewer-iframe
                    ></iframe>
                </div>
                <div class="text-center d-none" data-viewer-image>
                    <img src="" alt="Pratinjau file" class="img-fluid rounded shadow" data-viewer-image-el>
                </div>
                <div class="d-none" data-viewer-docx>
                    <div class="bg-body-secondary rounded shadow-sm p-3" data-viewer-docx-el></div>
                </div>
                <div class="d-none" data-viewer-message></div>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary d-none" data-open-flipbook>
                        <i class="fa fa-book-open me-1"></i> Mode Flipbook
                    </button>
                    <a
                        href="#"
                        class="btn btn-primary d-none"
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
            const docxContainer = modalEl.querySelector('[data-viewer-docx-el]');
            const messageWrapper = modalEl.querySelector('[data-viewer-message]');
            const downloadButton = modalEl.querySelector('[data-download-button]');
            const flipbookButton = modalEl.querySelector('[data-open-flipbook]');

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const docxExtensions = ['docx'];
            const officeExtensions = ['doc'];
            const pdfExtension = 'pdf';

            const DOCX_SCRIPT_SRC = 'https://cdn.jsdelivr.net/npm/docx-preview@0.4.0/dist/docx-preview.min.js';
            const DOCX_STYLE_SRC = 'https://cdn.jsdelivr.net/npm/docx-preview@0.4.0/dist/docx-preview.min.css';

            let currentUrl = null;
            let frameTimeout = null;
            let docxLibraryPromise = null;

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
                        script.addEventListener('error', function () { reject(new Error('Gagal memuat script: ' + src)); }, { once: true });
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
                        reject(new Error('Gagal memuat script: ' + src));
                    }, { once: true });

                    document.head.appendChild(script);
                });
            }

            function loadStyleOnce(href) {
                return new Promise(function (resolve, reject) {
                    let link = document.querySelector('link[data-dynamic-href="' + href + '"]');

                    if (link) {
                        resolve();
                        return;
                    }

                    link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = href;
                    link.setAttribute('data-dynamic-href', href);
                    link.addEventListener('load', function () { resolve(); }, { once: true });
                    link.addEventListener('error', function () {
                        link.remove();
                        reject(new Error('Gagal memuat stylesheet: ' + href));
                    }, { once: true });

                    document.head.appendChild(link);
                });
            }

            function ensureDocxLibrary() {
                if (!docxLibraryPromise) {
                    docxLibraryPromise = Promise.all([
                        loadStyleOnce(DOCX_STYLE_SRC),
                        loadScriptOnce(DOCX_SCRIPT_SRC),
                    ]).then(function () {
                        if (!window.docx || typeof window.docx.renderAsync !== 'function') {
                            throw new Error('docx-preview tidak tersedia');
                        }
                    }).catch(function (error) {
                        docxLibraryPromise = null;
                        throw error;
                    });
                }

                return docxLibraryPromise;
            }

            function resetState() {
                [loadingWrapper, frameWrapper, imageWrapper, docxWrapper, messageWrapper].forEach(function (element) {
                    if (!element) {
                        return;
                    }

                    element.classList.add('d-none');
                });

                if (iframeEl) {
                    iframeEl.src = 'about:blank';
                }

                if (imageEl) {
                    imageEl.src = '';
                }

                if (docxContainer) {
                    docxContainer.innerHTML = '';
                }

                if (messageWrapper) {
                    messageWrapper.innerHTML = '';
                }

                if (downloadButton) {
                    downloadButton.classList.add('d-none');
                    downloadButton.removeAttribute('href');
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

            function showMessage(message, options = {}) {
                hideLoading();

                if (!messageWrapper) {
                    return;
                }

                if (frameWrapper) {
                    frameWrapper.classList.add('d-none');
                }

                if (imageWrapper) {
                    imageWrapper.classList.add('d-none');
                }

                if (docxWrapper) {
                    docxWrapper.classList.add('d-none');
                }

                const text = message || 'Pratinjau tidak tersedia. Silakan unduh file untuk melihat isinya.';
                const extra = options.extraHtml || (currentUrl
                    ? `<a href="${currentUrl}" target="_blank" rel="noopener" class="btn btn-link p-0">Unduh file secara manual</a>`
                    : ''
                );

                messageWrapper.innerHTML = `
                    <div class="alert alert-info">
                        ${text}
                    </div>
                    ${extra}
                `;

                messageWrapper.classList.remove('d-none');
            }

            function enableDownload(url) {
                if (!downloadButton) {
                    return;
                }

                downloadButton.href = url;
                downloadButton.classList.remove('d-none');
            }

            function showImagePreview(url) {
                if (!imageWrapper || !imageEl) {
                    showMessage();
                    return;
                }

                showLoading();

                imageEl.onload = function () {
                    hideLoading();
                };

                imageEl.onerror = function () {
                    showMessage('Gagal memuat pratinjau gambar. Gunakan tombol unduh di bawah.');
                };

                imageEl.src = url;
                imageWrapper.classList.remove('d-none');
            }

            function showDocxPreview(url) {
                if (!docxWrapper || !docxContainer) {
                    showMessage('Pratinjau dokumen tidak tersedia. Silakan unduh file.');
                    return;
                }

                showLoading();
                docxContainer.innerHTML = '';

                ensureDocxLibrary()
                    .then(function () {
                        return fetch(url, { credentials: 'same-origin' });
                    })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Response tidak valid');
                        }

                        return response.arrayBuffer();
                    })
                    .then(function (buffer) {
                        return window.docx.renderAsync(buffer, docxContainer, undefined, {
                            className: 'docx-preview-content',
                            inWrapper: true,
                            ignoreWidth: true,
                            ignoreHeight: true,
                            breakPages: false,
                        });
                    })
                    .then(function () {
                        hideLoading();
                        docxWrapper.classList.remove('d-none');
                    })
                    .catch(function (error) {
                        console.error('DOCX preview gagal:', error);
                        docxWrapper.classList.add('d-none');
                        showMessage('Pratinjau dokumen tidak dapat dimuat. Silakan unduh file.');
                    });
            }

            function showFrame(src, fallbackMessage) {
                if (!iframeEl || !frameWrapper) {
                    showMessage(fallbackMessage);
                    return;
                }

                hideLoading();
                showLoading();

                const handleLoad = function () {
                    hideLoading();
                    frameTimeout && clearTimeout(frameTimeout);
                    frameTimeout = null;
                };

                iframeEl.removeAttribute('data-loaded');
                iframeEl.addEventListener('load', handleLoad, { once: true });

                frameTimeout = window.setTimeout(function () {
                    showMessage(fallbackMessage);
                }, 12000);

                iframeEl.src = src;
                frameWrapper.classList.remove('d-none');
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

                if (normalizedExtension === pdfExtension) {
                    if (window.ReportFlipbook && typeof window.ReportFlipbook.open === 'function') {
                        window.ReportFlipbook.open(resolvedUrl);
                        return;
                    }

                    const viewerUrl = 'https://mozilla.github.io/pdf.js/web/viewer.html?file=' + encodeURIComponent(resolvedUrl);
                    showFrame(viewerUrl, 'Pratinjau PDF tidak tersedia. Silakan gunakan tombol unduh.');

                    if (flipbookButton) {
                        flipbookButton.classList.remove('d-none');
                        flipbookButton.setAttribute('data-file-url', resolvedUrl);
                    }

                    modalInstance.show();
                    return;
                }

                if (imageExtensions.includes(normalizedExtension)) {
                    showImagePreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (docxExtensions.includes(normalizedExtension)) {
                    showDocxPreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (officeExtensions.includes(normalizedExtension)) {
                    const officeUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(resolvedUrl);
                    showFrame(officeUrl, 'Pratinjau dokumen tidak dapat dimuat. Silakan unduh file.');
                    modalInstance.show();
                    return;
                }

                hideLoading();
                showMessage('Pratinjau tidak tersedia untuk tipe file ini. Silakan unduh file.');
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
                        window.ReportFlipbook.open(currentUrl);
                        modalInstance.hide();
                    } else {
                        showMessage('Mode flipbook belum tersedia. Silakan unduh atau buka pratinjau standar.');
                    }
                });
            }

            modalEl.addEventListener('hidden.bs.modal', function () {
                resetState();
                currentUrl = null;
            });
        });
    </script>
@endonce
