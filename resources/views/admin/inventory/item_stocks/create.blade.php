@extends('admin.layouts.admin')

@section('title','Membuat Pengaturan Inventory '. $item->code)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.item_stocks.option.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}
            {{ csrf_field() }}

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

            <input type="hidden" id="item_id" name="item_id" value="{{ $item->id }}" />

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">
                    Kode Inventory
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12" value="{{ $item->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                    Nama Inventory
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $item->name }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="warehouse">
                    Gudang
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <select id="warehouse" name="warehouse" class="form-control col-md-7 col-xs-12 @if($errors->has('group')) parsley-error @endif">
                        <option value="-1" {{ empty(old('warehouse')) ? "selected":"" }}> - Pilih Gudang - </option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse') == $warehouse->id ? "selected":"" }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="location">
                    Lokasi atau Rak
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input id="location" name="location" type="text" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="stock_min">
                    Minimum Stok
                </label>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <input id="stock_min" name="stock_min" type="text" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="stock_max">
                    Maksimum Stok
                </label>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <input id="stock_max" name="stock_max" type="text" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="stock_warning">
                    Notifikasi Stok
                </label>
                <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                    <input type="checkbox" class="flat" id="stock_warning" name="stock_warning"/>
                </div>
            </div>

            <div class="form-group">
                <div class="control-label col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-9">
                    <a class="btn btn-warning" href="{{ route('admin.items.show', ['item' => $item->id]) }}"> Batal</a>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    <script>
        minStockFormat = new AutoNumeric('#stock_min', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            emptyInputBehavior: "zero",
            modifyValueOnWheel: false
        });

        maxStockFormat = new AutoNumeric('#stock_max', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            emptyInputBehavior: "zero",
            modifyValueOnWheel: false
        });
    </script>
@endsection