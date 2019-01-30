@extends('admin.layouts.admin')

@section('title', 'Pilih Material Request Terlebih Dahulu')

@section('content')

    <div class="row">
        @include('partials._error')
        <div class="navbar-left">
            <a class="btn btn-default" href="{{ route('admin.issued_dockets') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="mr-table">
            <thead>
            <tr>
                <th>Tindakan</th>
                <th>Nomor MR</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Departemen</th>
                <th>Kode Unit</th>
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
                ajax: {
                    url: '{!! route('datatables.material_requests') !!}',
                    data: {
                        'mode': 'before_create_id',
                        'type': 'non-service'
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'type', name: 'type', class: 'text-center', orderable: false, searchable: false },
                    { data: 'department', name: 'department', class: 'text-center', orderable: false, searchable: false },
                    { data: 'machinery', name: 'machinery', class: 'text-center', orderable: false, searchable: false }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
