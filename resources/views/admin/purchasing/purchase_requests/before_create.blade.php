@extends('admin.layouts.admin')

@section('title', 'Pilih Material Request Terlebih Dahulu')

@section('content')

    <div class="row">
        <div class="navbar-left">
            <a class="btn btn-default" href="{{ route('admin.purchase_requests') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="mr-table">
            <thead>
            <tr>
                <th>Tindakan</th>
                <th>Nomor MR</th>
                <th>Jenis MR</th>
                <th>Departemen</th>
                <th>Kode Unit</th>
                <th>Tanggal</th>
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
                        'mode': 'before_create_pr',
                        'is_mr_exists': 'false'
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'type', name: 'type', class: 'text-center', orderable: false, searchable: false },
                    { data: 'department', name: 'department', class: 'text-center', orderable: false, searchable: false },
                    { data: 'machinery', name: 'machinery', class: 'text-center', orderable: false, searchable: false },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
