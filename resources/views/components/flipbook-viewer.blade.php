@once
    <div class="modal fade" id="flipbookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Pratinjau Dokumen (Flipbook)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="d-flex justify-content-center align-items-center py-5" data-flipbook-loading>
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>
                    <div class="position-relative w-100" style="height: 80vh;">
                        <div id="flipbookContainer" class="w-100 h-100" data-flipbook-container></div>
                    </div>
                    <div class="p-4 d-none" data-flipbook-message></div>
                </div>
                <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                    <div class="btn-group" role="group" aria-label="Kontrol zoom">
                        <button type="button" class="btn btn-outline-light" data-flipbook-zoom-out title="Perkecil">
                            <i class="fa fa-search-minus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-light" data-flipbook-zoom-reset title="Reset">
                            <i class="fa fa-compress"></i>
                        </button>
                        <button type="button" class="btn btn-outline-light" data-flipbook-zoom-in title="Perbesar">
                            <i class="fa fa-search-plus"></i>
                        </button>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a
                            href="#"
                            class="btn btn-primary"
                            target="_blank"
                            rel="noopener"
                            download
                            data-flipbook-download
                        >
                            <i class="fa fa-download me-1"></i> Unduh File
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #flipbookModal .modal-body {
            background: var(--bs-body-bg);
        }

        body.dark-version #flipbookModal .modal-body,
        body[data-bs-theme="dark"] #flipbookModal .modal-body {
            background: #111;
            color: #f1f5f9;
        }

        #flipbookModal [data-flipbook-container] {
            background: transparent;
        }

        #flipbookModal [data-flipbook-container].flipbook-ready {
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.35);
        }
    </style>
@endonce

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('flipbookModal');
            if (!modalEl) {
                return;
            }

            const container = modalEl.querySelector('[data-flipbook-container]');
            const loadingWrapper = modalEl.querySelector('[data-flipbook-loading]');
            const messageWrapper = modalEl.querySelector('[data-flipbook-message]');
            const downloadButton = modalEl.querySelector('[data-flipbook-download]');
            const zoomInBtn = modalEl.querySelector('[data-flipbook-zoom-in]');
            const zoomOutBtn = modalEl.querySelector('[data-flipbook-zoom-out]');
            const zoomResetBtn = modalEl.querySelector('[data-flipbook-zoom-reset]');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

            const JQUERY_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js';
            const PDF_JS_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.js';
            const PDF_WORKER_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.js';
            const TURN_JS_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js';

            let currentUrl = null;
            let currentScale = 1;
            let libraryPromise = null;

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

            function ensureLibraries() {
                if (!libraryPromise) {
                    libraryPromise = Promise.resolve()
                        .then(function () {
                            if (window.jQuery) {
                                return;
                            }

                            return loadScriptOnce(JQUERY_CDN).then(function () {
                                if (!window.jQuery) {
                                    throw new Error('jQuery tidak tersedia');
                                }
                            });
                        })
                        .then(function () {
                            if (window.pdfjsLib) {
                                return;
                            }

                            return loadScriptOnce(PDF_JS_CDN).then(function () {
                                if (!window.pdfjsLib) {
                                    throw new Error('pdf.js tidak tersedia');
                                }

                                window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDF_WORKER_CDN;
                            });
                        })
                        .then(function () {
                            if (window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.turn === 'function') {
                                return;
                            }

                            return loadScriptOnce(TURN_JS_CDN).then(function () {
                                if (!window.jQuery || !window.jQuery.fn || typeof window.jQuery.fn.turn !== 'function') {
                                    throw new Error('Turn.js tidak tersedia');
                                }
                            });
                        })
                        .catch(function (error) {
                            console.error('Gagal memuat library flipbook:', error);
                            libraryPromise = null;
                            throw error;
                        });
                }

                return libraryPromise;
            }

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

            function resetView() {
                currentScale = 1;

                if (loadingWrapper) {
                    loadingWrapper.classList.remove('d-none');
                }

                if (messageWrapper) {
                    messageWrapper.classList.add('d-none');
                    messageWrapper.innerHTML = '';
                }

                if (downloadButton) {
                    downloadButton.href = '#';
                }

                if (container) {
                    if (window.jQuery && window.jQuery(container).data('turn')) {
                        window.jQuery(container).turn('destroy');
                    }

                    container.innerHTML = '';
                    container.classList.remove('flipbook-ready');
                    container.style.transform = 'scale(1)';
                    container.style.transformOrigin = 'center center';
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

            function showMessage(message) {
                hideLoading();

                if (!messageWrapper) {
                    return;
                }

                const isDark = document.body.classList.contains('dark-version') || document.body.getAttribute('data-bs-theme') === 'dark';
                const alertClass = isDark ? 'alert-info' : 'alert-warning';
                const manualLink = currentUrl
                    ? `<a href="${currentUrl}" target="_blank" rel="noopener" class="btn btn-link p-0">Gunakan tombol unduh di bawah</a>`
                    : '';

                messageWrapper.innerHTML = `
                    <div class="alert ${alertClass} mb-3">${message}</div>
                    <p class="mb-0">${manualLink}</p>
                `;

                messageWrapper.classList.remove('d-none');
            }

            function ensureDownload(url) {
                if (!downloadButton) {
                    return;
                }

                downloadButton.href = url;
            }

            function renderFlipbook(url) {
                if (!container || !window.pdfjsLib) {
                    return Promise.reject(new Error('Container atau pdf.js tidak siap'));
                }

                return window.pdfjsLib
                    .getDocument({ url: url, withCredentials: true })
                    .promise.then(function (pdf) {
                        container.innerHTML = '';
                        const renderTasks = [];

                        for (let page = 1; page <= pdf.numPages; page++) {
                            renderTasks.push(
                                pdf.getPage(page).then(function (pdfPage) {
                                    const viewport = pdfPage.getViewport({ scale: 1.2 });
                                    const canvas = document.createElement('canvas');
                                    const context = canvas.getContext('2d');
                                    canvas.width = viewport.width;
                                    canvas.height = viewport.height;

                                    return pdfPage.render({ canvasContext: context, viewport: viewport }).promise.then(function () {
                                        const wrapper = document.createElement('div');
                                        wrapper.className = 'page bg-white d-flex justify-content-center align-items-center';
                                        wrapper.appendChild(canvas);
                                        container.appendChild(wrapper);
                                    });
                                })
                            );
                        }

                        return Promise.all(renderTasks)
                            .then(function () {
                                hideLoading();

                                const $container = window.jQuery(container);

                                if ($container.data('turn')) {
                                    $container.turn('destroy');
                                }

                                const modalBody = modalEl.querySelector('.modal-body');
                                const width = (modalBody ? modalBody.clientWidth : container.clientWidth) || 960;
                                const height = (modalBody ? modalBody.clientHeight : container.clientHeight) || 600;

                                $container.turn({
                                    width: width,
                                    height: height,
                                    autoCenter: true,
                                    gradients: true,
                                    duration: 900,
                                    elevation: 70,
                                    display: 'double',
                                    when: {
                                        turning: function () {
                                            container.classList.add('turning');
                                        },
                                        turned: function () {
                                            container.classList.remove('turning');
                                        },
                                    },
                                });

                                container.classList.add('flipbook-ready');
                            });
                    });
            }

            function setScale(scale) {
                currentScale = Math.min(Math.max(scale, 0.5), 3);
                container.style.transform = 'scale(' + currentScale + ')';
            }

            function zoomIn() {
                setScale(currentScale + 0.25);
            }

            function zoomOut() {
                setScale(currentScale - 0.25);
            }

            function zoomReset() {
                setScale(1);
            }

            window.ReportFlipbook = window.ReportFlipbook || {};
            window.ReportFlipbook.open = function (url) {
                const resolvedUrl = resolveUrl(url);
                currentUrl = resolvedUrl;

                resetView();

                if (!resolvedUrl) {
                    showMessage('File PDF tidak ditemukan.');
                    modalInstance.show();
                    window.dispatchEvent(new CustomEvent('report-flipbook:failed', { detail: { url: resolvedUrl } }));
                    return;
                }

                ensureDownload(resolvedUrl);
                showLoading();
                modalInstance.show();

                ensureLibraries()
                    .then(function () {
                        return renderFlipbook(resolvedUrl);
                    })
                    .then(function () {
                        setScale(1.1);
                        window.dispatchEvent(new CustomEvent('report-flipbook:opened', { detail: { url: resolvedUrl } }));
                    })
                    .catch(function (error) {
                        console.error('Flipbook gagal dimuat:', error);
                        showMessage('Mode flipbook tidak dapat dimuat. Gunakan tombol unduh untuk membuka file.');
                        window.dispatchEvent(new CustomEvent('report-flipbook:failed', { detail: { url: resolvedUrl } }));
                    });
            };

            modalEl.addEventListener('hidden.bs.modal', function () {
                if (window.jQuery && container && window.jQuery(container).data('turn')) {
                    window.jQuery(container).turn('destroy');
                }

                currentUrl = null;
                resetView();
            });

            if (zoomInBtn) {
                zoomInBtn.addEventListener('click', function () {
                    zoomIn();
                });
            }

            if (zoomOutBtn) {
                zoomOutBtn.addEventListener('click', function () {
                    zoomOut();
                });
            }

            if (zoomResetBtn) {
                zoomResetBtn.addEventListener('click', function () {
                    zoomReset();
                });
            }
        });
    </script>
@endonce
