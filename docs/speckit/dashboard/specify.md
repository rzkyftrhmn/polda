# Specify: Squence Fitur UI Dashboard

## Purpose
Membuat html untuk page dashboard

# Saran Data Dashboard Berdasarkan Model di Project

Dokumen ini berisi saran konten dashboard yang dapat disajikan dengan mengacu pada model yang tersedia di `app/Models`:
- Division
- Institution
- Report
- ReportCategory
- ReportEvidence
- ReportJourney
- Role
- Suspect
- User

Tujuan: Memudahkan pemangku kepentingan (Admin, Polda, Polres, Kasubbid) untuk memonitor pelaporan, progres penanganan, dan sumber daya.

## Ringkasan KPI Utama (Header Dashboard)
- Total laporan (hari ini, minggu ini, bulan ini)
- Laporan aktif (status: belum selesai/terbuka)
- Tingkat penyelesaian (%) periode berjalan
- Rata-rata waktu penyelesaian laporan (mengacu ke `ReportJourney`)
- Persentase laporan dengan bukti (>=1 `ReportEvidence`)
- Kategori laporan terbanyak periode berjalan

## Bagian 1: Tren & Distribusi Laporan (Model: Report, ReportCategory)
- Grafik garis (ApexCharts): jumlah laporan per hari/minggu/bulan
- Bar chart: Top 5 kategori dengan jumlah laporan tertinggi (`Report.category_id` -> `ReportCategory.name`)
- Donut/Pie chart: Distribusi status laporan (contoh: Baru, Diproses, Selesai)
- Heatmap kalender: intensitas laporan per hari (opsional)
- Choropleth/peta (opsional jika pakai tabel provinsi/kota/kecamatan/kelurahan): distribusi laporan per wilayah

Metode hitung contoh:
- Total per periode: `Report::whereBetween('incident_datetime', [...])`
- Per status: `Report::select('status', DB::raw('count(*) as total'))->groupBy('status')`
- Per kategori: join ke `ReportCategory` untuk nama kategori

## Bagian 2: Kinerja Penanganan (Model: ReportJourney)
- Rata-rata waktu dari status “Baru” ke “Selesai” (SLA)
- Distribusi laporan berdasarkan tahap terakhir di journey (contoh: Verifikasi, Investigasi, Ditindaklanjuti, Selesai)
- Tren waktu penanganan per kategori (bar chart), untuk melihat kategori yang memerlukan perhatian khusus
- Backlog: jumlah laporan di tiap tahap yang belum pindah tahap dalam > X hari

Catatan: Struktur `ReportJourney` biasanya mencatat status, waktu, dan mungkin petugas. Gunakan data ini untuk menghitung durasi antar tahap.

## Bagian 3: Bukti & Kualitas Laporan (Model: ReportEvidence)
- Persentase laporan dengan bukti
- Rata-rata jumlah bukti per laporan
- Daftar laporan tanpa bukti (table, untuk ditindaklanjuti)
- Jika tersedia tipe bukti (foto, video, dokumen), tampilkan pie chart proporsi tipe bukti

Metode hitung contoh:
- Laporan dengan bukti: `ReportEvidence::select('report_id')->distinct()->count()` dibanding total `Report`
- Rata-rata jumlah bukti: `ReportEvidence::count() / Report::count()`

## Bagian 4: Institusi & Subbagian (Model: Institution, Division)
- Bar chart: Top institusi berdasarkan jumlah laporan
- Tabel: Institusi dengan SLA terburuk (rata-rata durasi penyelesaian tertinggi)
- Bar chart: Aktivitas per subbagian (`Division`) – jumlah laporan yang ditangani
- Pemetaan hierarki (opsional): Polda → Polres → Satuan/Subbag untuk navigasi drill-down

Metode hitung contoh:
- Join `reports` ke `institutions` dan `divisions` untuk agregasi per unit

## Bagian 5: Tersangka (Model: Suspect)
- Total tersangka terkait laporan periode berjalan
- Tersangka per kategori laporan (stacked bar)
- Daftar laporan dengan lebih dari N tersangka (indikasi kasus kompleks)

Catatan: Sesuaikan dengan field yang tersedia pada model `Suspect` (status/tingkat ancaman jika ada).

## Bagian 6: Pengguna & Role (Model: User, Role)
- Distribusi user per role (Admin, Polda, Polres, Kasubbid)
- Pertumbuhan user per bulan (line chart)
- Aktivitas terakhir user (opsional, jika ada log) – tabel top aktif

Catatan keamanan: Jangan tampilkan informasi sensitif (email lengkap) pada widget publik; gunakan ringkasan.

## Widget Tabel (DataTables) yang Direkomendasikan
- Laporan terbaru (kolom: waktu kejadian, kategori, institusi, status, aksi)
- Laporan tanpa bukti (aksi: ajukan permintaan bukti)
- Backlog per tahap (aksi: dorong progres/assign petugas)
- Top institusi dengan laporan terbanyak (aksi: buka detail)
- Tersangka terbanyak per laporan (aksi: buka detail)

## Filter yang Direkomendasikan
- Waktu: rentang tanggal (today, last 7 days, this month, custom)
- Wilayah: provinsi → kota → kecamatan → kelurahan
- Institusi & Subbagian
- Kategori laporan
- Status laporan
- Role (untuk hak akses tampilan)

## Segmentasi Berdasarkan Role (Hak Akses)
- Admin: semua data, kontrol manajemen (hapus/edit), KPI global
- Polda: fokus pada unit di bawah Polda, agregasi wilayah, SLA per Polres
- Polres: detail operasional harian, backlog, bukti, tersangka
- Kasubbid: laporan yang diassign, kinerja tim, SLA tim

## Rekomendasi Visual (Konsisten dengan Project)
- Gunakan ApexCharts (sudah digunakan di project) untuk line/bar/donut
- Gunakan DataTables untuk tabel interaktif server-side
- Gunakan SweetAlert2 untuk konfirmasi aksi (hapus/update status)
- Gunakan tema gelap sesuai styling yang sudah diatur (komponen style.blade.php)

## Contoh Struktur Halaman Dashboard
1. Header KPI
2. Row 1
   - Line chart tren laporan
   - Donut status laporan
   - Bar top kategori
3. Row 2
   - Bar top institusi
   - Table backlog per tahap (`ReportJourney`)
   - Table laporan tanpa bukti
4. Row 3 (opsional)
   - Peta distribusi laporan (jika data wilayah digunakan)
   - Stacked bar tersangka per kategori

## Catatan Implementasi Teknis (Singkat)
- Hanya pembuatan html codenya, tidak ada query server-side
- path: resources\views\pages\index.blade.php
---

Dengan saran di atas, dashboard dapat menyajikan informasi operasional yang relevan, mudah dipahami, dan dapat ditindaklanjuti oleh peran yang berbeda dalam organisasi.