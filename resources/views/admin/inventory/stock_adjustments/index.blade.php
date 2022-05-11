@extends('admin.layouts.admin')

@section('title', 'Daftar Stock Adjustment')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-right">
            <a href="{{ route('admin.stock_adjustments.create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="stock-adjustment-table">
            <thead>
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Kode Inventory</th>
                <th class="text-center">Nama Inventory</th>
                <th class="text-center">QTY Pengurangan</th>
                <th class="text-center">Gudang</th>
                <th class="text-center">Dibuat Oleh</th>
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
            $('#stock-adjustment-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('datatables.stock_adjustments') !!}',
                order: [ [0, 'desc'] ],
                columns: [
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY HH:mm');
                            }
                            return data;
                        }
                    },
                    { data: 'item_code', name: 'item_code', class: 'text-center' },
                    { data: 'item_name', name: 'item_name', class: 'text-center' },
                    { data: 'depreciation', name: 'depreciation', class: 'text-center' },
                    { data: 'warehouse_name', name: 'warehouse_name', class: 'text-center' },
                    { data: 'created_by', name: 'created_by', class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
