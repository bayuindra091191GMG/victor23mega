@extends('admin.layouts.admin')

@section('title','Ubah Data Inventory')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.items.update', $item->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @include('partials._success')
                    </div>
                </div>
            @endif

            @if(count($errors))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12 alert alert-danger alert-dismissible fade in" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">
                    Kode Inventory
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12" value="{{ $item->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                    Nama Inventory
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                           name="name" value="{{ $item->name }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="group" >
                    Kategori Inventory
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="group" name="group" class="form-control col-md-7 col-xs-12 @if($errors->has('group')) parsley-error @endif">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $item->group_id == $group->id ? "selected":"" }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="part_number">
                    Part Number
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="part_number" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('part_number')) parsley-error @endif"
                           name="part_number" value="{{ $item->part_number }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="uom" >
                    Satuan Unit
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="uom" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('uom')) parsley-error @endif"
                           name="uom" value="{{ $item->uom }}" required>
                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12">Gudang dan Stok</label>--}}
                {{--<div class="col-lg-4 col-md-4 col-xs-12 column">--}}
                    {{--<table class="table table-bordered table-hover" id="tab_logic">--}}
                        {{--<thead>--}}
                        {{--<tr >--}}
                            {{--<th class="text-center" style="width: 60%">--}}
                                {{--Gudang--}}
                            {{--</th>--}}
                            {{--<th class="text-center" style="width: 40%">--}}
                                {{--Stok--}}
                            {{--</th>--}}
                        {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tbody>--}}
                        {{--<tr id='addr0'>--}}
                            {{--<td class='field-item'>--}}
                                {{--<select id="warehouse0" name="warehouse[]" class='form-control'></select>--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--<input type='number' name='qty[]'  placeholder='Stok' class='form-control'/>--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--</tbody>--}}
                        {{--<tr id='addr1'></tr>--}}
                    {{--</table>--}}
                    {{--<a id="add_row" class="btn btn-default pull-left" style="margin-bottom: 10px;">Tambah</a><a id='delete_row' class="pull-right btn btn-default">Hapus</a>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery_type" >
                    Tipe Alat Berat
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="machinery_type" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery_type')) parsley-error @endif"
                           name="machinery_type" value="{{ $item->machinery_type }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="valuation" >
                    Nilai Beli per UOM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="valuation" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('valuation')) parsley-error @endif"
                           name="valuation" readonly>
                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="stock_min" >--}}
                    {{--Minimal Stok--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<input id="stock_min" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('stock_min')) parsley-error @endif"--}}
                           {{--name="stock_min" value="{{ $item->stock_minimum }}">--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="stock_notif"></label>--}}
                {{--<div class="col-md-4 col-sm-4 col-xs-12">--}}
                    {{--<div class="checkbox">--}}
                        {{--<label>--}}
                            {{--<input type="checkbox" class="flat" id="stock_notif" name="stock_notif" @if($item->stock_notification === 1) checked @endif> Notifikasi Stok--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description" >
                    Keterangan Tambahan
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="description" name="description" rows="5" class="form-control col-md-7 col-xs-12 @if($errors->has('description')) parsley-error @endif" style="resize: vertical">{{ $item->description }}</textarea>
                </div>
            </div>

            {{--<input type="hidden" name="is_used" value="{{ $isUsed === true ? '1' : '0' }}"/>--}}

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-warning" href="{{ route('admin.items') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>

            {{ Form::close() }}

            <hr/>

            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-0"></div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 box-section">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <h3>Gudang dan Stok</h3>

                        @if(!empty($itemStocks))
                            <table class="table table-bordered table-hover" id="warehouse-list">
                                <thead>
                                <tr >
                                    <th class="text-center" style="width: 20%">
                                        Gudang
                                    </th>
                                    <th class="text-center" style="width: 20%">
                                        Lokasi/Rak
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Stok On Hand
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Minimum Stok
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Maksimum Stok
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Notifikasi Stok
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Tindakan
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($itemStocks as $stock)
                                    <tr class="item{{ $stock->id }}">
                                        <td>
                                            {{ $stock->warehouse->name }}
                                        </td>
                                        <td>
                                            {{ $stock->location }}
                                        </td>
                                        <td class="text-right">
                                            {{ $stock->stock }}
                                        </td>
                                        <td class="text-right">
                                            {{ $stock->stock_min }}
                                        </td>
                                        <td class="text-right">
                                            {{ $stock->stock_max }}
                                        </td>
                                        <td class="text-center">
                                            {{ $stock->is_stock_warning ? 'AKTIF' : 'NON-AKTIF' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.item_stocks.option.edit', ['item_stock' => $stock->id]) }}" class="btn btn-default">PENGATURAN</a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="col-lg-12 col-md-12 col-xs-12 text-center">
                        <a href="{{ route('admin.item_stocks.option.create', ['item' => $item->id]) }}" class="btn btn-primary">TAMBAH PENGATURAN GUDANG BARU</a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-0"></div>
            </div>
        </div>
    </div>

    {{--<!-- Modal form to add new stock -->--}}
    {{--<div id="addModal" class="modal fade" role="dialog">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal">×</button>--}}
                    {{--<h4 class="modal-title"></h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<form class="form-horizontal" role="form">--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="warehouse_add">Gudang:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<select class="form-control" id="warehouse_add" name="warehouse_add"></select>--}}
                                {{--<p class="errorWarehouse text-center alert alert-danger hidden"></p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="stock_add">Stock:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="stock_add" name="stock_add">--}}
                                {{--<p class="errorStock text-center alert alert-danger hidden"></p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-warning" data-dismiss="modal">--}}
                            {{--<span class='glyphicon glyphicon-remove'></span> Batal--}}
                        {{--</button>--}}
                        {{--<button type="button" class="btn btn-success add" data-dismiss="modal">--}}
                            {{--<span id="" class='glyphicon glyphicon-check'></span> Simpan--}}
                        {{--</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<!-- Modal form to edit stock -->--}}
    {{--<div id="editModal" class="modal fade" role="dialog">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal">×</button>--}}
                    {{--<h4 class="modal-title"></h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<form class="form-horizontal" role="form">--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="warehouse_edit">Gudang:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<select class="form-control" id="warehouse_edit" name="warehouse_edit"></select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="stock_edit">Stock:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="stock_edit" name="stock_edit">--}}
                                {{--<p class="errorStock text-center alert alert-danger hidden"></p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-warning" data-dismiss="modal">--}}
                            {{--<span class='glyphicon glyphicon-remove'></span> Batal--}}
                        {{--</button>--}}
                        {{--<button type="button" class="btn btn-primary edit" data-dismiss="modal">--}}
                            {{--<span class='glyphicon glyphicon-check'></span> Simpan--}}
                        {{--</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<!-- Modal form to delete stock -->--}}
    {{--<div id="deleteModal" class="modal fade" role="dialog">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal">×</button>--}}
                    {{--<h4 class="modal-title"></h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<h3 class="text-center">Apakah anda yakin ingin menghapus stock ini?</h3>--}}
                    {{--<br />--}}
                    {{--<form class="form-horizontal" role="form">--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="warehouse_delete">Gudang:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="warehouse_delete" readonly>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="stock_delete">Stock:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="stock_delete" readonly>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-warning" data-dismiss="modal">--}}
                            {{--<span class='glyphicon glyphicon-remove'></span> Batal--}}
                        {{--</button>--}}
                        {{--<button type="button" class="btn btn-danger delete" data-dismiss="modal">--}}
                            {{--<span id="" class='glyphicon glyphicon-trash'></span> Hapus--}}
                        {{--</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    {{ Html::script(mix('assets/admin/js/stringbuilder.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">

        // autoNumeric
        @if(!empty($item->value) && $item->value > 0)
            valuationFormat = new AutoNumeric('#valuation', {
                minimumValue: '0',
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
            valuationFormat.clear();

            valuationFormat.set('{{ $item->value }}', {
                minimumValue: '0',
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        stockMinFormat = new AutoNumeric('#stock_min', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            modifyValueOnWheel: false
        });

        stockAddFormat = new AutoNumeric('#stock_add', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            modifyValueOnWheel: false
        });

        stockEditormat = new AutoNumeric('#stock_edit', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            modifyValueOnWheel: false
        });

        // Select warehouses
        $('#warehouse0').select2({
            placeholder: {
                id: '-1',
                text: '- Pilih Gudang -'
            },
            width: '100%',
            ajax: {
                url: '{{ route('select.warehouses') }}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });


        // Add new stock
        $(document).on('click', '.add-modal', function() {
            $('#warehouse_add').select2({
                placeholder: {
                    id: '-1',
                    text: 'Pilih Gudang...'
                },
                width: '100%',
                ajax: {
                    url: '{{ route('select.extended_warehouses') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term)
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('.modal-title').text('Tambah Stock');
            $('#addModal').modal('show');
        });
        $('.modal-footer').on('click', '.add', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.item_stocks.store') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'item_id': '{{ $item->id }}',
                    'warehouse_id': $('#warehouse_add').val(),
                    'stock': $('#stock_add').val()
                },
                success: function(data) {
                    $('.errorWarehouse').addClass('hidden');
                    $('.errorStock').addClass('hidden');

                    if ((data.errors)) {
                        if(data.errors === 'exists'){
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Stock sudah terdaftar, mohon refresh browser!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else{
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Gagal tambah stock!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);

                            if (data.errors.warehouse_id) {
                                $('.errorWarehouse').removeClass('hidden');
                                $('.errorWarehouse').text(data.errors.warehouse_id);
                            }
                            if (data.errors.stock) {
                                $('.errorStock').removeClass('hidden');
                                $('.errorStock').text(data.errors.stock);
                            }
                        }

                    } else {
                        toastr.success('Berhasil simpan stock!', 'Sukses', {timeOut: 6000, positionClass: "toast-top-center"});

                        var sbAdd = new stringbuilder();
                        sbAdd.append("<tr class='item" + data.id +"'>");
                        sbAdd.append("<td class='text-center'>" + data.site_name + "</td>");
                        sbAdd.append("<td class='text-center'>" + data.warehouse.name + "</td>");
                        sbAdd.append("<td class='text-center'>" + data.stock + "</td>");
                        sbAdd.append("<td class='text-center'>");
                        sbAdd.append("<button class='edit-modal btn btn-info' data-id='" + data.id + "' data-warehouse-id='" + data.warehouse_id + "' data-warehouse-text='" + data.warehouse.code + " - " + data.warehouse.name + "' data-stock='" + data.stock + "'>");
                        sbAdd.append("<span class='glyphicon glyphicon-edit'></span></button>");
                        sbAdd.append("<button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-warehouse-id='" + data.warehouse_id + "' data-warehouse-text='" + data.warehouse.code + " - " + data.warehouse.name + "' data-stock='" + data.stock + "'>");
                        sbAdd.append("<span class='glyphicon glyphicon-trash'></span></button>");
                        sbAdd.append("</td>");

                        $('#warehouse-list').append(sbAdd.toString());

                        // Reset add modal
                        $('#warehouse_add').val(null).trigger('change');
                        stockAddFormat.clear();
                    }
                },
            });
        });

        $("#addModal").on('hidden.bs.modal', function () {
            // Reset add modal
            $('#warehouse_add').val(null).trigger('change');
            stockAddFormat.clear();
        });

        // Edit detail
        $(document).on('click', '.edit-modal', function() {
            id = $(this).data('id');

            $('.modal-title').text('Ubah Stock');

            $('#warehouse_edit').select2({
                placeholder: {
                    id: $(this).data('warehouse-id'),
                    text: $(this).data('warehouse-text')
                },
                width: '100%',
                ajax: {
                    url: '{{ route('select.extended_warehouses') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term)
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#stock_edit').val($(this).data('stock'));
            $('#editModal').modal('show');
        });
        $('.modal-footer').on('click', '.edit', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.item_stocks.update') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id' : id,
                    'item_id' : '{{ $item->id }}',
                    'warehouse_id': $("#warehouse_edit").val(),
                    'stock': $('#stock_edit').val()
                },
                success: function(data) {
                    $('.errorWarehouse').addClass('hidden');
                    $('.errorStock').addClass('hidden');

                    if ((data.errors)) {
                        if(data.errors === 'used'){
                            setTimeout(function () {
                                $('#editModal').modal('show');
                                toastr.error('Gagal ubah stock, inventory sudah terpakai!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'deleted'){
                            setTimeout(function () {
                                $('#editModal').modal('show');
                                toastr.error('Stock telah terhapus, mohon refresh kembali!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'exists'){
                            setTimeout(function () {
                                $('#editModal').modal('show');
                                toastr.error('Stock sudah terdaftar, mohon refresh browser!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else{
                            setTimeout(function () {
                                $('#editModal').modal('show');
                                toastr.error('Gagal ubah data!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);

                            if (data.errors.warehouse_id) {
                                $('.errorWarehouse').removeClass('hidden');
                                $('.errorWarehouse').text(data.errors.warehouse_id);
                            }
                            if (data.errors.stock) {
                                $('.errorStock').removeClass('hidden');
                                $('.errorStock').text(data.errors.stock);
                            }
                        }
                    } else {
                        toastr.success('Berhasil ubah stock!', 'Sukses', {timeOut: 6000, positionClass: "toast-top-center"});

                        var sbEdit = new stringbuilder();
                        sbEdit.append("<tr class='item" + data.id +"'>");
                        sbEdit.append("<td class='text-center'>" + data.site_name + "</td>");
                        sbEdit.append("<td class='text-center'>" + data.warehouse.name + "</td>");
                        sbEdit.append("<td class='text-center'>" + data.stock + "</td>");
                        sbEdit.append("<td class='text-center'>");
                        sbEdit.append("<button class='edit-modal btn btn-info' data-id='" + data.id + "' data-warehouse-id='" + data.warehouse_id + "' data-warehouse-text='" + data.warehouse.code + " - " + data.warehouse.name + "' data-stock='" + data.stock + "'>");
                        sbEdit.append("<span class='glyphicon glyphicon-edit'></span></button>");
                        sbEdit.append("<button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-warehouse-id='" + data.warehouse_id + "' data-warehouse-text='" + data.warehouse.code + " - " + data.warehouse.name + "' data-stock='" + data.stock + "'>");
                        sbEdit.append("<span class='glyphicon glyphicon-trash'></span></button>");
                        sbEdit.append("</td>");

                        $('.item' + data.id).replaceWith(sbEdit.toString());
                    }
                }
            });
        });

        $("#editModal").on('hidden.bs.modal', function () {
            // Reset add modal
            $('#warehouse_edit').val(null).trigger('change');
            $('#stock_edit').val('0')
        });

        // Delete detail
        $(document).on('click', '.delete-modal', function() {
            $('.modal-title').text('Hapus Stock');
            $('#warehouse_delete').val($(this).data('warehouse-text'));
            $('#stock_delete').val($(this).data('stock'));
            $('#deleteModal').modal('show');
            deletedId = $(this).data('id')
        });
        $('.modal-footer').on('click', '.delete', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.item_stocks.destroy') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'item_id': '{{ $item->id }}',
                    'id': deletedId
                },
                success: function(data) {
                    toastr.success('Berhasil menghapus stock!', 'Sukses', {timeOut: 5000});
                    $('.item' + data['id']).remove();
                }
            });
        });
    </script>
@endsection