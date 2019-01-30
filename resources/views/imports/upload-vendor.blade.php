@extends('admin.layouts.admin')

{{--@section('title','')--}}

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
            <h2>Import Vendor Data</h2>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.import.suppliers.submit'], 'method' => 'post', 'id' => 'general-form', 'class'=>'form-horizontal form-label-left', 'enctype'=>'multipart/form-data']) }}
            @include('partials._success')

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="file" >
                    File
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="file" type="file" class="form-control col-md-7 col-xs-12"
                           name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required/>
                </div>
            </div>

            <div class="form-group text-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <button type="submit" class="btn btn-success"> Import</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

{{--@section('styles')--}}
    {{--@parent--}}
    {{--{{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}--}}
{{--@endsection--}}

{{--@section('scripts')--}}
    {{--@parent--}}
    {{--{{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}--}}
    {{--<script>--}}
        {{--$('#contract_start').datetimepicker({--}}
            {{--format: "DD MMM Y"--}}
        {{--});--}}
        {{--$('#contract_finish').datetimepicker({--}}
            {{--format: "DD MMM Y"--}}
        {{--});--}}
    {{--</script>--}}
{{--@endsection--}}