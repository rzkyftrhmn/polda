<!--**********************************
    Main wrapper end
***********************************-->

<!--**********************************
    Scripts
***********************************-->
<!-- Required vendors -->
<script src="{{ asset('dashboard/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>


<!-- counter -->
<script src="{{ asset('dashboard/vendor/counter/counter.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/counter/waypoint.min.js') }}"></script>

<!-- Chart piety plugin files -->
<script src="{{ asset('dashboard/vendor/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/swiper/js/swiper-bundle.min.js') }}"></script>

<!-- Datatable -->
<script src="{{ asset('dashboard/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/datatables/responsive/responsive.js') }}"></script>
<script src="{{ asset('dashboard/js/plugins-init/datatables.init.js') }}"></script>
<script src="{{ asset('dashboard/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

<!-- Dashboard 1 -->
<script src="{{ asset('dashboard/js/custom.min.js') }}"></script>
<script src="{{ asset('dashboard/js/dlabnav-init.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('dashboard/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
{{-- Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    jQuery(function($){
        function initSelect2($container){
            var $selects = ($container || $(document)).find('select.select2');
            if ($selects.length) {
                $selects.each(function(){
                    var $el = $(this);
                    if ($el.hasClass('default-select') && typeof $el.selectpicker === 'function') {
                        try { $el.selectpicker('destroy'); } catch (e) {}
                        $el.removeClass('default-select');
                    }
                    var placeholder = $el.find('option[value=""]').first().text() || 'Pilih';
                    var options = {
                        placeholder: placeholder,
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'select2-dark',
                        selectionCssClass: 'select2-dark'
                    };
                    var $modal = $el.closest('.modal');
                    if ($modal.length) {
                        options.dropdownParent = $modal;
                    }
                    $el.select2(options);
                });
            }
        }
        initSelect2($(document));
        $(document).on('shown.bs.modal', function(e){
            var $m = $(e.target);
            initSelect2($m);
        });
    });
</script>

<script>
    jQuery(function($){
        function readPref(){
            var ls = null;
            try { ls = localStorage.getItem('theme-version'); } catch(e) {}
            if (ls) return ls;
            var m = document.cookie.match(/(?:^|; )version=([^;]+)/);
            return m ? decodeURIComponent(m[1]) : null;
        }
        function persist(version){
            try { localStorage.setItem('theme-version', version); } catch(e) {}
            if (typeof setCookie === 'function') {
                setCookie('version', version);
            } else {
                document.cookie = 'version=' + version + '; path=/; max-age=' + (60*60*24*365);
            }
        }
        function apply(version){
            if (typeof dlabSettingsOptions !== 'undefined') {
                dlabSettingsOptions.version = version;
                new dlabSettings(dlabSettingsOptions);
            }
            $('body').attr('data-theme-version', version);
            if (version === 'dark') {
                $('#icon-dark').show();
                $('#icon-light').hide();
            } else {
                $('#icon-dark').hide();
                $('#icon-light').show();
            }
        }
        var pref = readPref() || 'light';
        apply(pref);
        $(document).on('click','.dz-theme-mode',function(e){
            e.preventDefault();
            var current = $('body').attr('data-theme-version') || pref;
            var next = current === 'dark' ? 'light' : 'dark';
            apply(next);
            persist(next);
        });
    });
</script>

@yield('scripts')

@include('components.sweetalert')

<script>
jQuery(function($){
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

    // --- GLOBAL DELETE HANDLER ---
    $(document).on('click', '.btn-delete', function(e){
        e.preventDefault();

        const id = $(this).data('id');
        const name = $(this).data('name') || '';
        const url = $(this).data('url'); 
        const title = $(this).data('title') || 'Hapus Data?'; 

        if (!id || !url) return;

        Swal.fire({
            title: title,
            html: 'Anda akan menghapus: <b>' + name + '</b>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'POST',
                data: { _method: 'DELETE', _token: csrfToken },
                success: function(res){
                    Swal.fire({
                        icon: res.status ? 'success' : 'error',
                        title: res.status ? 'Berhasil' : 'Gagal',
                        text: res.message || ''
                    }).then(() => {
                        if ($.fn.DataTable) {
                            try {
                                $('.dataTable').DataTable().ajax.reload(null, false);
                            } catch (e) { location.reload(); }
                        } else {
                            location.reload();
                        }
                    });
                },
                error: function(xhr){
                    const msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Terjadi kesalahan saat menghapus data.';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        });
    });
});
</script>
