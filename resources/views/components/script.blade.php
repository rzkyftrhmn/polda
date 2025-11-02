<!--**********************************
    Main wrapper end
***********************************-->

<!--**********************************
    Scripts
***********************************-->
<!-- Required vendors -->
<script src="{{ asset('dashboard/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

<!-- Apex Chart -->
<script src="{{ asset('dashboard/vendor/apexchart/apexchart.js') }}"></script>
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>

<!-- counter -->
<script src="{{ asset('dashboard/vendor/counter/counter.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/counter/waypoint.min.js') }}"></script>

<!-- Chart piety plugin files -->
<script src="{{ asset('dashboard/vendor/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/swiper/js/swiper-bundle.min.js') }}"></script>

<!-- Dashboard 1 -->
<script src="{{ asset('dashboard/js/dashboard/dashboard-1.js') }}"></script>
<script src="{{ asset('dashboard/js/custom.min.js') }}"></script>
<script src="{{ asset('dashboard/js/dlabnav-init.js') }}"></script>

<script>
    jQuery(document).ready(function(){
        setTimeout(function(){
            dlabSettingsOptions.version = 'dark';
            new dlabSettings(dlabSettingsOptions);
            setCookie('version','dark');
        },1500)
    });
</script>