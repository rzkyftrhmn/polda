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
                                <label class="form-label">Cari Sub Divisi</label>
                                <input type="text" class="form-control mb-xl-0 mb-3"
                                       id="filter_q" placeholder="Cari nama sub divisi">
                            </div>
                            <div class="col-xl-3 col-sm-6 align-self-end">
                                <div>
                                    <button id="btnFilter" class="btn btn-primary me-2" type="button">
                                        <i class="fa fa-filter me-1"></i>Filter
                                    </button>
                                    <button id="btnClear" class="btn btn-danger light" type="button">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tambah Sub Divisi -->
            <div class="mb-3">
                <a href="{{ route('subdivisions.create') }}" class="btn btn-primary btn-sm">Tambah Sub Divisi</a>
            </div>

            <!-- DataTables -->
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa"><i class="fa-solid fa-file-lines me-1"></i>Daftar Sub Divisi</div>
                </div>

                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="subdivisions-table" class="display min-w850">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sub Divisi</th>
                                        <th>Divisi Induk</th>
                                        <th>Jenis</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sub Divisi</th>
                                        <th>Divisi Induk</th>
                                        <th>Jenis</th>
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
    var table = $('#subdivisions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("subdivisions.datatables") }}',
            type: 'GET',
            data: function(d) {
                d.filter_q = $('#filter_q').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name', name: 'name' },
            { data: 'parent', name: 'parent' },
            { data: 'type', name: 'type' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            paginate: { previous: '<<', next: '>>' }
        }
    });

    $('#btnFilter').on('click', function() {
        table.draw();
    });

    $('#btnClear').on('click', function() {
        $('#filter_q').val('');
        table.draw();
    });
});
</script>
@endsection
