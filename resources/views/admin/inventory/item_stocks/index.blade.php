@extends('admin.layouts.admin')

@section('title', 'Daftar Stock Inventory')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-left">
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label for="filter_warehouse">Gudang:</label>
                    <select id="filter_warehouse" class="form-control" onchange="filterWarehouse(this)">
                        @if($site === 1)
                            <option value="-1" @if($filterWarehouse == '-1') selected @endif>Semua</option>
                        @endif
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @if($filterWarehouse == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter_stock">Status Stok:</label>
                    <select id="filter_stock" class="form-control" onchange="filterStock(this)">
                        <option value="0" @if($filterStock == '0') selected @endif>Semua</option>
                        <option value="1" @if($filterStock == '1') selected @endif>Tersedia</option>
                        <option value="2" @if($filterStock == '2') selected @endif>Kosong</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="nav navbar-right">
            <a href="{{ route('admin.item_stocks.excel') }}" class="btn btn-app">
                <i class="fa fa-file-excel-o"></i> EXCEL
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive" cellspacing="0"
               width="100%" id="items-table">
            <thead>
            <tr>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Gudang</th>
                <th class="text-center">Lokasi/Rak</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Satuan Unit</th>
                <th class="text-center">Minimum Stok</th>
                <th class="text-center">Maksimum Stok</th>
                <th class="text-center">Stock on Order</th>
                <th class="text-center">Notifikasi Stok</th>
                <th class="text-center">Kategori Inventory</th>
                <th class="text-center">Tipe Alat Berat</th>
                <th class="text-center">Tindakan</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    @include('partials._delete')
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(function() {
            $('#items-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.item_stocks') !!}',
                    data: {
                        'site': '{{ $site }}',
                        'warehouse': '{{ $filterWarehouse }}',
                        'stock': '{{ $filterStock }}'
                    }
                },
                columns: [
                    { data: 'code', name: 'item.code' },
                    { data: 'name', name: 'item.name' },
                    { data: 'part_number', name: 'item.part_number' },
                    { data: 'warehouse', name: 'warehouse', class: 'text-center', searchable: false, sortable: false },
                    { data: 'location', name: 'item_stocks.location' },
                    { data: 'stock', name: 'stock', class: 'text-right' },
                    { data: 'uom', name: 'item.uom', class: 'text-center' },
                    { data: 'stock_min', name: 'stock_min', class: 'text-right' },
                    { data: 'stock_max', name: 'stock_max', class: 'text-right' },
                    { data: 'stock_on_order', name: 'stock_on_order', class: 'text-right' },
                    { data: 'is_stock_warning', name: 'is_stock_warning', class: 'text-center' },
                    { data: 'group', name: 'group', class: 'text-center', searchable: false, sortable: false },
                    { data: 'machinery_type', name: 'item.machinery_type', class: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });

        $(document).on('click', '.delete-modal', function(){
            $('#deleteModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#deleted-id').val($(this).data('id'));
        });

        function filterWarehouse(e){
            // Get stock filter value
            var warehouse = e.value;
            var stockStatus = $('#filter_stock').val();

            var url = '{{ route('admin.item_stocks') }}';

            window.location = url + '?warehouse=' + warehouse + '&stock=' + stockStatus;
        }

        function filterStock(e){
            // Get stock filter value
            var stockStatus = e.value;
            var warehouse = $('#filter_warehouse').val();

            var url = '{{ route('admin.item_stocks') }}';

            window.location = url + '?warehouse=' + warehouse + '&stock=' + stockStatus;
        }
    </script>
    @include('partials._deleteJs', ['routeUrl' => 'admin.items.destroy', 'redirectUrl' => 'admin.items'])
@endsection
