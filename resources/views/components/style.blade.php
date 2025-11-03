<!-- FAVICONS ICON -->
<link rel="shortcut icon" type="image/png" href="{{ asset('dashboard/images/propam.ico') }}">
<link href="{{ asset('dashboard/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('dashboard/vendor/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">

<!-- Style css -->
<link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet"/>
<!-- Datatable -->
<link href="{{ asset('dashboard/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<!-- Custom Stylesheet -->
<link href="{{ asset('dashboard/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
<link href="{{ asset('dashboard/vendor/datatables/responsive/responsive.css') }}" rel="stylesheet">
<style>
    /* Select2 Dark Theme (match dashboard dark colors) */
    /* Note: selectionCssClass applies to the selection element, not the container */
    [data-theme-version="dark"] .select2-container .select2-selection--single.select2-dark {
        background-color: #1E1E2D;
        border-color: #3A3A4F;
        color: #fff;
    }
    [data-theme-version="dark"] .select2-container .select2-selection--single.select2-dark .select2-selection__rendered {
        color: #fff;
        text-align: center;
        width: 100%;
        padding-left: 0; /* center text without left padding */
        padding-right: 2.25rem; /* space for the arrow on the right */
    }
    [data-theme-version="dark"] .select2-container .select2-selection--single.select2-dark .select2-selection__placeholder {
        color: #9EA3B4;
        text-align: center;
    }
    [data-theme-version="dark"] .select2-container .select2-selection--single.select2-dark .select2-selection__arrow b {
        border-color: #fff transparent transparent transparent;
    }
    [data-theme-version="dark"] .select2-container .select2-selection--single.select2-dark .select2-selection__clear {
        color: #fff;
    }

    [data-theme-version="dark"] .select2-dropdown.select2-dark {
        background-color: #1E1E2D;
        border-color: #3A3A4F;
        color: #fff;
    }
    [data-theme-version="dark"] .select2-dropdown.select2-dark .select2-results__option {
        color: #fff;
        text-align: center; /* center option text */
    }
    [data-theme-version="dark"] .select2-dropdown.select2-dark .select2-results__option--highlighted {
        background-color: #2A2A3C;
        color: #fff;
    }
    [data-theme-version="dark"] .select2-dropdown.select2-dark .select2-search__field {
        background-color: #101022;
        border-color: #3A3A4F;
        color: #fff;
    }
    /* Make sure Select2 respects full width like form-control */
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { height: calc(2.375rem); display: flex; align-items: center; }
    .select2-container .select2-selection--single .select2-selection__rendered { line-height: normal; }
    .select2-container .select2-selection--single .select2-selection__arrow { height: 100%; }
    .select2-search--dropdown .select2-search__field { outline: none; }
</style>
