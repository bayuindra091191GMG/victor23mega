@extends('admin.layouts.admin')

@section('title','Buat Issued Docket BBM Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left">
                <div class="form-group" style="margin-bottom: 1.5em;">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                        Unduh File EXCEL
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <a href="{{ route('admin.issued_dockets.excel.download') }}" class="btn btn-info">Unduh File Excel</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                        Unggah File EXCEL untuk input detil inventory
                    </label>
                    <div class="col-md-2 col-sm-2 col-xs-12">
                        <input type="file" id="excel" name="excel" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                    </div>
                </div>
            </form>
            <hr style="border-top: 1px solid #ccc;"/>
        </div>
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
                            <option value="{{ $department->id }}" @if(!empty(old('department')) && old('department') == $department->id) selected @endif>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

{{--            <div class="form-group">--}}
{{--                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account">--}}
{{--                    Cost Code--}}
{{--                    <span class="required">*</span>--}}
{{--                </label>--}}
{{--                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                    <select id="account" name="account" class="form-control col-md-7 col-xs-12 @if($errors->has('account')) parsley-error @endif">--}}
{{--                        @if(!empty(old('account')))--}}
{{--                            <option value="{{ old('account') }}" selected>{{ old('account_text') }}</option>--}}
{{--                        @endif--}}
{{--                    </select>--}}
{{--                    <input type="hidden" id="account_text" name="account_text" value="{{ old('account_text') }}"/>--}}
{{--                </div>--}}
{{--            </div>--}}

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
                            <option value="{{ $warehouse->id }}" @if(!empty(old('warehouse')) && old('warehouse') == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
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

            <hr style="border-top: 1px solid #ccc;"/>

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
                                    Cost Code
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Unit Alat Berat
                                </th>
                                <th class="text-center" style="width: 5%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 5%">
                                    UOM
                                </th>
                                <th class="text-center" style="width: 5%">
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
                            @if(!empty(old('item')))
                                @php( $oldItemCodes = old('item_codes') )
                                @php( $oldItemTextes = old('item_textes') )
                                @php( $oldMachineries = old('machinery') )
                                @php( $oldMachineryTextes = old('machinery_textes') )
                                @php( $oldAccounts = old('accounts') )
                                @php( $oldAccountTextes = old('account_textes') )
                                @php( $oldQtys = old('qty') )
                                @php( $oldUoms = old('uoms') )
                                @php( $oldShifts = old('shift') )
                                @php( $oldTimes = old('time') )
                                @php( $oldHms = old('hm') )
                                @php( $oldKms = old('km') )
                                @php( $oldFuelmans = old('fuelman') )
                                @php( $oldOperators = old('operator') )
                                @php( $oldRemarks = old('remark') )
                                @foreach(old('item') as $item)
                                        <tr class='item{{ $idx }}'>
                                            <td class='text-center'>

                                                @if($item == '-1')
                                                    <span style="color: red;">DATA TIDAK DITEMUKAN!</span>
                                                @else
                                                    {{ $oldItemTextes[$idx] }}
                                                @endif

                                                <input type='hidden' name='item[]' value='{{ $item }}'/>
                                                <input type='hidden' name='item_textes[]' value='{{ $oldItemTextes[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>

                                                @if($oldAccounts[$idx] == '-1')
                                                    <span style="color: red;">DATA TIDAK DITEMUKAN!</span>
                                                @else
                                                    {{ $oldAccountTextes[$idx] }}
                                                @endif

                                                <input type='hidden' name='accounts[]' value='{{ $oldAccounts[$idx] }}'/>
                                                <input type='hidden' name='account_textes[]' value='{{ $oldAccountTextes[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>

                                                @if($oldMachineries[$idx] == '-1')
                                                    <span style="color: red;">DATA TIDAK DITEMUKAN!</span>
                                                @else
                                                    {{ $oldMachineryTextes[$idx] }}
                                                @endif

                                                <input type='hidden' name='machinery[]' value='{{ $oldMachineries[$idx] }}'/>
                                                <input type='hidden' name='machinery_textes[]' value='{{ $oldMachineryTextes[$idx] }}'/>
                                            </td>
                                            <td class='text-right'>
                                                {{ $oldQtys[$idx] }}
                                                <input type='hidden' name='qty[]'  value="{{ $oldQtys[$idx] }}"/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldUoms[$idx] }}
                                                <input type='hidden' name='uoms[]'  value="{{ $oldUoms[$idx] }}"/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldShifts[$idx] }}
                                                <input type='hidden' name='shift[]' value='{{ $oldShifts[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldTimes[$idx]  }}
                                                <input type='hidden' name='time[]' value='{{ $oldTimes[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldHms[$idx] }}
                                                <input type='hidden' name='hm[]' value='{{ $oldHms[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldKms[$idx] }}
                                                <input type='hidden' name='km[]' value='{{ $oldKms[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldFuelmans[$idx] }}
                                                <input type='hidden' name='fuelman[]' value='{{ $oldFuelmans[$idx] }}'/>
                                            </td>
                                            <td class='text-center'>
                                                {{ $oldOperators[$idx] }}
                                                <input type='hidden' name='operator[]' value='{{ $oldOperators[$idx] }}'/>
                                            </td>
                                            <td>
                                                {{ $oldRemarks[$idx] }}
                                                <input type='hidden' name='remark[]' value="{{ $oldRemarks[$idx] }}"/>
                                            </td>
                                            <td class="text-center">
                                                <a class="edit-modal btn btn-info"
                                                   data-id="{{ $idx }}"
                                                   data-item-id="{{ $item }}"
                                                   data-item-text="{{ $oldItemTextes[$idx] }}"
                                                   data-item-uom="{{ $oldUoms[$idx] }}"
                                                   data-account-id="{{ $oldAccounts[$idx] }}"
                                                   data-account-text="{{ $oldAccountTextes[$idx] }}"
                                                   data-machinery-id="{{ $oldMachineries[$idx] }}"
                                                   data-machinery-text="{{ $oldMachineryTextes[$idx] }}"
                                                   data-qty="{{ $oldQtys[$idx] }}"
                                                   data-shift="{{ $oldShifts[$idx] }}"
                                                   data-time="{{ $oldTimes[$idx] }}"
                                                   data-hm="{{ $oldHms[$idx] }}"
                                                   data-km="{{ $oldKms[$idx] }}"
                                                   data-fuelman="{{ $oldFuelmans[$idx] }}"
                                                   data-operator="{{ $oldOperators[$idx] }}"
                                                   data-remark="{{ $oldRemarks[$idx] }}">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                                <a class="delete-modal btn btn-danger" data-id="{{ $idx }}"
                                                   data-item-id="{{ $item }}"
                                                   data-item-text="{{ $oldItemTextes[$idx]  }}"
                                                   data-machinery-id="{{ $oldMachineries[$idx] }}"
                                                   data-machinery-text="{{ $oldMachineryTextes[$idx] }}"
                                                   data-qty="{{ $oldQtys[$idx] }}">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </a>
                                            </td>
                                        </tr>
                                        @php($idx++)
                                @endforeach
                            @endif
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
        <div class="modal-dialog modal-lg">
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
                            <label class="control-label col-sm-2" for="account_add">Cost Code *:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="account_add" name="account_add"></select>
                                <p class="errorItem text-center alert alert-danger hidden"></p>
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
        <div class="modal-dialog modal-lg">
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
                            <label class="control-label col-sm-2" for="account_edit">Cost Code *:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="account_edit" name="account_edit"></select>
                                <p class="errorItem text-center alert alert-danger hidden"></p>
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

        {{--$('#account').select2({--}}
        {{--    placeholder: {--}}
        {{--        id: '-1',--}}
        {{--        text: ' - Pilih Nomor Cost Code - '--}}
        {{--    },--}}
        {{--    width: '100%',--}}
        {{--    minimumInputLength: 0,--}}
        {{--    allowClear: true,--}}
        {{--    ajax: {--}}
        {{--        url: '{{ route('select.accounts') }}',--}}
        {{--        dataType: 'json',--}}
        {{--        data: function (params) {--}}
        {{--            return {--}}
        {{--                q: $.trim(params.term)--}}
        {{--            };--}}
        {{--        },--}}
        {{--        processResults: function (data) {--}}
        {{--            return {--}}
        {{--                results: data--}}
        {{--            };--}}
        {{--        }--}}
        {{--    }--}}
        {{--});--}}

        {{--$('#account').on('select2:select', function (e) {--}}
        {{--    var data = e.params.data;--}}
        {{--    $('#account_text').val(data.text);--}}
        {{--});--}}

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

            $('#account_add').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Cost Code - '
                },
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('select.accounts.name') }}',
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
                minimumInputLength: 0,
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
            let accountAdd = $('#account_add').val();

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

            if(!accountAdd || accountAdd === ""){
                alert('Mohon Pilih Cost Code!');
                return false;
            }

            let splittedAccountAdd = accountAdd.split('#');
            let accountAddText = $('#account_add').text();

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
            sbAdd.append("<input type='hidden' name='item[]' value='" + splittedItemAdd[0] + "'/>");
            sbAdd.append("<input type='hidden' name='item_textes[]' value='" + itemAddText + "'/></td>");
            sbAdd.append("<td class='text-center'>" + accountAddText);
            sbAdd.append("<input type='hidden' name='accounts[]' value='" + splittedAccountAdd[0] + "'/>");
            sbAdd.append("<input type='hidden' name='account_textes[]' value='" + accountAddText + "'/></td>");
            sbAdd.append("<td class='text-center'>" + machineryAddText);
            sbAdd.append("<input type='hidden' name='machinery[]' value='" + machineryAdd + "'/>");
            sbAdd.append("<input type='hidden' name='machinery_textes[]' value='" + machineryAddText + "'/></td>");

            if(qtyAdd && qtyAdd !== ""){
                sbAdd.append("<td class='text-right'>" + qtyAdd + "<input type='hidden' name='qty[]' value='" + qtyAdd + "'/></td>");
            }
            else{
                sbAdd.append("<td class='text-right'><input type='hidden' name='qty[]'/></td>");
            }

            sbAdd.append("<td class='text-center'>" + splittedItemAdd[3]);
            sbAdd.append("<input type='hidden' name='uoms[]' value='" + splittedItemAdd[3] + "'/></td>");
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
            sbAdd.append(" data-account-id='" + splittedAccountAdd[0] + "'");
            sbAdd.append(" data-account-text='" + accountAddText + "'");
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
            $('#account_add').val(null).trigger('change');
            $('#account_add').text("");
            $('#machinery_add').val(null).trigger('change');
            $('#machinery_add').text("");
            qtyAddFormat.clear();
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
            $('#account_add').val(null).trigger('change');
            $('#account_add').text("");
            $('#machinery_add').val(null).trigger('change');
            $('#machinery_add').text("");
            qtyAddFormat.clear();
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

            let accountEdit = $(this).data('account-id');
            let accountTextEdit = $(this).data('account-text');
            $('#account_edit').append('<option value="' + accountEdit + '#' + accountTextEdit + '" selected>' + accountTextEdit + '</option>');
            $('#account_edit').select2({
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('select.accounts.name') }}',
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
                minimumInputLength: 0,
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
            let accountEdit = $('#account_edit').val();

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

            if(!accountEdit || accountEdit === ""){
                alert('Mohon Pilih Cost Code!');
                return false;
            }

            let splittedAccountEdit = accountEdit.split('#');
            let accountEditText = $('#account_edit').text();

            var machineryOldValue = $('#machinery_old_value').val();

            if(!machineryEdit || machineryEdit === ""){
                if(!machineryOldValue || machineryOldValue === "" || machineryOldValue === "-1"){
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

            if(itemEditId === "-1"){
                alert('Mohon pilih inventory!');
                return false;
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
            sbEdit.append("<td class='text-center'>" + accountEditText);
            sbEdit.append("<input type='hidden' name='accounts[]' value='" + splittedAccountEdit[0] + "'/>");
            sbEdit.append("<input type='hidden' name='account_textes[]' value='" + accountEditText + "'/></td>");
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
            sbEdit.append(" data-account-id='" + splittedAccountEdit[0] + "'");
            sbEdit.append(" data-account-text='" + accountEditText + "'");
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
            $('#account_edit').val(null).trigger('change');
            $('#account_edit').text("");
            $('#machinery_edit').val(null).trigger('change');
            $('#machinery_edit').text("");
            $('#machinery_old_value').val('');
            $('#machinery_old_text').val('');
            qtyEditFormat.clear();
            $('#remark_edit').val('');
        });

        $("#editModal").on('hidden.bs.modal', function () {
            // Reset edit form modal
            $('#item_edit').val(null).trigger('change');
            $('#item_edit').text("");
            $('#item_old_value').val('');
            $('#item_old_text').val('');
            $('#item_old_uom').val('');
            $('#account_edit').val(null).trigger('change');
            $('#account_edit').text("");
            $('#machinery_edit').val(null).trigger('change');
            $('#machinery_edit').text("");
            $('#machinery_old_value').val('');
            $('#machinery_old_text').val('');
            qtyEditFormat.clear();
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

        $('#excel').change(function () {
            if ($(this).val() != '') {
                uploadExcel(this);
            }
        });

        function uploadExcel(file){
            var form_data = new FormData();
            form_data.append('excel', file.files[0]);
            form_data.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: "{{ route('admin.issued_dockets.excel.upload') }}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.fail) {
                        alert('FILE EXCEL YANG DIUNGGAH BERBEDA FORMAT!');
                        document.getElementById('excel').value= null;
                    }
                    else {
                        for (let j = 0; j < data.length; j++) {
                            // Increase idx
                            let idx = $('#index_counter').val();
                            idx++;
                            $('#index_counter').val(idx);

                            let sbImport = new stringbuilder();

                            sbImport.append("<tr class='item" + idx + "'>");

                            if(data[j].item_id === -1){
                                sbImport.append("<td class='text-center'><span style='color: red;'>DATA TIDAK DITEMUKAN!</span>");
                            }
                            else{
                                sbImport.append("<td class='text-center'>" + data[j].item_text);
                            }

                            sbImport.append("<input type='hidden' name='item[]' value='" + data[j].item_id + "'/>");
                            sbImport.append("<input type='hidden' name='item_textes[]' value='" + data[j].item_text + "'/></td>");

                            if(data[j].account_id === -1){
                                sbImport.append("<td class='text-center'><span style='color: red;'>DATA TIDAK DITEMUKAN!</span>");
                            }
                            else{
                                sbImport.append("<td class='text-center'>" + data[j].account_text);
                            }

                            sbImport.append("<input type='hidden' name='accounts[]' value='" + data[j].account_id + "'/>");
                            sbImport.append("<input type='hidden' name='account_textes[]' value='" + data[j].account_text + "'/></td>");

                            if(data[j].machinery_id === -1){
                                sbImport.append("<td class='text-center'><span style='color: red;'>DATA TIDAK DITEMUKAN!</span>");
                            }
                            else{
                                sbImport.append("<td class='text-center'>" + data[j].machinery_text);
                            }

                            sbImport.append("<input type='hidden' name='machinery[]' value='" + data[j].machinery_id + "'/>");
                            sbImport.append("<input type='hidden' name='machinery_textes[]' value='" + data[j].machinery_text + "'/></td>");

                            sbImport.append("<td class='text-right'>" + data[j].qty + "<input type='hidden' name='qty[]' value='" + data[j].qty + "'/></td>");

                            sbImport.append("<td class='text-center'>" + data[j].uom);
                            sbImport.append("<input type='hidden' name='uoms[]' value='" + data[j].uom + "'/></td>");
                            sbImport.append("<td class='text-center'>" + data[j].shift);
                            sbImport.append("<input type='hidden' name='shift[]' value='" + data[j].shift + "'/></td>");
                            sbImport.append("<td class='text-center'>" + data[j].time);
                            sbImport.append("<input type='hidden' name='time[]' value='" + data[j].time + "'/></td>");
                            sbImport.append("<td class='text-center'>" + data[j].hm);
                            sbImport.append("<input type='hidden' name='hm[]' value='" + data[j].hm + "'/></td>");
                            sbImport.append("<td class='text-center'>" + data[j].km);
                            sbImport.append("<input type='hidden' name='km[]' value='" + data[j].km + "'/></td>");
                            sbImport.append("<td class='text-center'>" + data[j].fuelman);
                            sbImport.append("<input type='hidden' name='fuelman[]' value='" + data[j].fuelman + "'/></td>");
                            sbImport.append("<td class='text-center'>" + data[j].operator);
                            sbImport.append("<input type='hidden' name='operator[]' value='" + data[j].operator + "'/></td>");
                            sbImport.append("<td>" + data[j].remark + "<input type='hidden' name='remark[]' value='" + data[j].remark  + "'/></td>");

                            sbImport.append("<td class='text-center'>");
                            sbImport.append("<a class='edit-modal btn btn-info' data-id='" + idx + "'");
                            sbImport.append(" data-item-id='" + data[j].item_id + "'");
                            sbImport.append(" data-item-text='" + data[j].item_text + "'");
                            sbImport.append(" data-item-uom='" + data[j].uom + "'");
                            sbImport.append(" data-account-id='" + data[j].account_id + "'");
                            sbImport.append(" data-account-text='" + data[j].account_text + "'");
                            sbImport.append(" data-machinery-id='" + data[j].machinery_id + "'");
                            sbImport.append(" data-machinery-text='" + data[j].machinery_text + "'");
                            sbImport.append(" data-qty='" + data[j].qty + "'");
                            sbImport.append(" data-shift='" + data[j].shift + "'");
                            sbImport.append(" data-time='" + data[j].time + "'");
                            sbImport.append(" data-hm='" + data[j].hm + "'");
                            sbImport.append(" data-km='" + data[j].km + "'");
                            sbImport.append(" data-fuelman='" + data[j].fuelman + "'");
                            sbImport.append(" data-operator='" + data[j].operator + "'");
                            sbImport.append(" data-remark='" + data[j].remark + "'");
                            sbImport.append("><span class='glyphicon glyphicon-edit'></span></a>");
                            sbImport.append("<a class='delete-modal btn btn-danger' data-id='" + idx + "' " +
                                "data-item-id='" + data[j].item_id + "' " +
                                "data-item-text='" + data[j].item_text + "' " +
                                "data-machinery-id='" + data[j].machinery_id + "' " +
                                "data-machinery-text='" + data[j].machinery_text + "' " +
                                "data-qty='" + data[j].qty + "' " +
                                "data-remark='" + data[j].remark + "'><span class='glyphicon glyphicon-trash'></span></a>");
                            sbImport.append("</td>");
                            sbImport.append("</tr>");

                            $('#detail_table').append(sbImport.toString());
                        }

                        document.getElementById('excel').value= null;
                    }
                },
                error: function (xhr, status, error) {
                    alert("INTERNAL SERVER ERROR");
                    document.getElementById('excel').value= null;
                }
            });
        }
    </script>
@endsection