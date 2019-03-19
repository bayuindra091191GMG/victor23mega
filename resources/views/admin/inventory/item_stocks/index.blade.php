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
                <div class="form-group">
                    <label for="filter_movement">Status Movement:</label>
                    <select id="filter_movement" class="form-control" onchange="filterMovement(this)">
                        <option value="ALL" @if($filterMovement == 'ALL') selected @endif>SEMUA</option>
                        <option value="DEAD" @if($filterMovement == 'DEAD') selected @endif>DEAD</option>
                        <option value="SLOW" @if($filterMovement == 'SLOW') selected @endif>SLOW</option>
                        <option value="MEDIUM" @if($filterMovement == 'MEDIUM') selected @endif>MEDIUM</option>
                        <option value="FAST" @if($filterMovement == 'FAST') selected @endif>FAST</option>
                    </select>
                </div>
                <div class="form-group">
                    <a href="{{ route('admin.item_stocks') }}" class="btn btn-primary" style="margin: 0 !important;">RESET</a>
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
        <div class="table-responsive" id="table_container">
            <table class="table table-striped table-bordered dt-responsive" cellspacing="0"
                   width="100%" id="items-table">
                <thead>
                <tr>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Nama</th>
                    <th class="text-center">Part Number</th>
                    <th class="text-center">Gudang</th>
                    <th class="text-center">Lokasi/Rak</th>
                    <th class="text-center">Satuan Unit</th>
                    <th class="text-center">Stock on Hand</th>
                    <th class="text-center">Stock on Order</th>
                    <th class="text-center">Minimum Stok</th>
                    <th class="text-center">Maksimum Stok</th>
                    <th class="text-center">Qty Issued 12 Bulan</th>
                    <th class="text-center">Movement Status</th>
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
                responsive: false,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.item_stocks') !!}',
                    data: {
                        'site': '{{ $site }}',
                        'warehouse': '{{ $filterWarehouse }}',
                        'stock': '{{ $filterStock }}',
                        'movement': '{{ $filterMovement }}'
                    }
                },
                columns: [
                    { data: 'code', name: 'item.code' },
                    { data: 'name', name: 'item.name' },
                    { data: 'part_number', name: 'item.part_number' },
                    { data: 'warehouse', name: 'warehouse', class: 'text-center', searchable: false, sortable: false },
                    { data: 'location', name: 'item_stocks.location' },
                    { data: 'uom', name: 'item.uom', class: 'text-center' },
                    { data: 'stock', name: 'stock', class: 'text-right' },
                    { data: 'stock_on_order', name: 'stock_on_order', class: 'text-right' },
                    { data: 'stock_min', name: 'stock_min', class: 'text-right' },
                    { data: 'stock_max', name: 'stock_max', class: 'text-right' },
                    { data: 'qty_issued_12_months', name: 'qty_issued_12_months', class: 'text-right' },
                    { data: 'movement_status', name: 'movement_status', class: 'text-center' },
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

            var warehouse = e.value;
            var stockStatus = $('#filter_stock').val();
            var movement = $('#filter_movement').val();

            var url = '{{ route('admin.item_stocks') }}';

            window.location = url + '?warehouse=' + warehouse + '&stock=' + stockStatus + '&movement=' + movement;
        }

        function filterStock(e){

            var stockStatus = e.value;
            var warehouse = $('#filter_warehouse').val();
            var movement = $('#filter_movement').val();

            var url = '{{ route('admin.item_stocks') }}';

            window.location = url + '?warehouse=' + warehouse + '&stock=' + stockStatus + '&movement=' + movement;
        }

        function filterMovement(e){

            var movement = e.value;
            var warehouse = $('#filter_warehouse').val();
            var stockStatus = $('#filter_stock').val();

            var url = '{{ route('admin.item_stocks') }}';

            window.location = url + '?movement=' + movement + '&stock=' + stockStatus + '&warehouse=' + warehouse;
        }
    </script>
    @include('partials._deleteJs', ['routeUrl' => 'admin.items.destroy', 'redirectUrl' => 'admin.items'])
@endsection
