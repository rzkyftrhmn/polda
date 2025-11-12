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
                <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                    <a
                        href="#"
                        class="btn btn-primary d-none"
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
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

            const PDF_JS_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js';
            const PDF_WORKER_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            const TURN_JS_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js';
            const JQUERY_SRC = 'https://code.jquery.com/jquery-3.6.0.min.js';

            let currentUrl = null;
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

            function ensureJquery() {
                if (window.jQuery && typeof window.jQuery === 'function') {
                    return Promise.resolve(window.jQuery);
                }

                return loadScriptOnce(JQUERY_SRC).then(function () {
                    if (!window.jQuery) {
                        throw new Error('jQuery tidak tersedia');
                    }

                    return window.jQuery;
                });
            }

            function ensureLibraries() {
                if (!libraryPromise) {
                    libraryPromise = ensureJquery()
                        .then(function () {
                            return loadScriptOnce(PDF_JS_SRC);
                        })
                        .then(function () {
                            if (!window.pdfjsLib) {
                                throw new Error('pdf.js tidak ditemukan');
                            }

                            window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDF_WORKER_SRC;

                            return loadScriptOnce(TURN_JS_SRC);
                        })
                        .then(function () {
                            if (!window.jQuery || !window.jQuery.fn.turn) {
                                throw new Error('Turn.js tidak tersedia');
                            }
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
                if (loadingWrapper) {
                    loadingWrapper.classList.remove('d-none');
                }

                if (messageWrapper) {
                    messageWrapper.classList.add('d-none');
                    messageWrapper.innerHTML = '';
                }

                if (downloadButton) {
                    downloadButton.classList.add('d-none');
                    downloadButton.removeAttribute('href');
                }

                if (container) {
                    if (window.jQuery && window.jQuery(container).data('turn')) {
                        window.jQuery(container).turn('destroy').removeClass('shadow');
                    }

                    container.innerHTML = '';
                    container.classList.remove('d-flex', 'flex-column', 'gap-3', 'p-3');
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

                const manualLink = currentUrl
                    ? `<a href="${currentUrl}" target="_blank" rel="noopener" class="btn btn-link p-0">Unduh file secara manual</a>`
                    : '';

                messageWrapper.innerHTML = `
                    <div class="alert alert-warning mb-3">${message}</div>
                    <p class="mb-2">Gunakan tombol unduh di bawah untuk membuka file secara manual.</p>
                    ${manualLink}
                `;

                messageWrapper.classList.remove('d-none');
            }

            function ensureDownload(url) {
                if (!downloadButton) {
                    return;
                }

                downloadButton.href = url;
                downloadButton.classList.remove('d-none');
            }

            function renderFlipbook(url) {
                if (!container || !window.pdfjsLib) {
                    showMessage('Pratinjau flipbook tidak tersedia.');
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
                                    const viewport = pdfPage.getViewport({ scale: 1.1 });
                                    const canvas = document.createElement('canvas');
                                    const context = canvas.getContext('2d');
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

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

                                if (window.jQuery && typeof window.jQuery.fn.turn === 'function') {
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
                                        elevation: 50,
                                    });

                                    $container.addClass('shadow');
                                } else {
                                    container.classList.add('d-flex', 'flex-column', 'gap-3', 'p-3');
                                    const notice = document.createElement('div');
                                    notice.className = 'alert alert-info text-center';
                                    notice.textContent = 'Library Turn.js tidak tersedia. Menampilkan halaman PDF secara berurutan.';
                                    container.prepend(notice);
                                }
                            })
                            .catch(function (error) {
                                console.error('Gagal merender halaman PDF:', error);
                                showMessage('Gagal merender halaman PDF.');
                                throw error;
                            });
                    })
                    .catch(function (error) {
                        console.error('Gagal memuat file PDF:', error);
                        showMessage('Gagal memuat file PDF.');
                        throw error;
                    });
            }

            window.ReportFlipbook = window.ReportFlipbook || {};
            window.ReportFlipbook.open = function (url) {
                const resolvedUrl = resolveUrl(url);
                currentUrl = resolvedUrl;

                resetView();

                if (!resolvedUrl) {
                    showMessage('File PDF tidak ditemukan.');
                    modalInstance.show();
                    return;
                }

                ensureDownload(resolvedUrl);
                showLoading();
                modalInstance.show();

                ensureLibraries()
                    .then(function () {
                        return renderFlipbook(resolvedUrl);
                    })
                    .catch(function () {
                        showMessage('Mode flipbook tidak dapat dimuat. Silakan gunakan tombol unduh.');
                    });
            };

            modalEl.addEventListener('hidden.bs.modal', function () {
                if (window.jQuery && container && window.jQuery(container).data('turn')) {
                    window.jQuery(container).turn('destroy').removeClass('shadow');
                }

                currentUrl = null;
                resetView();
            });
        });
    </script>
@endonce
