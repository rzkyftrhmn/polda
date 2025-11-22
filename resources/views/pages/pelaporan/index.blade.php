@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">

            <!-- Filter -->
            <div class="filter cm-content-box box-primary mb-3">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-sharp fa-solid fa-filter me-2"></i>Filter</div>
                    <div class="tools">
                        <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                    </div>
                </div>

                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control mb-xl-0 mb-3"
                                    id="filter_q" placeholder="nama pelapor / terlapor / no dokumen">
                            </div>
                            <div class="col-xl-3 col-sm-6 align-self-end">
                                <div>
                                    <button id="btnFilter" class="btn btn-primary me-2" type="button">
                                        <i class="fa fa-filter me-1"></i>Filter
                                    </button>
                                    <button id="btnClear" class="btn btn-danger light" type="button">
                                        Remove Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tambah Laporan -->
            <div class="mb-3">
                <a href="{{ route('pelaporan.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i>Tambah Laporan
                </a>
            </div>

            <!-- DataTables -->
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-file-lines me-1"></i>Laporan List</div>
                </div>

                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pelaporan-table" class="display min-w850">
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
jQuery(function($) {
    var table = $('#pelaporan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("datatables.pelaporan") }}',
            type: 'GET',
            data: function(d) {
                d.filter_q = $('#filter_q').val();  
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'title', name: 'title' },
            { data: 'category', name: 'category', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'incident_at', name: 'incident_datetime' },
            { data: 'province', name: 'province', orderable: false, searchable: false },
            { data: 'city', name: 'city', orderable: false, searchable: false },
            { data: 'district', name: 'district', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'finished_at', name: 'finish_time' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],

        order: [[1, 'asc']],
        language: {
            paginate: {
                previous: '<<',
                next: '>>'
            }
        }
    });

    // Tombol filter
    $('#btnFilter').on('click', function() {
        table.draw();
    });

    // Tombol clear filter
    $('#btnClear').on('click', function() {
        $('#filter_q').val('');
        table.draw();
    });
});
</script>
@endsection
