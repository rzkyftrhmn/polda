@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary mb-3">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-sharp fa-solid fa-filter me-2"></i>Filter</div>
                    <div class="tools">
                        <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <form id="filter-form">
                            <div class="row g-3">
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" class="form-control" id="filter_q" name="q" placeholder="Kode / Judul / Deskripsi">
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-control select2" id="filter_status" name="status">
                                        <option value="">Semua Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Kategori Pelanggaran</label>
                                    <select class="form-control select2" id="filter_category_id" name="category_id">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Satwil/Satker (Unit)</label>
                                    <select class="form-control select2" id="filter_division_id" name="division_id">
                                        <option value="">Semua Unit</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Rentang Kejadian</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" class="form-control" id="filter_incident_from" name="incident_from">
                                        <input type="date" class="form-control" id="filter_incident_to" name="incident_to">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Rentang Dibuat</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" class="form-control" id="filter_created_from" name="created_from">
                                        <input type="date" class="form-control" id="filter_created_to" name="created_to">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Rentang Selesai</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" class="form-control" id="filter_finish_from" name="finish_from">
                                        <input type="date" class="form-control" id="filter_finish_to" name="finish_to">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Urut Berdasarkan</label>
                                    <select class="form-control" id="filter_sort_by" name="sort_by">
                                        <option value="">Default (Tanggal Dibuat)</option>
                                        <option value="created_at">Tanggal Dibuat</option>
                                        <option value="incident_datetime">Tanggal Kejadian</option>
                                        <option value="finish_time">Tanggal Selesai</option>
                                        <option value="status">Status</option>
                                        <option value="code">Kode</option>
                                        <option value="title">Judul</option>
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <label class="form-label">Arah Pengurutan</label>
                                    <select class="form-control" id="filter_sort_dir" name="sort_dir">
                                        <option value="desc">Turun</option>
                                        <option value="asc">Naik</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex align-items-end justify-content-start gap-2 mt-2">
                                    <button type="button" id="btnFilter" class="btn btn-primary">
                                        <i class="fa fa-filter me-1"></i> Terapkan
                                    </button>
                                    <button type="button" id="btnReset" class="btn btn-danger light">
                                        <i class="fa fa-rotate-left me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                <h4 class="mb-0">Data Laporan</h4>
                <div class="d-flex gap-2">
                    <button type="button" id="btnExportExcel" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel me-1"></i> Export Excel
                    </button>
                    <button type="button" id="btnExportPdf" class="btn btn-danger btn-sm">
                        <i class="fa fa-file-pdf me-1"></i> Export PDF
                    </button>
                </div>
            </div>

            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-database me-1"></i>Daftar Laporan</div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="report-data-table" class="display min-w1100">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal Kejadian</th>
                                    <th>Provinsi</th>
                                    <th>Kota/Kabupaten</th>
                                    <th>Kecamatan</th>
                                    <th>Dibuat</th>
                                    <th>Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal Kejadian</th>
                                    <th>Provinsi</th>
                                    <th>Kota/Kabupaten</th>
                                    <th>Kecamatan</th>
                                    <th>Dibuat</th>
                                    <th>Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    jQuery(function ($) {
        const datatableUrl = '{{ route('datatables.report-data') }}';
        const exportExcelUrl = '{{ route('report-data.export.excel') }}';
        const exportPdfUrl = '{{ route('report-data.export.pdf') }}';
        const table = $('#report-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: datatableUrl,
                type: 'GET',
                data: function (d) {
                    const filters = getFilters();
                    Object.assign(d, filters);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'title', name: 'title'},
                {data: 'category', name: 'category', orderable: false, searchable: false},
                {data: 'status', name: 'status'},
                {data: 'incident_at', name: 'incident_datetime'},
                {data: 'province', name: 'province', orderable: false, searchable: false},
                {data: 'city', name: 'city', orderable: false, searchable: false},
                {data: 'district', name: 'district', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'finished_at', name: 'finish_time'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[9, 'desc']],
            language: {
                paginate: {
                    previous: '<<',
                    next: '>>'
                }
            }
        });

        $('#btnFilter').on('click', function () {
            table.ajax.reload();
        });

        $('#btnReset').on('click', function () {
            resetFilters();
            table.ajax.reload();
        });

        $('#filter_province_id').on('change', function () {
            const provinceId = $(this).val();
            $('#filter_city_id').html('<option value="">Semua Kota/Kabupaten</option>');
            $('#filter_district_id').html('<option value="">Semua Kecamatan</option>');

            if (!provinceId) {
                return;
            }

            const url = citiesEndpointTemplate.replace('__province__', provinceId);
            $.get(url, function (response) {
                if (Array.isArray(response)) {
                    response.forEach(function (city) {
                        $('#filter_city_id').append('<option value="' + city.id + '">' + city.name + '</option>');
                    });
                }
            });
        });

        $('#filter_city_id').on('change', function () {
            const cityId = $(this).val();
            $('#filter_district_id').html('<option value="">Semua Kecamatan</option>');

            if (!cityId) {
                return;
            }

            const url = districtsEndpointTemplate.replace('__city__', cityId);
            $.get(url, function (response) {
                if (Array.isArray(response)) {
                    response.forEach(function (district) {
                        $('#filter_district_id').append('<option value="' + district.id + '">' + district.name + '</option>');
                    });
                }
            });
        });

        $('#btnExportExcel').on('click', function () {
            const params = new URLSearchParams(getFilters());
            window.location.href = exportExcelUrl + '?' + params.toString();
        });

        $('#btnExportPdf').on('click', function () {
            const params = new URLSearchParams(getFilters());
            window.location.href = exportPdfUrl + '?' + params.toString();
        });

        function getFilters() {
            return {
                q: $('#filter_q').val() || '',
                status: $('#filter_status').val() || '',
                category_id: $('#filter_category_id').val() || '',
                division_id: $('#filter_division_id').val() || '',
                incident_from: $('#filter_incident_from').val() || '',
                incident_to: $('#filter_incident_to').val() || '',
                created_from: $('#filter_created_from').val() || '',
                created_to: $('#filter_created_to').val() || '',
                finish_from: $('#filter_finish_from').val() || '',
                finish_to: $('#filter_finish_to').val() || '',
                sort_by: $('#filter_sort_by').val() || '',
                sort_dir: $('#filter_sort_dir').val() || '',
            };
        }

        function resetFilters() {
            $('#filter-form')[0].reset();
            $('#filter_division_id').val('').trigger('change.select2');
        }

    });
</script>
@endsection
