@extends('admin.layouts.admin')

@section('title', 'Daftar Peringatan Stok Inventory')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="navbar-left">
            <a class="btn btn-default" href="{{ route('admin.dashboard') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
        </div>
        <div class="nav navbar-right">
            <a href="{{ route('admin.items.create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="items-table">
            <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Satuan Unit</th>
                <th class="text-center">Stok On Hand</th>
                <th class="text-center">Minimum Stok</th>
                <th class="text-center">Maksimum Stok</th>
                <th class="text-center">Gudang</th>
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
            $('#items-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: '{!! route('datatables.item_stock_notifications') !!}',
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable: false, class: 'text-center'},
                    { data: 'code', name: 'code', class: 'text-left'},
                    { data: 'name', name: 'name', class: 'text-left'},
                    { data: 'uom', name: 'uom', class: 'text-center'},
                    { data: 'stock', name: 'stock', class: 'text-right'},
                    { data: 'stock_min', name: 'stock_min', class: 'text-right'},
                    { data: 'stock_max', name: 'stock_max', class: 'text-right'},
                    { data: 'warehouse', name: 'warehouse', class: 'text-center'}
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
