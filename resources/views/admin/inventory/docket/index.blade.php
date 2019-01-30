@extends('admin.layouts.admin')

@section('title', 'Daftar Issued Docket')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-right">
            <a href="{{ route('admin.issued_dockets.create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah ID Part
            </a>
            <a href="{{ route('admin.issued_dockets.fuel.create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah ID BBM
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="id-table">
            <thead>
            <tr>
                <th class="text-center">No Issued Docket</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Jenis</th>
                <th class="text-center">Nomor MR</th>
                <th class="text-center">Nomor Unit Alat Berat</th>
                <th class="text-center">Department</th>
                <th class="text-center">Cost Code</th>
                <th class="text-center">Status Retur</th>
                <th class="text-center">Tindakan</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script>
        $(function() {
            $('#id-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: '{!! route('datatables.issued_dockets') !!}',
                order: [ [1, 'desc'] ],
                columns: [
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'type', name: 'type', class: 'text-center', orderable: false, searchable: false },
                    { data: 'no_mr', name: 'no_mr', class: 'text-center', orderable: false, searchable: false },
                    { data: 'no_unit', name: 'no_unit', class: 'text-center', orderable: false, searchable: false },
                    { data: 'department', name: 'department', class: 'text-center', orderable: false, searchable: false },
                    { data: 'cost_code', name: 'cost_code', class: 'text-center', orderable: false, searchable: false },
                    { data: 'retur', name: 'retur', class: 'text-center', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
