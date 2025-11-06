@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <!-- Row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-sharp fa-solid fa-filter me-2"></i>Filter
                    </div>
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
                                       id="filter_q" placeholder="Search by Role Name">
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

            <!-- Tambah Role -->
            <div class="mb-4 pb-3">
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">Tambah Role</a>
            </div>

            <!-- Table Box -->
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-file-lines me-1"></i>Role List
                    </div>
                    <div class="tools">
                        <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                    </div>
                </div>

                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="roles-table" class="display min-w850">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
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
        // Inisialisasi DataTable
        var table = $('#roles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('datatables.roles') }}',
                type: 'GET',
                data: function(d) {
                    d.filter_q = $('#filter_q').val(); 
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [
                { targets: 0, className: 'text-center', width: '5%' },
                { targets: 1, width: '35%' },
                { targets: 2, width: '35%' },
                { targets: 3, className: 'text-nowrap text-center', width: '25%' },
            ],
            order: [[2, 'desc']],
            language: {
                paginate: {
                    previous: '<<',
                    next: '>>'
                }
            }
        });

        // Trigger filter
        $('#btnFilter').on('click', function() {
            table.draw(); 
        });

        // Clear filter
        $('#btnClear').on('click', function() {
            $('#filter_q').val(''); 
            table.draw(); 
        });
    });
</script>

@endsection
