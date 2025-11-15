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
    // Dummy data initializer for Dashboard KPIs, Charts, and Tables
    (function() {
        function numberFormat(n) {
            try { return n.toLocaleString('id-ID'); } catch(e) { return String(n); }
        }

        function setText(id, value) {
            var el = document.getElementById(id);
            if (el) el.textContent = value;
        }

        function ensureApexCharts() {
            return new Promise(function(resolve) {
                if (window.ApexCharts) return resolve();
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
                s.async = true;
                s.onload = function() { resolve(); };
                s.onerror = function() { console.warn('ApexCharts CDN gagal dimuat. Grafik mungkin tidak tampil.'); resolve(); };
                document.head.appendChild(s);
            });
        }

        //total laporan perbulan ini
        $(document).ready(function () {
            $.ajax({
                url: "/dashboard/total-laporan",
                method: "GET",
                success: function (res) {
                    $("#kpi_total_laporan").text(res.total);
                },
                error: function (err) {
                    console.error("Gagal mengambil total laporan:", err);
                }
            });
        });

        //jumlah kategori berdasarkan status aktif
        $(document).ready(function () {
            $.ajax({
                url: "/dashboard/top-category-active",
                method: "GET",
                success: function (res) {
                    $("#kpi_top_category").text(res.category);
                },
                error: function (err) {
                    console.error("Gagal mengambil kategori terbanyak:", err);
                }
            });
        });

        //jumlah laporan berdasarkan status aktif
        $(document).ready(function () {
            $.ajax({
                url: "/dashboard/laporan-aktif",
                method: "GET",
                success: function (res) {
                    setText('kpi_laporan_aktif', numberFormat(res.aktif));
                },
                error: function (err) {
                    console.error("Gagal mengambil laporan aktif:", err);
                }
            });
        });


        //persentase laporan selesai
        $.ajax({
            url: "/dashboard/completion-rate",
            method: "GET",
            success: function(res) {
                setText('kpi_completion_rate', res.rate);
            }
        });

        //Rata Rata waktu selesai
        $.get('/dashboard/avg-resolution', function(data){
            var txt = data.avg_resolution_time + ' jam';
            setText('kpi_avg_resolution_time', txt);
        });

        //persentase dengan bukti
        $.ajax({
            url: "/dashboard/kpi-with-evidence",
            method: "GET",
            dataType: "json",
            success: function(res) {
                $('#kpi_with_evidence').text(res.rate); // update span id="kpi_with_evidence"
            },
            error: function(err) {
                console.error("Gagal load KPI With Evidence:", err);
            }
        });




        function renderKPIs() {
            
        }

        function renderCharts() {
            // Helper to safely render ApexCharts if available
            function render(elId, options) {
                var el = document.getElementById(elId);
                if (!el) return;
                if (!window.ApexCharts) return; // skip if library missing
                var chart = new ApexCharts(el, options);
                chart.render();
            }

            // 1) Tren Laporan (last 14 days)
            $(document).ready(function() {
                $.ajax({
                    url: "{{ route('dashboard.trendReports') }}",
                    method: "GET",
                    dataType: "json",
                    success: function(res) {

                        var days = 14;
                        var labels = [];
                        var counts = [];

                        for (var i = days - 1; i >= 0; i--) {
                            var d = new Date();
                            d.setDate(d.getDate() - i);

                            var tanggal = d.toISOString().slice(0, 10); 
                            var tampil = String(d.getDate()).padStart(2, '0') + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + d.getFullYear(); // DD-MM-YYYY untuk label

                            labels.push(tampil);

                            var found = res.find(item => item.date_iso === tanggal); 
                            counts.push(found ? found.total : 0);
                        }

                        var options = {
                            chart: { type: 'line', height: 280, toolbar: { show: false } },
                            series: [{ name: 'Laporan', data: counts }],
                            xaxis: { categories: labels },
                            stroke: { curve: 'smooth' },
                            dataLabels: { enabled: false },
                            colors: ['#3b82f6']
                        };

                        if(window.ApexCharts){
                            var chart = new ApexCharts(document.querySelector("#chart_tren_laporan"), options);
                            chart.render();
                        }
                    },
                    error: function(err) {
                        console.error("Gagal memuat tren laporan:", err);
                    }
                });
            });



            // 2) Distribusi Status Laporan
            $(document).ready(function() {
                $.ajax({
                    url: "{{ route('dashboard.statusSummary') }}", 
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        var baru = data.baru;
                        var diproses = data.diproses;
                        var selesai = data.selesai;

                        var options = {
                            chart: { type: 'donut', height: 280 },
                            series: [baru, diproses, selesai],
                            labels: ['Baru', 'Diproses', 'Selesai'],
                            colors: ['#60a5fa', '#f59e0b', '#10b981'],
                            legend: { position: 'bottom' },
                            dataLabels: { enabled: true }
                        };

                        if(window.ApexCharts) {
                            var chart = new ApexCharts(document.querySelector("#chart_status_laporan"), options);
                            chart.render();
                        }
                    },
                    error: function(err) {
                        console.error("Gagal mengambil data status:", err);
                    }
                });
            });


            // 3) Top 5 Kategori Laporan
            $(document).ready(function() {
                $.ajax({
                    url: "{{ route('dashboard.topCategories') }}",
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        // Ambil label dan data dari response JSON
                        var kategoriLabels = data.map(item => item.category);
                        var kategoriCounts = data.map(item => item.total);

                        var options = {
                            chart: { type: 'bar', height: 280, toolbar: { show: false } },
                            series: [{ name: 'Jumlah', data: kategoriCounts }],
                            xaxis: { categories: kategoriLabels },
                            plotOptions: { bar: { columnWidth: '45%', distributed: true } },
                            dataLabels: { enabled: false },
                            colors: ['#ef4444', '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6']
                        };

                        if(window.ApexCharts) {
                            var chart = new ApexCharts(document.querySelector("#chart_top_kategori"), options);
                            chart.render();
                        }
                    },
                    error: function(err) {
                        console.error("Gagal mengambil data top category:", err);
                    }
                });
            });

            // 4) Top Institusi berdasarkan jumlah laporan
            $(document).ready(function () {
            $.ajax({
                    url: "{{ route('dashboard.topInstitusi') }}",
                    method: "GET",
                    data: getFilterParams(), // supaya ikut filter tanggal
                    success: function (res) {

                        let labels = res.map(item => item.institution);
                        let counts = res.map(item => item.total);

                        var options = {
                            chart: { type: 'bar', height: 280, toolbar: { show: false } },
                            series: [{ name: 'Jumlah', data: counts }],
                            xaxis: { categories: labels },
                            plotOptions: { bar: { horizontal: true } },
                            dataLabels: { enabled: false },
                            colors: ['#0ea5e9']
                        };

                        if (window.ApexCharts) {
                            var chart = new ApexCharts(
                                document.querySelector("#chart_top_institusi"),
                                options
                            );
                            chart.render();
                        }
                    },
                    error: function (err) {
                        console.error("Gagal mengambil data top institusi:", err);
                    }
                });
            });
        }

        function renderTables() {
            var backlogEl = document.querySelector('#table_backlog_tahap tbody');
            if (backlogEl) {
                backlogEl.innerHTML = [
                    '<tr><td>1</td><td>#RPT-1001</td><td>Penyelidikan Awal</td><td>12</td><td class="text-end"><button class="btn btn-sm btn-primary">Detail</button></td></tr>',
                    '<tr><td>2</td><td>#RPT-1017</td><td>Verifikasi Bukti</td><td>9</td><td class="text-end"><button class="btn btn-sm btn-primary">Detail</button></td></tr>',
                    '<tr><td>3</td><td>#RPT-1045</td><td>Koordinasi Institusi</td><td>15</td><td class="text-end"><button class="btn btn-sm btn-primary">Detail</button></td></tr>'
                ].join('');
            }
            $(document).ready(function() {
                $('#table_tanpa_bukti').DataTable({
                    processing: true,
                    serverSide: false, 
                    ajax: "{{ route('dashboard.reportsWithoutEvidence') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                        { data: 'code', name: 'code' },
                        { data: 'kategori', name: 'kategori' },
                        { data: 'institusi', name: 'institusi' },
                    ],
                    paging: false,      
                    searching: false,   
                    info: false         
                });
            });
        }

        function getFilterParams() {
            return {
                start_date: $('#filter_start_date').val(),
                end_date: $('#filter_end_date').val(),
            };
        }

        //filter 
        $('#btn_apply_date_filter').on('click', function () {
            console.log("Terapkan filter tanggal");
            loadDashboardWithFilter();
        });

        $('#btn_reset_date_filter').on('click', function () {
            console.log("Reset filter tanggal");
            $('#filter_start_date').val('');
            $('#filter_end_date').val('');
            loadDashboardWithFilter();
        });

        //debugg date error
        $('#filter_start_date').on('change', function () {
            console.log("Start date changed:", $(this).val());
        });
        $('#filter_end_date').on('change', function () {
            console.log("End date changed:", $(this).val());
        });

        //get ajax from controller
        function loadDashboardWithFilter() {

            const filter = getFilterParams();
            console.log("Load Dashboard with filter:", filter);

            // ---------------- KPI ----------------
            $.get('/dashboard/total-laporan', filter, function(res){
                $("#kpi_total_laporan").text(res.total);
            });

            $.get('/dashboard/laporan-aktif', filter, function(res){
                $("#kpi_laporan_aktif").text(res.aktif);
            });

            $.get('/dashboard/completion-rate', filter, function(res){
                $("#kpi_completion_rate").text(res.rate);
            });

            $.get('/dashboard/avg-resolution', filter, function(res){
                $("#kpi_avg_resolution_time").text(res.avg_resolution_time + " hari");
            });

            $.get('/dashboard/kpi-with-evidence', filter, function(res){
                $("#kpi_with_evidence").text(res.rate);
            });

            $.get('/dashboard/top-category-active', filter, function(res){
                $("#kpi_top_category").text(res.category);
            });

            // ---------------- TREND CHART ----------------
            $.get("{{ route('dashboard.trendReports') }}", filter, function(res){
                console.log("Reload Chart Trend:", res);
                // nanti bagian reload chart gua bikinin setelah filter kelar
            });
        }

        function initDummy() {
            renderKPIs();
            renderTables();
            ensureApexCharts().then(renderCharts);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDummy);
        } else {
            initDummy();
        }
    })();
    $(document).ready(function () {
        function statusBadgeClass(status){
            switch(status){
                case 'SUBMITTED': return 'badge-primary';
                case 'PEMERIKSAAN': return 'badge-warning';
                case 'LIMPAH': return 'badge-info';
                case 'SIDANG': return 'badge-info';
                case 'SELESAI': return 'badge-success';
                case 'DITOLAK': return 'badge-danger';
                default: return 'badge-light';
            }
        }

        // Isi tabel
        function fillTable(tableId, data){
            let tbody = $(`#${tableId} tbody`);
            tbody.empty();

            data.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.code}</td>
                        <td>${new Date(item.tanggal).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric'
                        })}</td>
                        <td>${item.pelapor}</td>
                        <td>${item.institusi}</td>
                        <td>${item.kategori}</td>
                        <td class="text-end">
                            <span class="badge badge-sm ${statusBadgeClass(item.status)}">
                                ${item.status.toUpperCase()}
                            </span>
                        </td>
                    </tr>
                `);
            });
        }

        $.ajax({
            url: "/dashboard/recent-reports",
            type: "GET",
            dataType: "json",

            success: function(response){
                fillTable("table_recent_week", response.slice(0, 6));
                fillTable("table_recent_month", response.slice(0, 10));
                fillTable("table_recent_year", response);
            },

            error: function(xhr){
                console.error("Gagal load recent report:", xhr.responseText);
            }
        });

    });
</script>
@endsection