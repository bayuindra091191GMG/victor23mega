@extends('admin.layouts.admin')

{{--@section('title','Download Report Purchase Request')--}}

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
            <h2>Laporan Pembelian Inventory</h2>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.items.download_invoice_report'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="inventory" >
                    Inventory
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="inventory" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('inventory')) parsley-error @endif"
                           name="inventory" value="{{ $item->code. ' - '. $item->name }}" readonly>
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start_date" >
                    Dari Tanggal
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="start_date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('start_date')) parsley-error @endif"
                           name="start_date" value="{{ old('start_date') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="end_date" >
                    Sampai Tanggal
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="end_date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('end_date')) parsley-error @endif"
                           name="end_date" value="{{ old('end_date') }}">
                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="department" >--}}
                    {{--Departemen--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<select id="department" name="department" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif">--}}
                        {{--<option value="0" @if(empty(old('department'))) selected @endif>Semua</option>--}}
                        {{--@foreach($departments as $dep)--}}
                            {{--<option value="{{ $dep->id }}" {{ old('department') == $dep->id ? "selected":"" }}>{{ $dep->name }}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="status" >--}}
                    {{--Status--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<select id="status" name="status" class="form-control col-md-7 col-xs-12">--}}
                        {{--<option value="0" selected>Semua</option>--}}
                        {{--<option value="3">Open</option>--}}
                        {{--<option value="4">Closed</option>--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}

            <input type="hidden" id="item" name="item" value="{{ $item->id }}"/>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-danger" href="{{ route('admin.items.show', ['item' => $item->id]) }}"> Batal</a>
                    <button type="submit" class="btn btn-success">Unduh Laporan</button>
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
    <script>
        $('#start_date').datetimepicker({
            format: "DD MMM Y"
        });
        $('#end_date').datetimepicker({
            format: "DD MMM Y"
        });
    </script>
@endsection