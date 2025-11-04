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
                        <a href="javascript:void(0);" class="expand handle"><i
                                class="fal fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control mb-xl-0 mb-3"
                                    id="filter_q" placeholder="Search by Name, Email, or Username">
                            </div>
                            <div class="col-xl-3  col-sm-6 mb-3 mb-xl-0">
                                <label class="form-label">Institusi</label>
                                <select id="filter_institution_id" class="form-control default-select h-auto wide"
                                    aria-label="Default select example">
                                    <option value="">Select Institusi</option>
                                    <option value="1">Institusi 1</option>
                                </select>
                            </div>
                            <div class="col-xl-3  col-sm-6 mb-3 mb-xl-0">
                                <label class="form-label">Sub Bagian</label>
                                <select id="filter_division_id" class="form-control default-select h-auto wide"
                                    aria-label="Default select example">
                                    <option value="">Select Sub Bagian</option>
                                    <option value="1">Sub Bagian 1</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-sm-6 align-self-end">
                                <div>
                                    <button id="btnFilter" class="btn btn-primary me-2" title="Click here to Search"
                                        type="button"><i class="fa fa-filter me-1"></i>Filter</button>
                                    <button id="btnClear" class="btn btn-danger light" title="Click here to remove filter"
                                        type="button">Remove Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 pb-3">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Tambah User</a>
            </div>
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-file-lines me-1"></i>User List
                    </div>
                    <div class="tools">
                        <a href="javascript:void(0);" class="expand handle"><i
                                class="fal fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="users-table" class="display min-w850">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Institusi</th>
                                        <th>Sub Bagian</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Institusi</th>
                                        <th>Sub Bagian</th>
                                        <th>Nama</th>
                                        <th>Email</th>
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
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('datatables.users') }}',
                type: 'GET',
                data: function (d) {
                    d.filter_q = $('#filter_q').val();
                    d.filter_institution_id = $('#filter_institution_id').val();
                    d.filter_division_id = $('#filter_division_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'institution_name', name: 'institutions.name' },
                { data: 'division_name', name: 'divisions.name' },
                { data: 'name', name: 'users.name' },
                { data: 'email', name: 'users.email' },
                { data: 'created_at', name: 'users.created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [
                { targets: 0, className: 'text-center', width: '5%' },
                { targets: 1, width: '20%' },
                { targets: 2, width: '20%' },
                { targets: 3, width: '20%' },
                { targets: 4, width: '20%' },
                { targets: 5, width: '20%' },
                { targets: 6, className: 'text-nowrap text-center', width: '15%' },
            ],
            order: [[5, 'desc']],
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
            $('#filter_institution_id').val('');
            $('#filter_division_id').val('');
            table.draw();
        });
    });
</script>
@endsection