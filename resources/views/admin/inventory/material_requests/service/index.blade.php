@extends('admin.layouts.admin')

@section('title', 'Daftar Material Request Servis')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-right">
            <a href="{{ route('admin.material_requests.service.create') }}" class="btn btn-app" target="_blank">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="mr-table">
            <thead>
            <tr>
                <th>Nomor MR</th>
                <th>Tanggal</th>
                <th>Prioritas</th>
                <th>Departemen</th>
                <th>Kode Unit</th>
                <th>Dibuat Oleh</th>
                <th>Tindakan</th>
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
            $('#mr-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.material_requests') !!}',
                    data: {
                        'type': 'service'
                    }
                },
                columns: [
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'priority', name: 'priority', class: 'text-center' },
                    { data: 'department', name: 'department', class: 'text-center', searchable: false, sortable: false },
                    { data: 'machinery', name: 'machinery', class: 'text-center', searchable: false, sortable: false },
                    { data: 'created_by', name: 'created_by', class: 'text-center', searchable: false, sortable: false },
                    { data: 'action', name: 'action',orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
