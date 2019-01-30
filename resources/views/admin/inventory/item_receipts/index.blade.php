@extends('admin.layouts.admin')

@section('title', 'Daftar Goods Receipt')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-right">
            <a href="{{ route('admin.item_receipts.before_create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="gr-table">
            <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nomor GR</th>
                <th class="text-center">Nomor PO</th>
                <th class="text-center">No SJ/SPB</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Dibuat Oleh</th>
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
            $('#gr-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: '{!! route('datatables.item_receipts') !!}',
                order: [ [4, 'desc']],
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'po_code', name: 'po_code', class: 'text-center' },
                    { data: 'no_sj_spb', name: 'no_sj_spb', class: 'text-center' },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'created_by', name: 'created_by', class: 'text-center' },
                    { data: 'action', name: 'action', class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
