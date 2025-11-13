@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="filter cm-content-box box-primary">
                <div class="content-title SlideToolHeader">
                    <div class="cpa">
                        <i class="fa-solid fa-user-shield me-1"></i> Detail Role
                    </div>
                </div>
                <div class="cm-content-body form excerpt">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Role</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $role->name ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guard Name</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ $role->guard_name ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dibuat</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ backChangeFormatDate($role->created_at) ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Diperbarui</label>
                                <div class="form-control" style="background: transparent; border-color: #3A3A4F;">
                                    {{ backChangeFormatDate($role->updated_at) ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Daftar Permission</label>
                                <div style="
                                    border: 1px solid #3A3A4F;
                                    border-radius: 10px;
                                    padding: 15px;
                                    display: grid;
                                    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                                    gap: 10px;
                                    ">
                                    @if(!empty($permissions))
                                        @foreach($permissions as $permName)
                                            <span style="
                                                display: inline-block;
                                                background: linear-gradient(135deg, #4C6EF5, #5B8CFF);
                                                color: #fff;
                                                padding: 6px 12px;
                                                border-radius: 8px;
                                                font-size: 0.85rem;
                                                font-weight: 500;
                                                text-align: center;
                                                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                                                ">
                                                {{ $permName }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span style="color: #999;">-</span>
                                    @endif
                                </div>
                            </div>


                            
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
