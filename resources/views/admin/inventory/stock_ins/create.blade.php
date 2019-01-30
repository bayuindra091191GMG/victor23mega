@extends('admin.layouts.admin')

@section('title','Buat Stock In Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.stock_ins.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="item">
                    Inventory
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs- field-item">
                    <select id="select0" name="item[]" class='form-control'></select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="warehouse" >
                    Gudang
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="warehouse" name="warehouse" class="form-control col-md-7 col-xs-12 @if($errors->has('warehouse')) parsley-error @endif">
                        <option value="-1" @if(empty(old('warehouse'))) selected @endif> - Pilih gudang - </option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse') == $warehouse->id ? "selected":"" }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="increase">--}}
                    {{--Jumlah Penambahan--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12 price-format">--}}
                    {{--<input id="increase" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('increase')) parsley-error @endif"--}}
                           {{--name="increase" value="{{ old('increase') }}">--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="increase">
                    QTY Penambahan
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 price-format">
                    <input id="increase" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('increase')) parsley-error @endif"
                           name="increase" value="{{ old('increase') }}">
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-warning" href="{{ route('admin.stock_ins') }}"> Batal</a>
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
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    <script type="text/javascript">
        var i=1;

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

        // autoNumeric
        [numberFormat1, numberFormat2] = AutoNumeric.multiple('.price-format > input', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            decimalPlaces: 0
        });
    </script>
@endsection