@extends('layouts.dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            {{-- FILTER DASHBOARD --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filter Dashboard</h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3 mb-2">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" id="filter_start_date" class="form-control">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" id="filter_end_date" class="form-control">
                        </div>

                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button id="btn_apply_date_filter" class="btn btn-primary w-100">Terapkan Filter</button>
                        </div>

                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button id="btn_reset_date_filter" class="btn btn-secondary w-100">Reset Filter</button>
                        </div>

                    </div>
                </div>
            </div>
            {{-- END FILTER --}}
            </div>
            <!-- Dashboard: Blok baru sesuai speckit -->
            <div class="row">
                <!-- Ringkasan KPI Utama -->
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-box bg-secondary">
                                <div class="card-header border-0 pb-0">
                                    <h6 class="text-white mb-1">Total Laporan (Bulan Ini)</h6>
                                    <h3 class="text-white mb-0"><span id="kpi_total_laporan">-</span></h3>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-white-50">Total keseluruhan laporan yang masuk</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-box bg-dark">
                                <div class="card-header border-0 pb-0">
                                    <h6 class="text-white mb-1">Laporan Aktif</h6>
                                    <h3 class="text-white mb-0"><span id="kpi_laporan_aktif">-</span></h3>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-white-50">Status: belum selesai/terbuka</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-box bg-warning">
                                <div class="card-header border-0 pb-0">
                                    <h6 class="text-white mb-1">Tingkat Penyelesaian</h6>
                                    <h3 class="text-white mb-0"><span id="kpi_completion_rate">-</span>%</h3>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-white-50">Periode berjalan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-box bg-pink">
                                <div class="card-header border-0 pb-0">
                                    <h6 class="text-white mb-1">Rata-rata Waktu Selesai</h6>
                                    <h3 class="text-white mb-0"><span id="kpi_avg_resolution_time">-</span></h3>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-white-50">Mengacu ke ReportJourney</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-box bg-secondary">
                                <div class="card-header border-0 pb-0">
                                    <h6 class="text-white mb-1">Persentase dengan Bukti</h6>
                                    <h3 class="text-white mb-0"><span id="kpi_with_evidence">-</span>%</h3>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-white-50">>= 1 ReportEvidence</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-box bg-dark">
                                <div class="card-header border-0 pb-0">
                                    <h6 class="text-white mb-1">Kategori Terbanyak</h6>
                                    <h5 class="text-white mb-0"><span id="kpi_top_category">-</span></h5>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-white-50">Periode berjalan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header pb-0 border-0">
                            <div class="mb-0">
                                <h4 class="card-title">Tren Laporan</h4>
                                <p>Jumlah laporan per hari/minggu/bulan</p>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            <div id="chart_tren_laporan" class="w-100" style="min-height:280px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <div class="mb-0">
                                <h4 class="card-title">Distribusi Status Laporan</h4>
                                <p>Baru, Diproses, Selesai</p>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            <div id="chart_status_laporan" class="w-100" style="min-height:280px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header pb-0 border-0">
                            <h4 class="card-title">Top 5 Kategori Laporan</h4>
                        </div>
                        <div class="card-body pt-2">
                            <div id="chart_top_kategori" class="w-100" style="min-height:280px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header pb-0 border-0">
                            <h4 class="card-title">Top Institusi berdasarkan jumlah laporan</h4>
                        </div>
                        <div class="card-body pt-2">
                            <div id="chart_top_institusi" class="w-100" style="min-height:280px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title">Backlog per Tahap (ReportJourney)</h4>
                            <p class="mb-0">Jumlah laporan belum pindah tahap dalam > X hari</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-responsive-md" id="table_backlog_tahap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Laporan</th>
                                            <th>Tahap Terakhir</th>
                                            <th>Durasi (hari)</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables server-side --> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title">Laporan Tanpa Bukti</h4>
                            <p class="mb-0">Daftar laporan yang belum memiliki evidence</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-responsive-md" id="table_tanpa_bukti">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Laporan</th>
                                            <th>Kategori</th>
                                            <th>Institusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables server-side -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card transaction-table">
                        <div class="card-header border-0 flex-wrap pb-0">
                            <div class="mb-2">
                                <h4 class="card-title">Recent Report</h4>
                                <p class="mb-sm-3 mb-0">Lorem ipsum dolor sit amet, consectetur</p>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="tab-content" id="myTabContent1">
                                <div class="tab-pane fade show active" id="Week" role="tabpanel" aria-labelledby="Week-tab">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md" id="table_recent_week">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Report ID</th>
                                                    <th>Tanggal</th>
                                                    <th>Pelapor</th>
                                                    <th>Institusi</th>
                                                    <th>Kategori</th>
                                                    <th class="text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Diisi via script dummy berdasarkan Model Report -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="month" role="tabpanel" aria-labelledby="month-tab">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md" id="table_recent_month">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Report ID</th>
                                                    <th>Tanggal</th>
                                                    <th>Pelapor</th>
                                                    <th>Institusi</th>
                                                    <th>Kategori</th>
                                                    <th class="text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Diisi via script dummy berdasarkan Model Report -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="year" role="tabpanel" aria-labelledby="year-tab">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md" id="table_recent_year">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Report ID</th>
                                                    <th>Tanggal</th>
                                                    <th>Pelapor</th>
                                                    <th>Institusi</th>
                                                    <th>Kategori</th>
                                                    <th class="text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Diisi via script dummy berdasarkan Model Report -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Apex Chart -->
<script src="{{ asset('dashboard/vendor/apexchart/apexchart.js') }}"></script>
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
<!-- Dashboard 1 -->
{{-- <script src="{{ asset('dashboard/js/dashboard/dashboard-1.js') }}"></script> --}}
<script>

    // ======================================================
    // GLOBAL FUNCTIONS
    // ======================================================
    function getFilterParams() {
        return {
            start_date: $('#filter_start_date').val(),
            end_date: $('#filter_end_date').val(),
        };
    }

    // ======================================================
    // LOAD KPI
    // ======================================================
    function loadDashboardKPIs() {
        const f = getFilterParams();

        $.get('/dashboard/total-laporan', f, res => {
            $("#kpi_total_laporan").text(res.total);
        });

        $.get('/dashboard/laporan-aktif', f, res => {
            $("#kpi_laporan_aktif").text(res.aktif);
        });

        $.get('/dashboard/completion-rate', f, res => {
            $("#kpi_completion_rate").text(res.rate);
        });

        $.get('/dashboard/avg-resolution', f, res => {
            $("#kpi_avg_resolution_time").text(res.avg_resolution_time + " hari");
        });

        $.get('/dashboard/kpi-with-evidence', f, res => {
            $("#kpi_with_evidence").text(res.rate);
        });

        $.get('/dashboard/top-category-active', f, res => {
            $("#kpi_top_category").text(res.category);
        });
    }



    // ======================================================
    // LOAD CHART: TOP INSTITUSI
    // ======================================================
    function loadTopInstitusiChart() {

        $.ajax({
            url: "{{ route('dashboard.topInstitusi') }}",
            method: "GET",
            data: getFilterParams(),
            success: function (res) {

                let labels = res.map(i => i.institution);
                let counts = res.map(i => i.total);

                $("#chart_top_institusi").html(""); // clear sebelum render ulang

                new ApexCharts(
                    document.querySelector("#chart_top_institusi"),
                    {
                        chart: { type: 'bar', height: 280, toolbar: { show: false } },
                        series: [{ name: 'Jumlah', data: counts }],
                        xaxis: { categories: labels },
                        plotOptions: { bar: { horizontal: true } },
                        dataLabels: { enabled: false },
                        colors: ['#0ea5e9']
                    }
                ).render();
            }
        });
    }

        //Rata Rata waktu selesai
        $.get('/dashboard/avg-resolution', function(data){
            var txt = data.avg_resolution_time + ' jam';
            setText('kpi_avg_resolution_time', txt);
        });


    // ======================================================
    // LOAD CHART: TREN LAPORAN
    // ======================================================
    function loadTrendLaporan() {

        $.ajax({
            url: "{{ route('dashboard.trendReports') }}",
            method: "GET",
            data: getFilterParams(),
            success: function(res){

                var labels = [];
                var counts = [];

                for (var i = 13; i >= 0; i--) {
                    var d = new Date();
                    d.setDate(d.getDate() - i);

                    var tgl = d.toISOString().slice(0,10);
                    var tampil = d.getDate().toString().padStart(2,'0')+'-'+(d.getMonth()+1);

                    labels.push(tampil);

                    var found = res.find(r => r.date === tgl);
                    counts.push(found ? found.total : 0);
                }

                $("#chart_tren_laporan").html(""); // clear

                new ApexCharts(
                    document.querySelector("#chart_tren_laporan"),
                    {
                        chart:{ type:'line', height:280, toolbar:{ show:false }},
                        series:[{ name:'Laporan', data:counts }],
                        xaxis:{ categories:labels },
                        stroke:{ curve:'smooth' },
                        dataLabels:{ enabled:false },
                        colors:['#3b82f6']
                    }
                ).render();
            }
        });
    }



    // ======================================================
    // LOAD CHART: STATUS LAPORAN
    // ======================================================
    function loadStatusSummaryChart() {

        $.ajax({
            url: "{{ route('dashboard.statusSummary') }}",
            method: "GET",
            data: getFilterParams(),
            success: function(res){

                $("#chart_status_laporan").html("");

                new ApexCharts(
                    document.querySelector("#chart_status_laporan"),
                    {
                        chart:{ type:'donut', height:280 },
                        series: [res.baru, res.diproses, res.selesai],
                        labels:['Baru','Diproses','Selesai'],
                        colors:['#60a5fa','#f59e0b','#10b981'],
                        legend:{ position:'bottom' }
                    }
                ).render();
            }
        });
    }



    // ======================================================
    // LOAD CHART: TOP KATEGORI
    // ======================================================
    function loadTopKategoriChart() {

        $.ajax({
            url: "{{ route('dashboard.topCategories') }}",
            method: "GET",
            data: getFilterParams(),
            success: function(res){

                let labels = res.map(i => i.category);
                let counts = res.map(i => i.total);

                $("#chart_top_kategori").html("");

                new ApexCharts(
                    document.querySelector("#chart_top_kategori"),
                    {
                        chart:{ type:'bar', height:280, toolbar:{ show:false } },
                        series:[{ name:'Jumlah', data:counts }],
                        xaxis:{ categories:labels },
                        plotOptions:{ bar:{ columnWidth:'45%', distributed:true }},
                        dataLabels:{ enabled:false }
                    }
                ).render();
            }
        });
    }



    // ======================================================
    // LOAD TABLE: BACKLOG
    // ======================================================
    function loadBacklogTable() {

        $.ajax({
            url: "/dashboard/backlog-tahap",
            method: "GET",
            data: getFilterParams(),
            success: function (res) {

                let tbody = $("#table_backlog_tahap tbody");
                tbody.empty();

                res.forEach((item, index) => {
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.report_id}</td>
                            <td>${item.tahap}</td>
                            <td>${item.durasi} hari</td>
                            <td class="text-end">
                                <a href="/pelaporan/${item.report_id_raw}" class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }



    // ======================================================
    // LOAD SEMUA DASHBOARD
    // ======================================================
    function loadDashboardAll() {
        loadDashboardKPIs();
        loadTrendLaporan();
        loadStatusSummaryChart();
        loadTopKategoriChart();
        loadTopInstitusiChart();
        loadBacklogTable();
    }



    // ======================================================
    // INIT PAGE
    // ======================================================
    $(document).ready(function(){

        // Load pertama kali
        loadDashboardAll();

        // Apply / Reset Filter
        $('#btn_apply_date_filter').on('click', loadDashboardAll);
        $('#btn_reset_date_filter').on('click', function(){
            $('#filter_start_date').val('');
            $('#filter_end_date').val('');
            loadDashboardAll();
        });

    });

</script>

@endsection