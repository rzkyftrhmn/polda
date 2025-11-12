@extends('layouts.dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <!-- Filter Tanggal (Date Range) -->
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body py-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-sm-12 col-md-3">
                                    <label for="filter_start_date" class="form-label mb-0">Mulai</label>
                                    <input type="date" class="form-control" id="filter_start_date" name="start_date">
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <label for="filter_end_date" class="form-label mb-0">Sampai</label>
                                    <input type="date" class="form-control" id="filter_end_date" name="end_date">
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <label class="form-label mb-0 d-block">Rentang Cepat</label>
                                    <div class="btn-group" role="group" aria-label="Quick ranges">
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-range="today">Hari ini</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-range="7">7 hari</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-range="30">30 hari</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-range="this_month">Bulan ini</button>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3 text-md-end">
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <button type="button" class="btn btn-primary" id="btn_apply_date_filter">Terapkan Filter</button>
                                        <button type="button" class="btn btn-light" id="btn_reset_date_filter">Reset</button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">Gunakan filter ini untuk memperbarui KPI, grafik, dan tabel sesuai rentang tanggal yang dipilih.</small>
                        </div>
                    </div>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

        function renderKPIs() {
            // Static dummy numbers (feel free to adjust)
            var totalLaporan = 128;
            var laporanAktif = 47;
            var completionRate = 63; // percent
            var avgResolution = '3.8 hari';
            var percentWithEvidence = 72; // percent
            var topCategory = 'Penipuan Online';

            setText('kpi_total_laporan', numberFormat(totalLaporan));
            setText('kpi_laporan_aktif', numberFormat(laporanAktif));
            setText('kpi_completion_rate', numberFormat(completionRate));
            setText('kpi_avg_resolution_time', avgResolution);
            setText('kpi_with_evidence', numberFormat(percentWithEvidence));
            setText('kpi_top_category', topCategory);
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
            var days = 14;
            var labels = [];
            var data = [];
            for (var i = days - 1; i >= 0; i--) {
                var d = new Date();
                d.setDate(d.getDate() - i);
                var dd = String(d.getDate()).padStart(2, '0');
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                labels.push(dd + '-' + mm);
                data.push(10 + Math.floor(Math.random() * 15)); // 10..24
            }
            render('chart_tren_laporan', {
                chart: { type: 'line', height: 280, toolbar: { show: false } },
                series: [{ name: 'Laporan', data: data }],
                xaxis: { categories: labels },
                stroke: { curve: 'smooth' },
                dataLabels: { enabled: false },
                colors: ['#3b82f6']
            });

            // 2) Distribusi Status Laporan
            var baru = {{ $baru }};
            var diproses = {{ $diproses }};
            var selesai = {{ $selesai }};
            var options = {
                chart: { type: 'donut', height: 280 },
                series: [baru, diproses, selesai],
                labels: ['Baru', 'Diproses', 'Selesai'],
                colors: ['#60a5fa', '#f59e0b', '#10b981'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true }
            };
            render('chart_status_laporan', options);

            // 3) Top 5 Kategori Laporan
            var kategoriLabels = ['Penipuan Online', 'Pencurian', 'Kekerasan', 'Korupsi', 'Narkoba'];
            var kategoriCounts = [58, 36, 22, 18, 14];
            render('chart_top_kategori', {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: 'Jumlah', data: kategoriCounts }],
                xaxis: { categories: kategoriLabels },
                plotOptions: { bar: { columnWidth: '45%', distributed: true } },
                dataLabels: { enabled: false },
                colors: ['#ef4444', '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6']
            });

            // 4) Top Institusi berdasarkan jumlah laporan
            var institusiLabels = ['Polda A', 'Polres B', 'Polsek C', 'Polda D', 'Polres E'];
            var institusiCounts = [40, 33, 29, 25, 21];
            render('chart_top_institusi', {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: 'Jumlah', data: institusiCounts }],
                xaxis: { categories: institusiLabels },
                plotOptions: { bar: { horizontal: true } },
                dataLabels: { enabled: false },
                colors: ['#0ea5e9']
            });
        }

        function renderTables() {
            var backlogEl = document.querySelector('#table_backlog_tahap tbody');
            var tanpaBuktiEl = document.querySelector('#table_tanpa_bukti tbody');
            if (backlogEl) {
                backlogEl.innerHTML = [
                    '<tr><td>1</td><td>#RPT-1001</td><td>Penyelidikan Awal</td><td>12</td><td class="text-end"><button class="btn btn-sm btn-primary">Detail</button></td></tr>',
                    '<tr><td>2</td><td>#RPT-1017</td><td>Verifikasi Bukti</td><td>9</td><td class="text-end"><button class="btn btn-sm btn-primary">Detail</button></td></tr>',
                    '<tr><td>3</td><td>#RPT-1045</td><td>Koordinasi Institusi</td><td>15</td><td class="text-end"><button class="btn btn-sm btn-primary">Detail</button></td></tr>'
                ].join('');
            }
            if (tanpaBuktiEl) {
                tanpaBuktiEl.innerHTML = [
                    '<tr><td>1</td><td>#RPT-1099</td><td>Penipuan Online</td><td>Polda A</td><td class="text-end"><button class="btn btn-sm btn-warning">Lengkapi Bukti</button></td></tr>',
                    '<tr><td>2</td><td>#RPT-1103</td><td>Pencurian</td><td>Polres B</td><td class="text-end"><button class="btn btn-sm btn-warning">Lengkapi Bukti</button></td></tr>',
                    '<tr><td>3</td><td>#RPT-1120</td><td>Kekerasan</td><td>Polsek C</td><td class="text-end"><button class="btn btn-sm btn-warning">Lengkapi Bukti</button></td></tr>'
                ].join('');
            }
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
</script>
<script>
// Dummy data untuk tabel Recent Report berbasis Model Report
(function(){
  const statuses = ['Baru','Diproses','Butuh Perbaikan','Selesai','Ditolak'];
  const institutions = ['Polda A','Polda B','Polres X','Polsek Y','Kejati Z'];
  const categories = ['Penipuan Online','Perjudian','Pencurian Data','Peretasan','Pemerasan'];
  const reporters = ['Budi Santoso','Siti Aminah','Andi Wijaya','Rina Kurnia','Dedi Saputra'];
  function rand(arr){ return arr[Math.floor(Math.random()*arr.length)]; }
  function pad(n){ return String(n).padStart(2,'0'); }
  function makeDate(offset){
    const d = new Date(); d.setDate(d.getDate()-offset);
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  }
  function makeReports(n){
    const list = [];
    for(let i=0;i<n;i++){
      const codeNum = 1200 + i + 1;
      list.push({
        id: `#RPT-${codeNum}`,
        tanggal: makeDate(i*2),
        pelapor: rand(reporters),
        institusi: rand(institutions),
        kategori: rand(categories),
        status: rand(statuses)
      });
    }
    return list;
  }
  function statusBadgeClass(status){
    switch(status){
      case 'Baru': return 'badge-primary';
      case 'Diproses': return 'badge-warning';
      case 'Butuh Perbaikan': return 'badge-info';
      case 'Selesai': return 'badge-success';
      case 'Ditolak': return 'badge-danger';
      default: return 'badge-light';
    }
  }
  function fillTable(tableId, reports){
    const tbody = document.querySelector(`#${tableId} tbody`);
    if(!tbody) return;
    tbody.innerHTML = reports.map((r,i)=>`\n      <tr>\n        <td>${i+1}</td>\n        <td>${r.id}</td>\n        <td>${new Date(r.tanggal).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})}</td>\n        <td>${r.pelapor}</td>\n        <td>${r.institusi}</td>\n        <td>${r.kategori}</td>\n        <td class=\"text-end\"><div class=\"badge badge-sm ${statusBadgeClass(r.status)}\">${r.status.toUpperCase()}</div></td>\n      </tr>\n    `).join('');
  }
  const all = makeReports(12);
  fillTable('table_recent_week', all.slice(0,6));
  fillTable('table_recent_month', all.slice(0,10));
  fillTable('table_recent_year', all);
})();
</script>
@endsection
