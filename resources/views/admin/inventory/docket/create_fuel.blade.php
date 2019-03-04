@extends('admin.layouts.admin')

@section('title','Buat Issued Docket BBM Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.issued_dockets.fuel.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                                <th class="text-center" style="width: 10%">
                                    Inventory
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Unit Alat Berat
                                </th>
                                <th class="text-center" style="width: 10%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 5%">
                                    UOM
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Shift
                                </th>
                                <th class="text-center" style="width: 5%">
                                    Jam
                                </th>
                                <th class="text-center" style="width: 5%">
                                    HM
                                </th>
                                <th class="text-center" style="width: 5%">
                                    KM
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Fuelman
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Operator
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Remark
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Tindakan
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($idx = 0)
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
                                                {{--<input type='hidden' name='machinery[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-right'>--}}
                                                {{--@php( $qty = $detail->quantity - $detail->quantity_issued )--}}
                                                {{--{{ $qty }}--}}
                                                {{--<input type='hidden' name='qty[]'  value="{{ $qty }}"/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--{{ $detail->item->uom }}--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--<input type='hidden' name='shift[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--<input type='hidden' name='time[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--<input type='hidden' name='hm[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--<input type='hidden' name='km[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--<input type='hidden' name='fuelman[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td class='text-center'>--}}
                                                {{--<input type='hidden' name='operator[]' value=''/>--}}
                                            {{--</td>--}}
                                            {{--<td>--}}
                                                {{--{{ $detail->remark }}--}}
                                                {{--<input type='hidden' name='remark[]' value="{{ $detail->remark }}"/>--}}
                                            {{--</td>--}}
                                            {{--<td class="text-center">--}}
                                                {{--<a class="edit-modal btn btn-info"--}}
                                                   {{--data-id="{{ $idx }}"--}}
                                                   {{--data-item-id="{{ $detail->item_id }}"--}}
                                                   {{--data-item-text="{{ $detail->item->code }}"--}}
                                                   {{--data-item-uom="{{ $detail->item->uom }}"--}}
                                                   {{--data-machinery-id="" data-machinery-text=""--}}
                                                   {{--data-qty="{{ $detail->quantity }}"--}}
                                                   {{--data-shift=""--}}
                                                   {{--data-time=""--}}
                                                   {{--data-hm=""--}}
                                                   {{--data-km=""--}}
                                                   {{--data-fuelman=""--}}
                                                   {{--data-operator=""--}}
                                                   {{--data-remark="{{ $detail->remark }}">--}}
                                                    {{--<span class="glyphicon glyphicon-edit"></span>--}}
                                                {{--</a>--}}
                                                {{--<a class="delete-modal btn btn-danger" data-id="{{ $idx }}" data-item-id="{{ $detail->item_id }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-machinery-id="" data-machinery-text="" data-qty="{{ $detail->quantity }}">--}}
                                                    {{--<span class="glyphicon glyphicon-trash"></span>--}}
                                                {{--</a>--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
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
                            <label class="control-label col-sm-2" for="item_add">Inventory *:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_add" name="item_add"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="machinery_add">Alat Berat *:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="machinery_add" name="machinery_add"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_add">QTY *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_add" name="qty_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="shift_add">Shift *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="shift_add" name="shift_add" style="text-transform: uppercase;" placeholder="MALAM atau SIANG">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="time_add">Jam *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="time_add" name="time_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="hm_add">HM *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="hm_add" name="hm_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="km_add">KM *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="km_add" name="km_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="fuelman_add">Fuelman *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="fuelman_add" name="fuelman_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="operator_add">Operator *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="operator_add" name="operator_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_add">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_add" name="remark_add" cols="40" rows="5"></textarea>
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
                            <label class="control-label col-sm-2" for="item_edit">Inventory *:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_edit" name="item_edit"></select>
                                <input type="hidden" id="item_old_value"/>
                                <input type="hidden" id="item_old_text"/>
                                <input type="hidden" id="item_old_uom"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="machinery_edit">Alat Berat *:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="machinery_edit" name="machinery_edit"></select>
                                <input type="hidden" id="machinery_old_value"/>
                                <input type="hidden" id="machinery_old_text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_edit">QTY *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" min="0" id="qty_edit" name="qty">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2" for="shift_edit">Shift *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="shift_edit" name="shift_edit" style="text-transform: uppercase;" placeholder="MALAM atau SIANG">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="time_edit">Jam *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="time_edit" name="time_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="hm_edit">HM *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="hm_edit" name="hm_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="km_edit">KM *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="km_edit" name="km_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="fuelman_edit">Fuelman *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="fuelman_edit" name="fuelman_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="operator_edit">Operator *:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="operator_edit" name="operator_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_edit">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_edit" name="remark" cols="40" rows="5"></textarea>
                            </div>
                        </div>
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
                            <label class="control-label col-sm-2" for="item_delete">Inventory:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="item_delete" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="machinery_delete">Alat Berat:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="machinery_delete" disabled>
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

        $('#time_add').datetimepicker({
            format: "HH:mm"
        });

        $('#time_edit').datetimepicker({
            format: "HH:mm"
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

        // Add new detail
        $(document).on('click', '.add-modal', function() {
            $('#item_add').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Inventory - '
                },
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('select.extended_items') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term),
                            type: 'fuel'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#machinery_add').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Alat Berat - '
                },
                width: '100%',
                minimumInputLength: 1,
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

            $('.modal-title').text('Tambah Detail');
            $('#addModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('.modal-footer').on('click', '.add', function() {
            var qtyAdd = $('#qty_add').val();
            var itemAdd = $('#item_add').val();
            var machineryAdd = $('#machinery_add').val();
            var remarkAdd = $('#remark_add').val();
            var shiftAdd = $('#shift_add').val();
            var timeAdd = $('#time_add').val();
            var hmAdd = $('#hm_add').val();
            var kmAdd = $('#km_add').val();
            var fuelmanAdd = $('#fuelman_add').val();
            var operatorAdd = $('#operator_add').val();

            if(!shiftAdd || shiftAdd === "" ||
                !timeAdd || timeAdd === "" ||
                !hmAdd || hmAdd === "" ||
                !kmAdd || kmAdd === "" ||
                !fuelmanAdd || fuelmanAdd === "" ||
                !operatorAdd || operatorAdd === ""){
                alert('Field dengan tanda * wajib diisi!');
                return false;
            }

            shiftAdd = shiftAdd.toUpperCase();

            if(!itemAdd || itemAdd === ""){
                alert('Mohon Pilih Inventory!');
                return false;
            }

            var itemAddText = $('#item_add').text();
            var splittedItemAdd = itemAdd.split('#');

            if(!machineryAdd || machineryAdd === ""){
                alert('Mohon Pilih Alat Berat!');
                return false;
            }

            var machineryAddText = $('#machinery_add').text();

            if(!qtyAdd || qtyAdd === "" || qtyAdd === "0"){
                alert('Mohon Isi Kuantitas!')
                return false;
            }

            // Split item value
            var qty = parseFloat(qtyAdd);

            // Increase idx
            var idx = $('#index_counter').val();
            idx++;
            $('#index_counter').val(idx);

            var sbAdd = new stringbuilder();

            sbAdd.append("<tr class='item" + idx + "'>");
            sbAdd.append("<td class='text-center'>" + itemAddText);
            sbAdd.append("<input type='hidden' name='item[]' value='" + splittedItemAdd[0] + "'/></td>");
            sbAdd.append("<td class='text-center'>" + machineryAddText);
            sbAdd.append("<input type='hidden' name='machinery[]' value='" + machineryAdd + "'/></td>");

            if(qtyAdd && qtyAdd !== ""){
                sbAdd.append("<td class='text-right'>" + qtyAdd + "<input type='hidden' name='qty[]' value='" + qtyAdd + "'/></td>");
            }
            else{
                sbAdd.append("<td class='text-right'><input type='hidden' name='qty[]'/></td>");
            }

            sbAdd.append("<td class='text-center'>" + splittedItemAdd[3] + "</td>");
            sbAdd.append("<td class='text-center'>" + shiftAdd);
            sbAdd.append("<input type='hidden' name='shift[]' value='" + shiftAdd + "'/></td>");
            sbAdd.append("<td class='text-center'>" + timeAdd);
            sbAdd.append("<input type='hidden' name='time[]' value='" + timeAdd + "'/></td>");
            sbAdd.append("<td class='text-center'>" + hmAdd);
            sbAdd.append("<input type='hidden' name='hm[]' value='" + hmAdd + "'/></td>");
            sbAdd.append("<td class='text-center'>" + kmAdd);
            sbAdd.append("<input type='hidden' name='km[]' value='" + kmAdd + "'/></td>");
            sbAdd.append("<td class='text-center'>" + fuelmanAdd);
            sbAdd.append("<input type='hidden' name='fuelman[]' value='" + fuelmanAdd + "'/></td>");
            sbAdd.append("<td class='text-center'>" + operatorAdd);
            sbAdd.append("<input type='hidden' name='operator[]' value='" + operatorAdd + "'/></td>");
            sbAdd.append("<td>" + remarkAdd + "<input type='hidden' name='remark[]' value='" + remarkAdd + "'/></td>");

            sbAdd.append("<td class='text-center'>");
            sbAdd.append("<a class='edit-modal btn btn-info' data-id='" + idx + "'");
            sbAdd.append(" data-item-id='" + splittedItemAdd[0] + "'");
            sbAdd.append(" data-item-text='" + itemAddText + "'");
            sbAdd.append(" data-item-uom='" + splittedItemAdd[3] + "'");
            sbAdd.append(" data-machinery-id='" + machineryAdd + "'");
            sbAdd.append(" data-machinery-text='" + machineryAddText + "'");
            sbAdd.append(" data-qty='" + qtyAdd + "'");
            sbAdd.append(" data-shift='" + shiftAdd + "'");
            sbAdd.append(" data-time='" + timeAdd + "'");
            sbAdd.append(" data-hm='" + hmAdd + "'");
            sbAdd.append(" data-km='" + kmAdd + "'");
            sbAdd.append(" data-fuelman='" + fuelmanAdd + "'");
            sbAdd.append(" data-operator='" + operatorAdd + "'");
            sbAdd.append(" data-remark='" + remarkAdd + "'");
            sbAdd.append("><span class='glyphicon glyphicon-edit'></span></a>");
            sbAdd.append("<a class='delete-modal btn btn-danger' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + itemAddText + "' data-machinery-id='" + machineryAdd + "' data-machinery-text='" + machineryAddText + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbAdd.append("</td>");
            sbAdd.append("</tr>");

            $('#detail_table').append(sbAdd.toString());

            // Reset add form modal
            $('#item_add').val(null).trigger('change');
            $('#item_add').text("");
            $('#machinery_add').val(null).trigger('change');
            $('#machinery_add').text("");
            $('#qty_add').val('');
            $('#shift_add').val('');
            $('#time_add').val('');
            $('#hm_add').val('');
            $('#km_add').val('');
            $('#fuelman_add').val('');
            $('#operator_add').val('');
            $('#remark_add').val('');
        });

        $("#addModal").on('hidden.bs.modal', function () {
            // Reset add form modal
            $('#item_add').val(null).trigger('change');
            $('#item_add').text("");
            $('#machinery_add').val(null).trigger('change');
            $('#machinery_add').text("");
            $('#qty_add').val('');
            $('#shift_add').val('');
            $('#time_add').val('');
            $('#hm_add').val('');
            $('#km_add').val('');
            $('#fuelman_add').val('');
            $('#operator_add').val('');
            $('#remark_add').val('');
        });

        // Edit detail
        $(document).on('click', '.edit-modal', function() {
            id = $(this).data('id');

            $('.modal-title').text('Ubah Detail');

            $('#item_old_value').val($(this).data('item-id'));
            $('#item_old_text').val($(this).data('item-text'));
            $('#item_old_uom').val($(this).data('item-uom'));
            $('#item_edit').select2({
                placeholder: {
                    id: $(this).data('item-id'),
                    text: $(this).data('item-text')
                },
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('select.extended_items') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term),
                            type: 'fuel'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#machinery_old_value').val($(this).data('machinery-id'));
            $('#machinery_old_text').val($(this).data('machinery-text'));
            $('#machinery_edit').select2({
                placeholder: {
                    id: $(this).data('machinery-id'),
                    text: $(this).data('machinery-text')
                },
                width: '100%',
                minimumInputLength: 1,
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

            qtyEditFormat.clear();
            qtyEditFormat.set($(this).data('qty'),{
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0
            });


            $('#shift_edit').val($(this).data('shift'));
            $('#time_edit').val($(this).data('time'));
            $('#hm_edit').val($(this).data('hm'));
            $('#km_edit').val($(this).data('km'));
            $('#fuelman_edit').val($(this).data('fuelman'));
            $('#operator_edit').val($(this).data('operator'));

            $('#remark_edit').val($(this).data('remark'));
            $('#editModal').modal('show');
        });

        $('.modal-footer').on('click', '.edit', function() {
            var itemEdit = $('#item_edit').val();
            var machineryEdit = $('#machinery_edit').val();
            var qtyEdit = $('#qty_edit').val();
            var remarkEdit = $('#remark_edit').val();
            var shiftEdit = $('#shift_edit').val();
            var timeEdit = $('#time_edit').val();
            var hmEdit = $('#hm_edit').val();
            var kmEdit = $('#km_edit').val();
            var fuelmanEdit = $('#fuelman_edit').val();
            var operatorEdit = $('#operator_edit').val();

            if(!shiftEdit || shiftEdit === "" ||
                !timeEdit || timeEdit === "" ||
                !hmEdit || hmEdit === "" ||
                !kmEdit || kmEdit === "" ||
                !fuelmanEdit || fuelmanEdit === "" ||
                !operatorEdit || operatorEdit === ""){
                alert('Field dengan tanda * wajib diisi!');
                return false;
            }

            shiftEdit = shiftEdit.toUpperCase();

            var machineryOldValue = $('#machinery_old_value').val();

            if(!machineryEdit || machineryEdit === ""){
                if(!machineryOldValue || machineryOldValue === ""){
                    alert('Mohon Pilih Alat Berat!');
                    return false;
                }
            }

            if(!qtyEdit || qtyEdit === "" || qtyEdit === "0"){
                alert('Mohon Isi Kuantitas!')
                return false;
            }

            // Get item properties
            var itemEditId = "0";
            var itemEditUom = "";
            var itemEditText = "default";
            if(itemEdit && itemEdit !== ''){
                var splittedItemEdit = itemEdit.split('#');
                itemEditId = splittedItemEdit[0];
                itemEditUom = splittedItemEdit[3];
                itemEditText = $('#item_edit').text();
            }
            else {
                itemEditId = $('#item_old_value').val();
                itemEditText = $('#item_old_text').val();
                itemEditUom = $('#item_old_uom').val();
            }

            // Get machinery properties
            var machineryEditId = "0";
            var machineryEditText = "default";
            if(machineryEdit && machineryEdit !== ''){
                machineryEditId = machineryEdit;
                machineryEditText = $('#machinery_edit').text();
            }
            else {
                machineryEditId = machineryOldValue;
                machineryEditText = $('#machinery_old_text').val();
            }

            var sbEdit = new stringbuilder();

            sbEdit.append("<tr class='item" + id + "'>");
            sbEdit.append("<td class='text-center'>" + itemEditText);
            sbEdit.append("<input type='hidden' name='item[]' value='" + itemEditId + "'/></td>");
            sbEdit.append("<td class='text-center'>" + machineryEditText);
            sbEdit.append("<input type='hidden' name='machinery[]' value='" + machineryEditId + "'/></td>");

            if(qtyEdit && qtyEdit !== ""){
                sbEdit.append("<td class='text-right'>" + qtyEdit + "<input type='hidden' name='qty[]' value='" + qtyEdit + "'/></td>");
            }
            else{
                sbEdit.append("<td class='text-right'><input type='hidden' name='qty[]'/></td>");
            }

            sbEdit.append("<td class='text-center'>" + itemEditUom + "</td>");
            sbEdit.append("<td class='text-center'>" + shiftEdit);
            sbEdit.append("<input type='hidden' name='shift[]' value='" + shiftEdit + "'/></td>");
            sbEdit.append("<td class='text-center'>" + timeEdit);
            sbEdit.append("<input type='hidden' name='time[]' value='" + timeEdit + "'/></td>");
            sbEdit.append("<td class='text-center'>" + hmEdit);
            sbEdit.append("<input type='hidden' name='hm[]' value='" + hmEdit + "'/></td>");
            sbEdit.append("<td class='text-center'>" + kmEdit);
            sbEdit.append("<input type='hidden' name='km[]' value='" + kmEdit + "'/></td>");
            sbEdit.append("<td class='text-center'>" + fuelmanEdit);
            sbEdit.append("<input type='hidden' name='fuelman[]' value='" + fuelmanEdit + "'/></td>");
            sbEdit.append("<td class='text-center'>" + operatorEdit);
            sbEdit.append("<input type='hidden' name='operator[]' value='" + operatorEdit + "'/></td>");

            sbEdit.append("<td>" + remarkEdit + "<input type='hidden' name='remark[]' value='" + remarkEdit + "'/></td>");

            sbEdit.append("<td class='text-center'>");
            sbEdit.append("<a class='edit-modal btn btn-info' data-id='" + id + "'");
            sbEdit.append(" data-item-id='" + itemEditId + "'");
            sbEdit.append(" data-item-text='" + itemEditText + "'");
            sbEdit.append(" data-item-uom='" + itemEditUom + "'");
            sbEdit.append(" data-machinery-id='" + machineryEditId + "'");
            sbEdit.append(" data-machinery-text='" + machineryEditText + "'");
            sbEdit.append(" data-qty='" + qtyEdit + "'");
            sbEdit.append(" data-shift='" + shiftEdit + "'");
            sbEdit.append(" data-time='" + timeEdit + "'");
            sbEdit.append(" data-hm='" + hmEdit + "'");
            sbEdit.append(" data-km='" + kmEdit + "'");
            sbEdit.append(" data-fuelman='" + fuelmanEdit + "'");
            sbEdit.append(" data-operator='" + operatorEdit + "'");
            sbEdit.append(" data-remark='" + remarkEdit + "'");
            sbEdit.append("><span class='glyphicon glyphicon-edit'></span></a>");
            sbEdit.append("<a class='delete-modal btn btn-danger' data-id='" + id + "' data-item-id='" + itemEditId + "' data-item-text='" + itemEditText + "' data-machinery-id='" + machineryEditId + "' data-machinery-text='" + machineryEditText + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbEdit.append("</td>");
            sbEdit.append("</tr>");

            $('.item' + id).replaceWith(sbEdit.toString());

            // Reset edit form modal
            $('#item_edit').val(null).trigger('change');
            $('#item_edit').text("");
            $('#item_old_value').val('');
            $('#item_old_text').val('');
            $('#item_old_uom').val('');
            $('#machinery_edit').val(null).trigger('change');
            $('#machinery_edit').text("");
            $('#machinery_old_value').val('');
            $('#machinery_old_text').val('');
            $('#qty_edit').val('');
            $('#remark_edit').val('');
        });

        $("#editModal").on('hidden.bs.modal', function () {
            // Reset edit form modal
            $('#item_edit').val(null).trigger('change');
            $('#item_edit').text("");
            $('#item_old_value').val('');
            $('#item_old_text').val('');
            $('#item_old_uom').val('');
            $('#machinery_edit').val(null).trigger('change');
            $('#machinery_edit').text("");
            $('#machinery_old_value').val('');
            $('#machinery_old_text').val('');
            $('#qty_edit').val('');
            $('#shift_edit').val('');
            $('#time_edit').val('');
            $('#hm_edit').val('');
            $('#km_edit').val('');
            $('#fuelman_edit').val('');
            $('#operator_edit').val('');
            $('#remark_edot').val('');
        });

        // Delete detail
        var deletedId = "0";
        $(document).on('click', '.delete-modal', function() {
            $('.modal-title').text('Hapus Detail');
            deletedId = $(this).data('id');
            $('#item_delete').val($(this).data('item-text'));
            $('#machinery_delete').val($(this).data('machinery-text'));
            $('#qty_delete').val($(this).data('qty'));
            $('#remark_delete').val($(this).data('remark'));
            $('#deleteModal').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function() {
            $('.item' + deletedId).remove();
        });
    </script>
@endsection