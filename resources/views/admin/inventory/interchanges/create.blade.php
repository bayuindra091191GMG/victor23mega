@extends('admin.layouts.admin')

@section('title','Tambah Interchanges')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.interchanges.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    </label>
                    <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                        @include('partials._success')
                    </div>
                </div>
            @endif

            @if(count($errors))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    </label>
                    <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12 alert alert-danger alert-dismissible fade in" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="item_previous">
                    Inventory Sebelumnya
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="item_previous" name="item_previous" class="form-control col-md-7 col-xs-12 @if($errors->has('item_id_before')) parsley-error @endif">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code" >
                    Kode Interchange
                    <span class="required">*</span>
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ old('code') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                    Nama Interchange
                    <span class="required">*</span>
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                           name="name" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="part_number">
                    Part Number Interchange
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="part_number" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('part_number')) parsley-error @endif"
                           name="part_number" value="{{ old('part_number') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="group" >
                    Kategori Inventory
                    <span class="required">*</span>
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <select id="group" name="group" class="form-control col-md-7 col-xs-12 @if($errors->has('group')) parsley-error @endif">
                        <option value="-1" @if(empty(old('group'))) selected @endif> - Pilih kategori inventory - </option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group') == $group->id ? "selected":"" }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="uom" >
                    UOM
                    <span class="required">*</span>
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="uom" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('uom')) parsley-error @endif"
                           name="uom" value="{{ old('uom') }}" required>
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery_type" >
                    Tipe Alat Berat
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="machinery_type" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery_type')) parsley-error @endif"
                           name="machinery_type" value="{{ old('machinery_type') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="valuation" >
                    Nilai Beli per UOM
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="valuation" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('valuation')) parsley-error @endif"
                           name="valuation">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description" >
                    Keterangan Tambahan
                </label>
                <div class="col-lg-6 col-md-9 col-sm-6 col-xs-12">
                    <textarea id="description" name="description" rows="5" class="form-control col-md-7 col-xs-12 @if($errors->has('description')) parsley-error @endif" style="resize: vertical">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Gudang dan Stok</label>
                <div class="col-lg-6 col-md-9 col-xs-12 column">
                    <table class="table table-bordered table-hover" id="table_item">
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
                            <th class="text-center" style="width: 15%">
                                Minimum Stok
                            </th>
                            <th class="text-center" style="width: 15%">
                                Maksimum Stok
                            </th>
                            <th class="text-center" style="width: 15%">
                                Notifikasi Stok
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php( $idx = 0 )
                        @if($warehouses->count() > 0)
                            @foreach($warehouses as $warehouse)
                                <tr id='item{{ $idx }}'>
                                    <td>
                                        <select id="warehouse{{ $idx }}" name="warehouse[]" class='form-control'>
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type='text' name='location[]'  placeholder='Lokasi' class='form-control'/>
                                    </td>
                                    <td>
                                        <input type='text' id='qty{{ $idx }}' name='qty[]'  placeholder='Stok On Hand' class='form-control text-right'/>
                                    </td>
                                    <td>
                                        <input type='text' id='min{{ $idx }}' name='min[]'  placeholder='Minimum Stok' class='form-control text-right'/>
                                    </td>
                                    <td>
                                        <input type='text' id='max{{ $idx }}' name='max[]'  placeholder='Maksimum Stok' class='form-control text-right'/>
                                    </td>
                                    <td class='text-center'>
                                        <input type='checkbox' class='flat' id='check{{ $idx }}' />
                                        <input type='hidden' id='warning{{ $idx }}' name='warning[]' value='0'/>
                                    </td>
                                </tr>
                                @php( $idx++ )
                            @endforeach
                            <tr id='item{{ $idx }}'></tr>
                        @else
                            <tr id='item0'>
                                <td>
                                    <select id="warehouse0" name="warehouse[]" class='form-control'></select>
                                </td>
                                <td>
                                    <input type='text' name='location[]'  placeholder='Lokasi' class='form-control'/>
                                </td>
                                <td>
                                    <input type='text' id='qty0' name='qty[]'  placeholder='Stok On Hand' class='form-control text-right'/>
                                </td>
                                <td>
                                    <input type='text' id='min0' name='min[]'  placeholder='Minimum Stok' class='form-control text-right'/>
                                </td>
                                <td>
                                    <input type='text' id='max0' name='max[]'  placeholder='Maksimum Stok' class='form-control text-right'/>
                                </td>
                                <td class='text-center'>
                                    <input type='checkbox' class='flat' id='check0' />
                                    <input type='hidden' id='warning0' name='warning[]' value='0'/>
                                </td>
                            </tr>
                        </tbody>
                        <tr id='item1'></tr>
                        @endif

                    </table>
                    <a id="add_row" class="btn btn-default pull-left" style="margin-bottom: 10px;">Tambah</a><a id='delete_row' class="pull-right btn btn-default">Hapus</a>
                </div>
            </div>

            <input type="hidden" id="is_repeat" name="is_repeat" value="0"/>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-warning" href="{{ route('admin.interchanges') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                    <a class="btn btn-success repeat-submit"> Simpan dan Tambah Lagi</a>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    <script type="text/javascript">

        // Save and add again
        $(document).on('click', '.repeat-submit', function() {
            $('#is_repeat').val('1');
            $('#general-form').submit();
        });

        // autoNumeric
        valuationFormat = new AutoNumeric('#valuation', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty(old('valuation')))
            var oldValue = '{{ old('valuation') }}';
            var valueClean = oldValue.replace(/\./g,'');
            valuationFormat.clear();
            valuationFormat.set(valueClean, {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        $('#item_previous').select2({
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

        @php( $idxScript = 0 )
        @if( $warehouses->count() > 0 )

        @foreach($warehouses as $warehouse)
            qtyFormat{{ $idxScript }} = new AutoNumeric('#qty{{ $idxScript }}', {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            minStockFormat{{ $idxScript }} = new AutoNumeric('#min{{ $idxScript }}', {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            maxStockFormat{{ $idxScript }} = new AutoNumeric('#max{{ $idxScript }}', {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            // Select warehouses
            $('#warehouse{{ $idxScript }}').select2({
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

            $('#check{{ $idxScript }}').on('change',function(){
                var _val = $(this).is(':checked') ? 'true' : 'false';
                if(_val === 'true'){
                    $('#warning{{ $idxScript }}').val('1');
                }
                else{
                    $('#warning{{ $idxScript }}').val('0');
                }
            });
            @php( $idxScript++ )
        @endforeach
        @else
            qtyFormat = new AutoNumeric('#qty0', {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            minStockFormat = new AutoNumeric('#min0', {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            maxStockFormat = new AutoNumeric('#max0', {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
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

            $('#check0').on('change',function(){
                var _val = $(this).is(':checked') ? 'true' : 'false';
                if(_val === 'true'){
                    $('#warning0').val('1');
                }
                else{
                    $('#warning0').val('0');
                }
            });
        @endif

        var i = parseInt('{{ $idx }}');
        $("#add_row").click(function(){
            $('#item' + i).html("<td>" +
                "<select id='warehouse" + i + "' name='warehouse[]' class='form-control'></select></td>" +
                "<td><input type='text' name='location[]'  placeholder='Lokasi' class='form-control'/></td>" +
                "<td><input type='text' id='qty" + i + "' name='qty[]'  placeholder='Stok On Hand' class='form-control text-right'/></td>" +
                "<td><input type='text' id='min" + i + "' name='min[]'  placeholder='Minimum Stok' class='form-control text-right'/></td>" +
                "<td><input type='text' id='max" + i + "' name='max[]'  placeholder='Maksimum Stok' class='form-control text-right'/></td>" +
                "<td class='text-center'><input type='checkbox' class='flat' id='check" + i + "' /><input type='hidden' id='warning" + i + "' name='warning[]' value='0'/></td>"
            );

            $('#table_item').append("<tr id='item" + (i+1) + "'></tr>");

            new AutoNumeric('#qty' + i, {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            new AutoNumeric('#min' + i, {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            new AutoNumeric('#max' + i, {
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                emptyInputBehavior: "zero",
                modifyValueOnWheel: false
            });

            $('#warehouse' + i).select2({
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

            $('#check' + i).on('change', function(){
                var _val = $(this).is(':checked') ? 'true' : 'false';
                if(_val === 'true'){
                    $('#warning' + (i-1)).val('1');
                }
                else{
                    $('#warning' + (i-1)).val('0');
                }
            });

            i++;
        });

        $("#delete_row").click(function(){
            if(i>1){
                $("#item"+(i-1)).html('');
                i--;
            }
        });
    </script>
@endsection