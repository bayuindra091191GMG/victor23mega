@extends('admin.layouts.admin')

{{--@section('title','Download Report Issued Docket')--}}

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
            <h2>Report Issued Docket berdasarkan Cost Code</h2>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.issued_dockets.cost_code.download_report'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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

            <input type="hidden" id="is_ho" name="is_ho" value="{{ $isHo }}" />

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

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account">
                    Cost Code
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

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department" >
                    Departemen
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="department" name="department" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif">
                        <option value="0" @if(empty(old('department'))) selected @endif>Semua</option>
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}" {{ old('department') == $dep->id ? "selected":"" }}>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" id="is_excel" name="is_excel" value="false" />

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="warehouse" >
                    Gudang
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="warehouse" name="warehouse" class="form-control col-md-7 col-xs-12 @if($errors->has('warehouse')) parsley-error @endif">
                        <option value="-1" @if(empty(old('warehouse'))) selected @endif>Semua</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('department') == $warehouse->id ? "selected":"" }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{--<div class="form-group">--}}
            {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery" >--}}
            {{--Unit Alat Berat--}}
            {{--</label>--}}
            {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
            {{--<select id="machinery" name="machinery" class="form-control col-md-7 col-xs-12"></select>--}}
            {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery" >--}}
                    {{--Penggunaan--}}
                    {{--<span class="required">*</span>--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<label class="radio-inline"><input type="radio" id="radio_all" name="type" value="all" checked>Semua</label>--}}
                    {{--<label class="radio-inline"><input type="radio" id="radio_non_bbm" name="type" value="non-bbm">Non-BBM</label>--}}
                    {{--<label class="radio-inline"><input type="radio" id="radio_bbm" name="type" value="bbm">BBM</label>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group" id="section_fuel" style="display: none;">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="item" >--}}
                    {{--Inventory BBM--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<select id="item" name="item" class="form-control col-md-7 col-xs-12"></select>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    {{--<a class="btn btn-primary" href="{{ route('admin.issued_dockets') }}"> Batal</a>--}}
                    <button type="submit" class="btn btn-success">Unduh Laporan</button>
                    <a class="btn btn-success excel-submit">Unduh Excel</a>
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
    <script>
        // Datetimepicker
        $('#start_date').datetimepicker({
            format: "DD MMM Y"
        });
        $('#end_date').datetimepicker({
            format: "DD MMM Y"
        });

        $(document).on('click', '.excel-submit', function() {
            $('#is_excel').val('true');
            $('#general-form').submit();
        });

        $('#account').select2({
            placeholder: {
                id: '-1',
                text: 'Semua'
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

        {{--$('#machinery').select2({--}}
            {{--placeholder: {--}}
                {{--id: '-1',--}}
                {{--text: ' - Semua - '--}}
            {{--},--}}
            {{--width: '100%',--}}
            {{--minimumInputLength: 1,--}}
            {{--allowClear: true,--}}
            {{--ajax: {--}}
                {{--url: '{{ route('select.machineries') }}',--}}
                {{--dataType: 'json',--}}
                {{--data: function (params) {--}}
                    {{--return {--}}
                        {{--q: $.trim(params.term)--}}
                    {{--};--}}
                {{--},--}}
                {{--processResults: function (data) {--}}
                    {{--return {--}}
                        {{--results: data--}}
                    {{--};--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}

        {{--$(document).on("change","#radio_bbm",function(){--}}
            {{--$('#section_fuel').show();--}}
        {{--});--}}

        {{--$(document).on("change","#radio_non_bbm",function(){--}}
            {{--$('#section_fuel').hide();--}}
        {{--});--}}

        {{--$(document).on("change","#radio_all",function(){--}}
            {{--$('#section_fuel').hide();--}}
        {{--});--}}

        {{--$('#item').select2({--}}
            {{--placeholder: {--}}
                {{--id: '-1',--}}
                {{--text: ' - Pilih BBM - '--}}
            {{--},--}}
            {{--width: '100%',--}}
            {{--minimumInputLength: 0,--}}
            {{--allowClear: true,--}}
            {{--ajax: {--}}
                {{--url: '{{ route('select.items') }}',--}}
                {{--dataType: 'json',--}}
                {{--data: function (params) {--}}
                    {{--return {--}}
                        {{--q: $.trim(params.term),--}}
                        {{--type: 'fuel'--}}
                    {{--};--}}
                {{--},--}}
                {{--processResults: function (data) {--}}
                    {{--return {--}}
                        {{--results: data--}}
                    {{--};--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}
    </script>
@endsection