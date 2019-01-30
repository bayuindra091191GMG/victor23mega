@extends('admin.layouts.admin')

@section('title','Buat Issued Docket Part Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.issued_dockets.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                    Nomor Issued Docket
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ $autoNumber }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <div class="control-label col-md-3 col-sm-3 col-xs-12">
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="flat" id="auto_number" name="auto_number" checked="checked"> Auto Number
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ old('date') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department">
                    Departemen
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="department" name="department" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif">
                        <option value="-1"> - Pilih Departemen - </option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account">
                    Cost Code
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="account" name="account" class="form-control col-md-7 col-xs-12 @if($errors->has('account')) parsley-error @endif">
                        @if(!empty(old('account')))
                            <option value="{{ old('account') }}" selected>{{ old('account_text') }}</option>
                        @endif
                    </select>
                    <input type="hidden" id="account_text" name="account_text" value="{{ old('account_text') }}"/>
                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mr_id">--}}
                    {{--Nomor MR--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<input id="mr_id" type="text" class="form-control col-md-7 col-xs-12"--}}
                           {{--name="mr_id" value="{{ $materialRequest->code }}" readonly>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="warehouse">
                    Gudang
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="warehouse" name="warehouse" class="form-control col-md-7 col-xs-12 @if($errors->has('warehouse')) parsley-error @endif">
                        <option value="-1"> - Pilih Gudang - </option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery">
                    Unit Alat Berat
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="machinery" name="machinery" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery')) parsley-error @endif">
                        @if(!empty(old('machinery')))
                            <option value="{{ old('machinery') }}" selected>{{ old('machinery_text') }}</option>
                        @endif
                    </select>
                    <input type="hidden" id="machinery_text" name="machinery_text" value="{{ old('machinery_text') }}"/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="hm">
                    HM<br/><span style="color: red;">Wajib isi apabila pilih unit alat berat</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="hm" type="text" class="form-control col-md-7 col-xs-12"
                           name="hm" value="{{ old('hm') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="km">
                    KM
                    <br/><span style="color: red;">Wajib isi apabila pilih unit alat berat</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="km" type="text" class="form-control col-md-7 col-xs-12"
                           name="km" value="{{ old('km') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="division">
                    Divisi
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="division" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('division')) parsley-error @endif"
                           name="division" value="{{ old('division') }}">
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-xs-12 box-section">
                    <h3 class="text-center">Detil Inventory</h3>
                    <a class="add-modal btn btn-info" style="margin-bottom: 10px;">
                        <span class="glyphicon glyphicon-plus-sign"></span> Tambah
                    </a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail_table">
                            <thead>
                            <tr >
                                <th class="text-center" style="width: 15%">
                                    Kode
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Part Number
                                </th>
                                <th class="text-center" style="width: 20%">
                                    Keterangan
                                </th>
                                <th class="text-center" style="width: 10%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 20%">
                                    Remark
                                </th>
                                <th class="text-center" style="width: 20%">
                                    Tindakan
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($idx = 0)
                            @if(!empty(old('item')))
                                @php( $oldItemCode = old('item_code') )
                                @php( $oldItemPartNumber = old('item_part_number') )
                                @php( $oldItemName = old('item_name') )
                                @php( $oldQty = old('qty') )
                                @php( $oldRemark = old('remark') )
                                @foreach(old('item') as $item)
                                    <tr class='item{{ $idx }}'>
                                        <td>
                                            {{ $oldItemCode[$idx] }}
                                            <input type='hidden' name='item[]' value='{{ $item }}'/>
                                            <input type='hidden' name='item_code[]' value='{{ $oldItemCode[$idx] }}'/>
                                        </td>
                                        <td>
                                            {{ $oldItemPartNumber[$idx] }}
                                            <input type='hidden' name='item_part_number[]' value='{{ $oldItemPartNumber[$idx] }}'/>
                                        </td>
                                        <td>
                                            {{ $oldItemName[$idx] }}
                                            <input type='hidden' name='item_name[]' value='{{ $oldItemName[$idx] }}'/>
                                        </td>
                                        <td class='text-right'>
                                            {{ $oldQty[$idx] }}
                                            <input type='hidden' name='qty[]'  value="{{ $oldQty[$idx] }}"/>
                                        </td>
                                        <td>
                                            {{ $oldRemark[$idx] }}
                                            <input type='hidden' name='remark[]' value="{{ $oldRemark[$idx] }}"/>
                                        </td>
                                        <td class="text-center">
                                            @php($itemId = $item. "#". $oldItemCode[$idx]. "#".  $oldItemName[$idx]. "#null#null#". $oldItemPartNumber[$idx])
                                            <a class="edit-modal btn btn-info" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $oldItemCode[$idx]. ' - '. $oldItemName[$idx] }}" data-qty="{{ $oldQty[$idx] }}" data-remark="{{ $oldRemark[$idx] }}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <a class="delete-modal btn btn-danger" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $oldItemCode[$idx]. ' - '. $oldItemName[$idx] }}" data-qty="{{ $oldQty[$idx] }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    @php( $idx++ )
                                @endforeach
                            @endif
                            {{--@if(!empty($materialRequest))--}}
                                {{--@foreach($materialRequest->material_request_details as $detail)--}}
                                    {{--@if($detail->quantity_issued < $detail->quantity)--}}
                                    {{--@php($idx++)--}}
                                    {{--<tr class='item{{ $idx }}'>--}}
                                        {{--<td class='text-center'>--}}
                                            {{--{{ $detail->item->code. ' - '. $detail->item->name }}--}}
                                            {{--<input type='hidden' name='item[]' value='{{ $detail->item_id }}'/>--}}
                                        {{--</td>--}}
                                        {{--<td class='text-center'>--}}
                                            {{--@php( $qty = $detail->quantity - $detail->quantity_issued )--}}
                                            {{--{{ $qty }}--}}
                                            {{--<input type='hidden' name='qty[]'  value="{{ $qty }}"/>--}}
                                        {{--</td>--}}
                                        {{--<td>--}}
                                            {{--{{ $detail->remark }}--}}
                                            {{--<input type='hidden' name='remark[]' value="{{ $detail->remark }}"/>--}}
                                        {{--</td>--}}
                                        {{--<td class="text-center">--}}
                                            {{--@php($itemId = $detail->item_id. "#". $detail->item->code. "#". $detail->item->name)--}}
                                            {{--<a class="edit-modal btn btn-info" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}" data-remark="{{ $detail->remark }}">--}}
                                                {{--<span class="glyphicon glyphicon-edit"></span>--}}
                                            {{--</a>--}}
                                            {{--<a class="delete-modal btn btn-danger" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}">--}}
                                                {{--<span class="glyphicon glyphicon-trash"></span>--}}
                                            {{--</a>--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr id='addr1'></tr>--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--@endif--}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-xs-0"></div>
            </div>

            <input id="index_counter" type="hidden" value="{{ $idx }}"/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.issued_dockets') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <!-- Modal form to add new detail -->
    <div id="addModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="item_add">Barang:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_add" name="item_add"></select>
                                <p class="errorItem text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_add">Qty:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" min="0" id="qty_add" name="qty_add" autocomplete="off">
                                <p class="errorQty text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_add">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_add" name="remark_add" cols="40" rows="5"></textarea>
                                <p class="errorRemark text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success add" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-check'></span> Simpan
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to edit a detail -->
    <div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="item_edit">Barang:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_edit" name="item_edit"></select>
                                <input type="hidden" id="item_old_value"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_edit">Qty:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" min="0" id="qty_edit" name="qty" autocomplete="off">
                                <p class="errorQty text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_edit">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_edit" name="remark" cols="40" rows="5"></textarea>
                                <p class="errorRemark text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <input type="hidden" id="edited_id"/>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success edit" data-dismiss="modal">
                            <span class='glyphicon glyphicon-check'></span> Simpan
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to delete a form -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin menghapus detail ini?</h3>
                    <br />
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="item_delete">Barang:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="item_delete" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_delete">Jumlah:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_delete" disabled>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger delete" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-trash'></span> Hapus
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
    <style>
        .box-section{
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 2px;
            padding: 10px;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    {{ Html::script(mix('assets/admin/js/stringbuilder.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        qtyAddFormat = new AutoNumeric('#qty_add', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        qtyEditFormat = new AutoNumeric('#qty_edit', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        var i=1;

        $('#account').select2({
            placeholder: {
                id: '-1',
                text: ' - Pilih Nomor Cost Code - '
            },
            width: '100%',
            minimumInputLength: 0,
            allowClear: true,
            ajax: {
                url: '{{ route('select.accounts') }}',
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

        $('#account').on('select2:select', function (e) {
            var data = e.params.data;
            $('#account_text').val(data.text);
        });

        $('#machinery').select2({
            placeholder: {
                id: '-1',
                text: ' - Pilih Unit Alat Berat - '
            },
            width: '100%',
            minimumInputLength: 1,
            allowClear: true,
            ajax: {
                url: '{{ route('select.machineries') }}',
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

        $('#machinery').on('select2:select', function (e) {
            var data = e.params.data;
            $('#machinery_text').val(data.text);
        });

        // Get selected PR data
        $(document).on('click', '.get-pr-data', function(){
            var url = '{{ route('admin.issued_dockets.create') }}';
            if($('#purchase_request_header').val() && $('#purchase_request_header').val() !== ""){
                url += "?pr=" + $('#purchase_request_header').val();
                window.location = url;
            }
            else{
                if($('#pr_id').val() && $('#pr_id').val() !== ""){
                    url += "?pr=" + $('#pr_id').val();
                    window.location = url;
                }
            }
        });

        // Clear selected PR data
        $(document).on('click', '.clear-pr-data', function(){
            var url = '{{ route('admin.issued_dockets.create') }}';
            window.location = url;
        });

        $('#auto_number').change(function(){
            if(this.checked){
                $('#code').val('{{ $autoNumber }}');
                $('#code').prop('disabled', true);
            }
            else{
                $('#code').val('');
                $('#code').prop('disabled', false);
            }
        });

        $('#select0').select2({
            placeholder: {
                id: '-1',
                text: ' - Pilih Inventory - '
            },
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route('select.items') }}',
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

        // Add new detail
        $(document).on('click', '.add-modal', function() {
            $('#item_add').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Inventory - '
                },
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('select.extended_items') }}',
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

            $('.modal-title').text('Tambah Detail');
            $('#addModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('.modal-footer').on('click', '.add', function() {
            var qtyAdd = $('#qty_add').val();
            var itemAdd = $('#item_add').val();
            var remarkAdd = $('#remark_add').val();

            if(!itemAdd || itemAdd === ""){
                alert('Mohon Pilih Part!');
                return false;
            }

            if(!qtyAdd || qtyAdd === "" || qtyAdd === "0"){
                alert('Mohon Isi Kuantitas!')
                return false;
            }

            // Split item value
            var splitted = itemAdd.split('#');
            var qty = parseFloat(qtyAdd);

            // Increase idx
            var idx = $('#index_counter').val();
            idx++;
            $('#index_counter').val(idx);

            var sbAdd = new stringbuilder();

            sbAdd.append("<tr class='item" + idx + "'>");
            sbAdd.append("<td>" + splitted[1]);
            sbAdd.append("<input type='hidden' name='item[]' value='" + splitted[0] + "'/>");
            sbAdd.append("<input type='hidden' name='item_code[]' value='" + splitted[1] + "'/></td>");
            sbAdd.append("<td>" + splitted[5]);
            sbAdd.append("<input type='hidden' name='item_part_number[]' value='" + splitted[5] + "'/></td>");
            sbAdd.append("<td>" + splitted[2]);
            sbAdd.append("<input type='hidden' name='item_name[]' value='" + splitted[2] + "'/></td>");

            if(qtyAdd && qtyAdd !== ""){
                sbAdd.append("<td class='text-right'>" + qtyAdd + "<input type='hidden' name='qty[]' value='" + qtyAdd + "'/></td>");
            }
            else{
                sbAdd.append("<td class='text-right'><input type='hidden' name='qty[]'/></td>");
            }

            sbAdd.append("<td>" + remarkAdd + "<input type='hidden' name='remark[]' value='" + remarkAdd + "'/></td>");

            sbAdd.append("<td class='text-center'>");
            sbAdd.append("<a class='edit-modal btn btn-info' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbAdd.append("<a class='delete-modal btn btn-danger' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbAdd.append("</td>");
            sbAdd.append("</tr>");

            $('#detail_table').append(sbAdd.toString());

            // Reset add form modal
            $('#item_add').val(null).trigger('change');
            qtyAddFormat.clear();
            $('#remark_add').val('');
        });

        $("#addModal").on('hidden.bs.modal', function () {
            // Reset edit form modal
            $('#item_add').val(null).trigger('change');
            qtyAddFormat.clear();
            $('#remark_add').val('');
        });

        // Edit detail
        $(document).on('click', '.edit-modal', function() {
            var editedId = $(this).data('id');

            $('.modal-title').text('Ubah Detail');

            $('#edited_id').val(editedId);
            $('#item_old_value').val($(this).data('item-id'));
            $('#item_edit').select2({
                placeholder: {
                    id: $(this).data('item-id'),
                    text: $(this).data('item-text')
                },
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('select.extended_items') }}',
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

            qtyEditFormat.clear();
            qtyEditFormat.set($(this).data('qty'),{
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0
            });

            $('#remark_edit').val($(this).data('remark'));
            $('#editModal').modal('show');
        });

        $('.modal-footer').on('click', '.edit', function() {
            var itemEdit = $('#item_edit').val();
            var qtyEdit = $('#qty_edit').val();
            var remarkEdit = $('#remark_edit').val();
            var editedId = $('#edited_id').val();

            if(!qtyEdit || qtyEdit === "" || qtyEdit === "0"){
                alert('Mohon Isi Kuantitas!')
                return false;
            }

            // Split item value
            var data = "default";
            if(itemEdit && itemEdit !== ''){
                data = itemEdit;
            }
            else {
                data = $('#item_old_value').val();
            }

            var splitted = data.split('#');

            var sbEdit = new stringbuilder();

            sbEdit.append("<tr class='item" + editedId + "'>");
            sbEdit.append("<td>" + splitted[1]);
            sbEdit.append("<input type='hidden' name='item[]' value='" + splitted[0] + "'/>");
            sbEdit.append("<input type='hidden' name='item_code[]' value='" + splitted[0] + "'/></td>");
            sbEdit.append("<td>" + splitted[5]);
            sbEdit.append("<input type='hidden' name='item_part_number[]' value='" + splitted[5] + "'/></td>");
            sbEdit.append("<td>" + splitted[2]);
            sbEdit.append("<input type='hidden' name='item_name[]' value='" + splitted[2] + "'/></td>");

            if(qtyEdit && qtyEdit !== ""){
                sbEdit.append("<td class='text-right'>" + qtyEdit + "<input type='hidden' name='qty[]' value='" + qtyEdit + "'/></td>");
            }
            else{
                sbEdit.append("<td class='text-right'><input type='hidden' name='qty[]'/></td>");
            }

            sbEdit.append("<td>" + remarkEdit + "<input type='hidden' name='remark[]' value='" + remarkEdit + "'/></td>");

            sbEdit.append("<td class='text-center'>");
            sbEdit.append("<a class='edit-modal btn btn-info' data-id='" + editedId + "' data-item-id='" + data + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbEdit.append("<a class='delete-modal btn btn-danger' data-id='" + editedId + "' data-item-id='" + data + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbEdit.append("</td>");
            sbEdit.append("</tr>");

            $('.item' + editedId).replaceWith(sbEdit.toString());

            // Reset edit form modal
            $('#item_edit').val(null).trigger('change');
            $('#item_old_value').val('');
            qtyEditFormat.clear();
            $('#remark_edit').val('');
        });

        $("#editModal").on('hidden.bs.modal', function () {
            // Reset edit form modal
            $('#item_edit').val(null).trigger('change');
            $('#item_old_value').val('');
            qtyEditFormat.clear();
            $('#remark_edit').val('');
        });

        // Delete detail
        var deletedId = "0";
        $(document).on('click', '.delete-modal', function() {
            $('.modal-title').text('Hapus Detail');
            deletedId = $(this).data('id');
            $('#item_delete').val($(this).data('item-text'));
            $('#qty_delete').val($(this).data('qty'));
            $('#remark_delete').val($(this).data('remark'));
            $('#deleteModal').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function() {
            $('.item' + deletedId).remove();
        });
    </script>
@endsection