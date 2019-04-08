@extends('admin.layouts.admin')

@section('title', 'Daftar Reorder')

@section('content')

    <hr style="border-color: #000000; width: 100%;"/>
    <div class="nav navbar-left">
        <form class="form-inline" style="margin-bottom: 10px;">
            <div class="form-group">
                <label for="filter_warehouse">Gudang:</label>
                <select id="filter_warehouse" class="form-control" onchange="filterWarehouse(this)">
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @if($filterWarehouse == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-left: 20px;">
                <label for="filter_type">Kategori:</label>
                <select id="filter_type" class="form-control" onchange="filterType(this)">
                    <option value="1" @if($filterType == '1') selected @endif>Part/Non-Part</option>
                    <option value="2" @if($filterType == '2') selected @endif>BBM</option>
                    <option value="3" @if($filterType == '3') selected @endif>Oli</option>
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
        </form>
    </div>
    <div class="nav navbar-right">
        <a class="btn btn-success" onclick="submitReorder();">PROSES REORDER MR</a>
    </div>
    <hr style="border-color: #000000; width: 100%;"/>

    {{ Form::open(['route'=>['admin.reorder.store'],'method' => 'post','id' => 'general-form', 'enctype' => 'multipart/form-data', 'novalidate']) }}

    @if(count($errors))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">

            <div class="table-responsive" id="table_container">
                <table id="reorder_table" class="table table-striped table-bordered dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">Hapus</th>
                        <th class="text-center" style="width: 10%;">Kode Item</th>
                        <th class="text-center" style="width: 10%;">Part Number</th>
                        <th class="text-center" style="width: 10%;">Keterangan</th>
                        <th class="text-center" style="width: 5%;">Gudang</th>
                        <th class="text-center" style="width: 5%;">Lokasi</th>
                        <th class="text-center" style="width: 10%;">Qty Issued 12 Bulan</th>
                        <th class="text-center" style="width: 10%;">Stock on Hand</th>
                        <th class="text-center" style="width: 10%;">Stock on Order</th>
                        <th class="text-center" style="width: 10%;">Minimum Stok</th>
                        <th class="text-center" style="width: 10%;">Maksimum Stok</th>
                        <th class="text-center" style="width: 5%;">Movement Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($itemStocks as $itemStock)
                            <tr data-stock-id="{{ $itemStock->id }}">
                                <td>
                                    <a class="remove-row btn btn-xs btn-danger"><i class='fa fa-minus'></i></a>
{{--                                    <input type="hidden" name="stock_ids[]" value="{{ $itemStock->id }}"/>--}}
{{--                                    <input type="hidden" name="item_stock_maxs[]" value="{{ $itemStock->stock_max }}"/>--}}
                                </td>
                                <td>{{ $itemStock->item->code }}</td>
                                <td>{{ $itemStock->item->part_number }}</td>
                                <td>{{ $itemStock->item->name }}</td>
                                <td class="text-center">{{ $itemStock->warehouse->name }}</td>
                                <td>{{ $itemStock->location ?? '-' }}</td>
                                <td class="text-right">{{ $itemStock->qty_issued_12_months }}</td>
                                <td class="text-right">{{ $itemStock->stock }}</td>
                                <td class="text-right">{{ $itemStock->stock_on_order ?? 0 }}</td>
                                <td class="text-right">{{ $itemStock->stock_min }}</td>
                                <td class="text-right">{{ $itemStock->stock_max }}</td>
                                <td class="text-center">{{ $itemStock->movement_status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <input type="hidden" value="{{ $filterWarehouse }}" name="warehouse_id">
            <input type="hidden" value="{{ $filterType }}" name="type_id" id="type">
            <input type="hidden" value="{{ $filterMovement }}" name="movement_status_id" id="movement_status">
        </div>
        <div class="col-md-2 text-right">
{{--            <a class="btn btn-success" onclick="submitReorder();">PROSES MR</a>--}}
{{--            <input type="submit" class="btn btn-success" value="PROSES MR">--}}
        </div>
    </div>

    {{ Form::close() }}
{{--    <hr style="border-color: #000000; width: 100%;"/>--}}

{{--    <div class="row">--}}
{{--        <div class="col-md-10">--}}
{{--            <h4>Inventory yang dipilih untuk reorder</h4>--}}
{{--            <input type="hidden" value="{{ $filterWarehouse }}" name="warehouseId">--}}
{{--            <input type="hidden" value="{{ $filterType }}" name="type" id="type">--}}
{{--        </div>--}}
{{--        <div class="col-md-2">--}}
{{--            <input type="submit" class="btn btn-success" value="PROSES MR">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="row">--}}
{{--        <div class="col-md-12">--}}
{{--            <table class="table table-striped table-bordered dt-responsive nowrap" id="reorder_table_submit">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th class="text-center" style="width: 10%;">Kode Item</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Part Number</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Keterangan</th>--}}
{{--                    <th class="text-center" style="width: 5%;">Gudang</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Lokasi</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Qty Issued 12 Bulan</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Stock on Hand</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Stock on Order</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Minimum Stok</th>--}}
{{--                    <th class="text-center" style="width: 10%;">Maksimum Stok</th>--}}
{{--                    <th class="text-center" style="width: 5%;">Tindakan</th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                </tbody>--}}
{{--            </table>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection

@section('styles')
    @parent
{{--    {{ Html::style(mix('assets/admin/css/datatables.css')) }}--}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
{{--    {{ Html::script(mix('assets/admin/js/datatables.js')) }}--}}
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>

        // Initialize array of reorders
        var reorders = [];
        var reorderTable = $('#reorder_table').DataTable({
            "order": [[ 1, "asc" ]]
        });

        var removedStockIdArr = [];

        $('#reorder_table tbody').on( 'click', '.remove-row', function () {
            let removedRow = $(this).closest('tr');

            //alert(removedRow.data('stock-id'));
            let removedStockId = removedRow.data('stock-id');
            removedStockIdArr.push(removedStockId);

            removedRow.fadeOut(400, function () {
                reorderTable
                    .row( $(this).parents('tr') )
                    .remove()
                    .draw();

            });
        } );

        function submitReorder() {

            $('<input />').attr('type', 'hidden')
                .attr('name', "removed_stock_ids")
                .attr('value', removedStockIdArr)
                .appendTo('#general-form');

            $('#general-form').submit();
        }

        $(function() {
            {{--$('#reorder_table').DataTable({--}}
            {{--    processing: true,--}}
            {{--    serverSide: true,--}}
            {{--    responsive: false,--}}
            {{--    pageLength: 10,--}}
            {{--    ajax: {--}}
            {{--        url: '{!! route('datatables.reorders') !!}',--}}
            {{--        data: {--}}
            {{--            'warehouse': '{{ $filterWarehouse }}',--}}
            {{--            'type': '{{ $filterType }}',--}}
            {{--            'movement': '{{ $filterMovement }}'--}}
            {{--        }--}}
            {{--    },--}}
            {{--    order: [ [0, 'asc'] ],--}}
            {{--    columns: [--}}
            {{--        { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},--}}
            {{--        { data: 'item_id', name: 'item_id' },--}}
            {{--        { data: 'part_number', name: 'item.part_number' },--}}
            {{--        { data: 'name', name: 'item.name' },--}}
            {{--        { data: 'warehouse_id', name: 'warehouse_id', class: 'text-center'},--}}
            {{--        { data: 'location', name: 'location', class: 'text-center'},--}}
            {{--        { data: 'qty_issued_12_months', name: 'qty_issued_12_months', class: 'text-right'},--}}
            {{--        { data: 'stock', name: 'stock', class: 'text-right'},--}}
            {{--        { data: 'stock_on_order', name: 'stock_on_order', class: 'text-right'},--}}
            {{--        { data: 'stock_min', name: 'stock_min', class: 'text-right'},--}}
            {{--        { data: 'stock_max', name: 'stock_max', class: 'text-right'},--}}
            {{--        { data: 'movement_status', name: 'movement_status', class: 'text-center'},--}}
            {{--    ],--}}
            {{--    language: {--}}
            {{--        url: "{{ URL::asset('indonesian.json') }}"--}}
            {{--    }--}}
            {{--});--}}
        });

        var idx = 1;

        $(document).on('click', '.add-table', function(){
            var code = $(this).data('itemcode');

            // Check exists & prevent duplicates
            if(reorders.length > 0){
                for(var h = 0; h < reorders.length; h++){
                    if(reorders[h].code === code){
                        return false;
                    }
                }
            }

            // var stockMin = parseFloat($(this).data('stockmin'));
            // var stockOnHand = parseFloat($(this).data('stock'));
            // var stockOnOrder = parseFloat($(this).data('stockonorder'));
            // var reorderQty = stockMin - (stockOnHand + stockOnOrder);

            $('#reorder_table_submit tr:last')
                .after("<tr id='" + idx + "'>" +
                    '<td>'+ code + '<input type="hidden" name="itemcode[]" value="'+ $(this).data('itemcode') +'"/></td>' +
                    '<td>'+ $(this).data('partnumber') + '</td>' +
                    '<td>'+ $(this).data('itemname') + '</td>' +
                    '<td class="text-center">'+ $(this).data('warehousename') + '</td>' +
                    '<td class="text-center">'+ $(this).data('location') + '</td>' +
                    '<td class="text-center">'+ $(this).data('qty_issued_12_months') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stock') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stockonorder') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stockmin') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stockmax') + '<input type="hidden" name="stock_max[]" value="' + $(this).data('stockmax') + '" /></td>' +
                    '<td class="text-center"><a class="delete-row btn btn-xs btn-danger" data-id="' + idx + '">' +
                    '<i class="fa fa-minus"></i>' +
                    '</a></td>' +
                    '</tr>');

            // Add to reorder array
            var reorderObj = {};
            reorderObj.index = idx;
            reorderObj.code = code;

            reorders.push(reorderObj);

            idx++;
        });

        $(document).on('click', '.delete-row', function(){
            var id = $(this).data('id');

            // Remove from reorder array
            if(reorders.length > 0){
                for(var h = 0; h < reorders.length; h++){
                    if(reorders[h].index === id){
                        reorders.splice(h, 1);
                    }
                }
            }

            $('#'+id).remove();
        });

        function filterWarehouse(e){
            // Get stock filter value
            var warehouse = e.value;
            var type = $('#filter_type').val();
            var movement = $('#filter_movement').val();

            var url = '{{ route('admin.reorder.list') }}';

            window.location = url + '?warehouse=' + warehouse + '&type=' + type + '&movement=' + movement;
        }

        function filterType(e){
            // Get stock filter value
            var type = e.value;
            var warehouse = $('#filter_warehouse').val();
            var movement = $('#filter_movement').val();

            var url = '{{ route('admin.reorder.list') }}';

            window.location = url + '?warehouse=' + warehouse + '&type=' + type + '&movement=' + movement;
        }

        function filterMovement(e){
            // Get stock filter value
            var movement = e.value;
            var warehouse = $('#filter_warehouse').val();
            var type = $('#filter_type').val();

            var url = '{{ route('admin.reorder.list') }}';

            window.location = url + '?movement=' + movement + '&type=' + type + '&warehouse=' + warehouse;
        }
    </script>
@endsection