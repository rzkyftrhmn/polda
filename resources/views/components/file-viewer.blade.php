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
            const messageWrapper = modalEl.querySelector('[data-viewer-message]');
            const downloadButton = modalEl.querySelector('[data-download-button]');
            const flipbookButton = modalEl.querySelector('[data-open-flipbook]');

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const officeExtensions = ['doc', 'docx'];
            const pdfExtension = 'pdf';

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

            function resetState() {
                [loadingWrapper, frameWrapper, imageWrapper, messageWrapper, docxWrapper].forEach(function (element) {
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

                if (docxWrapper) {
                    docxWrapper.innerHTML = '';
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

                const text = message || 'Pratinjau tidak tersedia. Silakan unduh file untuk melihat isinya.';
                const extra = options.extraHtml || (currentUrl
                    ? `<a href="${currentUrl}" target="_blank" rel="noopener" class="btn btn-link p-0">Unduh file secara manual</a>`
                    : ''
                );

                messageWrapper.innerHTML = `
                    <div class="alert alert-info">${text}</div>
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

            function showFrame(src, fallbackMessage, options = {}) {
                if (!iframeEl || !frameWrapper) {
                    showMessage(fallbackMessage);
                    return;
                }

                showLoading();

                const handleLoad = function () {
                    hideLoading();
                    frameTimeout && clearTimeout(frameTimeout);
                    frameTimeout = null;
                };

                iframeEl.removeAttribute('data-loaded');
                iframeEl.addEventListener('load', handleLoad, { once: true });

                const timeout = typeof options.timeout === 'number' ? options.timeout : 12000;

                if (timeout > 0) {
                    frameTimeout = window.setTimeout(function () {
                        frameTimeout = null;
                        showMessage(fallbackMessage);
                        if (typeof options.onTimeout === 'function') {
                            options.onTimeout();
                        }
                    }, timeout);
                }

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

            function openOfficePreview(url) {
                if (!iframeEl || !frameWrapper) {
                    showMessage('Pratinjau dokumen tidak dapat dimuat. Silakan unduh file.');
                    modalInstance.show();
                    return;
                }

                const officeUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(url);

                showFrame(officeUrl, 'Pratinjau dokumen tidak dapat dimuat. Silakan unduh file.', {
                    timeout: 20000,
                    onTimeout: function () {
                        if (!messageWrapper) {
                            return;
                        }

                        messageWrapper.innerHTML = `
                            <div class="alert alert-warning mb-2">Pratinjau dokumen tidak dapat dimuat.</div>
                            <p class="mb-0">Gunakan tombol unduh untuk membuka file melalui Microsoft Office.</p>
                        `;
                    },
                });

                modalInstance.show();
            }

            function openPdfFallback(url) {
                showFrame(url, 'Pratinjau PDF tidak tersedia. Silakan gunakan tombol unduh.', { timeout: 15000 });

                if (flipbookButton) {
                    flipbookButton.classList.remove('d-none');
                    flipbookButton.setAttribute('data-file-url', url);
                }

                modalInstance.show();
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

                    if (window.ReportFlipbook && typeof window.ReportFlipbook.open === 'function') {
                        window.ReportFlipbook.open(resolvedUrl);
                        return;
                    }

                    openPdfFallback(resolvedUrl);
                    return;
                }

                if (imageExtensions.includes(normalizedExtension)) {
                    showImagePreview(resolvedUrl);
                    modalInstance.show();
                    return;
                }

                if (officeExtensions.includes(normalizedExtension)) {
                    openOfficePreview(resolvedUrl);
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
                        pendingFlipbookUrl = currentUrl;
                        window.ReportFlipbook.open(currentUrl);
                        modalInstance.hide();
                    } else {
                        showMessage('Mode flipbook belum tersedia. Silakan unduh atau buka pratinjau standar.');
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

                resetState();
                enableDownload(failedUrl);
                openPdfFallback(failedUrl);
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
