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
        </form>
    </div>
    <hr style="border-color: #000000; width: 100%;"/>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                   width="100%" id="reorder_table">
                <thead>
                <tr>
                    <th class="text-center" style="width: 10%;">Tambah</th>
                    <th class="text-center" style="width: 10%;">Kode Item</th>
                    <th class="text-center" style="width: 10%;">Part Number</th>
                    <th class="text-center" style="width: 10%;">Keterangan</th>
                    <th class="text-center" style="width: 15%;">Gudang</th>
                    <th class="text-center" style="width: 15%;">Lokasi</th>
                    <th class="text-center" style="width: 10%;">Minimum Stock</th>
                    <th class="text-center" style="width: 10%;">Stock on Hand</th>
                    <th class="text-center" style="width: 10%;">Stock on Order</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <hr style="border-color: #000000; width: 100%;"/>
    {{ Form::open(['route'=>['admin.reorder.store'],'method' => 'post','id' => 'general-form', 'class'=>'form-horizontal form-label-left', 'enctype' => 'multipart/form-data', 'novalidate']) }}

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

    <div class="row">
        <div class="col-md-10">
            <h4>Inventory yang dipilih untuk reorder</h4>
            <input type="hidden" value="{{ $filterWarehouse }}" name="warehouseId">
            <input type="hidden" value="{{ $filterType }}" name="type" id="type">
        </div>
        <div class="col-md-2">
            <input type="submit" class="btn btn-success" value="PROSES MR">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                   width="100%" id="reorder_table_submit">
                <thead>
                <tr>
                    <th class="text-center" style="width: 10%;">Kode Item</th>
                    <th class="text-center" style="width: 10%;">Part Number</th>
                    <th class="text-center" style="width: 10%;">Keterangan</th>
                    <th class="text-center" style="width: 10%;">Gudang</th>
                    <th class="text-center" style="width: 10%;">Lokasi</th>
                    <th class="text-center" style="width: 10%;">Minimum Stock</th>
                    <th class="text-center" style="width: 10%;">Stock on Hand</th>
                    <th class="text-center" style="width: 10%;">Stock on Order</th>
                    <th class="text-center" style="width: 10%;">Reorder Qty</th>
                    <th class="text-center" style="width: 10%;">Tindakan</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    {{ Form::close() }}
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

        // Initialize array of reorders
        var reorders = [];

        $(function() {
            $('#reorder_table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: '{!! route('datatables.reorders') !!}',
                    data: {
                        'warehouse': '{{ $filterWarehouse }}',
                        'type': '{{ $filterType }}'
                    }
                },
                order: [ [0, 'asc'] ],
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},
                    { data: 'item_id', name: 'item_id' },
                    { data: 'part_number', name: 'item.part_number' },
                    { data: 'name', name: 'item.name' },
                    { data: 'warehouse_id', name: 'warehouse_id', class: 'text-center'},
                    { data: 'location', name: 'location', class: 'text-center'},
                    { data: 'stock_min', name: 'stock_min', class: 'text-right'},
                    { data: 'stock', name: 'stock', class: 'text-right'},
                    { data: 'stock_on_order', name: 'stock_on_order', class: 'text-right'}
                ],
                language: {
                    url: "{{ URL::asset('indonesian.json') }}"
                }
            });
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

            var stockMin = parseFloat($(this).data('stockmin'));
            var stockOnHand = parseFloat($(this).data('stock'));
            var stockOnOrder = parseFloat($(this).data('stockonorder'));
            var reorderQty = stockMin - (stockOnHand + stockOnOrder);

            $('#reorder_table_submit tr:last')
                .after("<tr id='" + idx + "'>" +
                    '<td>'+ code + '<input type="hidden" name="itemcode[]" value="'+ $(this).data('itemcode') +'"/></td>' +
                    '<td>'+ $(this).data('partnumber') + '</td>' +
                    '<td>'+ $(this).data('itemname') + '</td>' +
                    '<td class="text-center">'+ $(this).data('warehousename') + '</td>' +
                    '<td class="text-center">'+ $(this).data('location') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stockmin') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stock') + '</td>' +
                    '<td class="text-right">'+ $(this).data('stockonorder') + '</td>' +
                    '<td class="text-right">'+ reorderQty + '<input type="hidden" name="reorderQtys[]" value="' + reorderQty + '"/></td>' +
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

            var url = '{{ route('admin.reorder.list') }}';

            window.location = url + '?warehouse=' + warehouse + '&type=' + type;
        }

        function filterType(e){
            // Get stock filter value
            var type = e.value;
            var warehouse = $('#filter_warehouse').val();

            var url = '{{ route('admin.reorder.list') }}';

            window.location = url + '?warehouse=' + warehouse + '&type=' + type;
        }
    </script>
@endsection