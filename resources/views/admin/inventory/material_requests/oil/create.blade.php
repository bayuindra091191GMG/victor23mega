@extends('admin.layouts.admin')

@section('title','Buat Material Request Oli')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.material_requests.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left', 'enctype' => 'multipart/form-data', 'novalidate']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mr_code">
                    Nomor MR
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="mr_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('mr_code')) parsley-error @endif"
                           name="mr_code" value="{{ $autoNumber }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_number"></label>
                <div class="col-md-4 col-sm-4 col-xs-12">
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
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ old('date') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department" >
                    Departemen
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="department" name="department" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif">
                        <option value="-1" @if(empty(old('department'))) selected @endif> - Pilih Departemen - </option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department') == $department->id ? "selected":"" }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="priority">
                    Prioritas
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="priority" name="priority" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif">
                        <option value="-1" @if(empty(old('priority'))) selected @endif> - Pilih Prioritas - </option>
                        <option value="Part - P1" {{ old('priority') == "Part - P1" ? "selected":"" }}>Part - P1</option>
                        <option value="Part - P2" {{ old('priority') == "Part - P2" ? "selected":"" }}>Part - P2</option>
                        <option value="Part - P3" {{ old('priority') == "Part - P3" ? "selected":"" }}>Part - P3</option>
                        <option value="Non-Part - P1" {{ old('priority') == "Non-Part - P1" ? "selected":"" }}>Non-Part - P1</option>
                        <option value="Non-Part - P2" {{ old('priority') == "Non-Part - P2" ? "selected":"" }}>Non-Part - P2</option>
                        <option value="Non-Part - P3" {{ old('priority') == "Non-Part - P3" ? "selected":"" }}>Non-Part - P3</option>
                    </select>
                </div>
            </div>

{{--            <div class="form-group">--}}
{{--                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="warehouse" >--}}
{{--                    Gudang--}}
{{--                    <span class="required">*</span>--}}
{{--                </label>--}}
{{--                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                    <select id="warehouse" name="warehouse" class="form-control col-md-7 col-xs-12 @if($errors->has('warehouse')) parsley-error @endif">--}}
{{--                        @foreach($warehouses as $warehouse)--}}
{{--                            <option value="{{ $warehouse->id }}" {{ $userWarehouseId == $warehouse->id ? "selected":"" }}>{{ $warehouse->name }}</option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery" >
                    Unit Alat Berat
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="machinery" name="machinery" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery')) parsley-error @endif">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="engine_model">
                    Engine Model
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="engine_model" type="text" class="form-control col-md-7 col-xs-12"
                           name="engine_model" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sn_chasis">
                    SN Chasis
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sn_chasis" type="text" class="form-control col-md-7 col-xs-12"
                           name="sn_chasis" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sn_engine">
                    SN Engine
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sn_engine" type="text" class="form-control col-md-7 col-xs-12"
                           name="sn_engine" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="km">
                    KM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="km" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('km')) parsley-error @endif"
                           name="km" value="{{ old('km') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="hm">
                    HM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="hm" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('hm')) parsley-error @endif"
                           name="hm" value="{{ old('hm') }}">
                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery" >--}}
                    {{--Penggunaan MR--}}
                    {{--<span class="required">*</span>--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<label class="radio-inline"><input type="radio" name="purpose" value="stock" checked>Stock</label>--}}
                    {{--<label class="radio-inline"><input type="radio" name="purpose" value="non-stock">Non-Stock</label>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="requested_by">
                    Request Oleh
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="requested_by" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('requested_by')) parsley-error @endif"
                           name="requested_by" value="{{ old('requested_by') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="document">
                    Lampiran Berita Acara
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('document', array('id' => 'document', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-lg12 col-md-12 col-xs-12 box-section">
                    <h3 class="text-center">Detil Inventory</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tab_logic">
                            <thead>
                            <tr >
                                <th class="text-center" style="width: 40%">
                                    Nomor Part
                                </th>
                                <th class="text-center" style="width: 20%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 40%">
                                    Remark
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr id='addr0'>
                                <td class='field-item'>
                                    <select id="select0" name="item[]" class='form-control'></select>
                                </td>
                                <td>
                                    <input id="qty0" type='text' name='qty[]'  placeholder='QTY' class='form-control'/>
                                </td>
                                <td>
                                    <input type='text' name='remark[]' placeholder='Keterangan' class='form-control'/>
                                </td>
                            </tr>
                            <tr id='addr1'></tr>
                            </tbody>
                        </table>
                    </div>
                    <a id="add_row" class="btn btn-default pull-left">Tambah</a><a id='delete_row' class="pull-right btn btn-default">Hapus</a>
                </div>
            </div>

            <hr/>

            <input type="hidden" name="type" id="type" value="3"/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.material_requests.other') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
    {{ Html::style(mix('assets/admin/css/fileinput.css')) }}
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
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    {{ Html::script(mix('assets/admin/js/fileinput.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // FILEINPUT
        $("#document").fileinput({
            allowedFileExtensions: ["pdf"],
            showUpload: false,
            dropZoneTitle: "UNGGAH PDF DI SINI"
        });

        // Auto Numbering
        $('#auto_number').change(function(){
            if(this.checked){
                $('#mr_code').val('{{ $autoNumber }}');
                $('#mr_code').prop('readonly', true);
            }
            else{
                $('#mr_code').val('');
                $('#mr_code').prop('readonly', false);
            }
        });

        // AutoNumeric
        qtyAddFormat = new AutoNumeric('#qty0', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        var i=1;

        $('#machinery').select2({
            placeholder: {
                id: '-1',
                text: ' - Pilih Alat Berat - '
            },
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route('select.extended_machineries') }}',
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
            var splittedMachinery = data.id.split('#');
            $('#engine_model').val(splittedMachinery[1]);
            $('#sn_chasis').val(splittedMachinery[2]);
            $('#sn_engine').val(splittedMachinery[3]);
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
                        q: $.trim(params.term),
                        type: 'oil'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        var i=1;
        $("#add_row").click(function(){
            $('#addr'+i).html("<td class='field-item'><select id='select" + i + "' name='item[]' class='form-control'></select></td><td><input type='text' id='qty" + i + "' name='qty[]'  placeholder='QTY' class='form-control'/></td><td><input type='text' name='remark[]' placeholder='Keterangan' class='form-control'/></td>");

            $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');

            $('#select' + i).select2({
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
                            q: $.trim(params.term),
                            type: 'oil'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            // AutoNumeric
            new AutoNumeric('#qty' + i, {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0
            });

            i++;
        });

        $("#delete_row").click(function(){
            if(i>1){
                $("#addr"+(i-1)).html('');
                i--;
            }
        });
    </script>
@endsection