@extends('admin.layouts.admin')

@section('title', 'Pilih Purchase Request Terlebih Dahulu')

@section('content')

    <div class="row">
        <div class="navbar-left">
            <a class="btn btn-default" href="{{ route('admin.purchase_orders') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="pr-table">
            <thead>
            <tr>
                <th class="text-center">Tindakan</th>
                <th class="text-center">Nomor PR</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Departemen</th>
                <th class="text-center">Kode Unit</th>
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
            $('#pr-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.purchase_requests') !!}',
                    data: {
                        'mode': 'before_create_po'
                    }
                },
                order: [ [2, 'desc'] ],
                columns: [
                    { data: 'action', name: 'action',orderable: false, searchable: false, class: 'text-center' },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
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
