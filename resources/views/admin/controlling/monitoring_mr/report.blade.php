@extends('admin.layouts.admin')

{{--@section('title','Download Report Purchase Order')--}}

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
            <h2>Laporan Status Material Request</h2>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.controlling.monitor.mr.report.excel.download'],'method' => 'post', 'id' => 'general-form', 'class'=>'form-horizontal form-label-left']) }}

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

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="type" >--}}
                    {{--Tipe MR--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<select id="type" name="type" class="form-control col-md-7 col-xs-12">--}}
                        {{--<option value="0" selected>Semua</option>--}}
                        {{--<option value="1">Part & Non-Part</option>--}}
                        {{--<option value="2">BBM</option>--}}
                        {{--<option value="2">Oli</option>--}}
                        {{--<option value="4">Servis</option>--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start_date" >
                    Dari Tanggal
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="start_date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('start_date')) parsley-error @endif"
                           name="start_date" value="{{ $dateStart }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="end_date" >
                    Sampai Tanggal
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="end_date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('end_date')) parsley-error @endif"
                           name="end_date" value="{{ $dateEnd }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status" >
                    Status
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="status" name="status" class="form-control col-md-7 col-xs-12">
                        <option value="0" selected>Semua</option>
                        <option value="3">Open</option>
                        <option value="4">Closed</option>
                        <option value="11">Closed Manual</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site" >
                    Site
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="site" name="site" class="form-control col-md-7 col-xs-12">
                        <option value="0">Semua</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="priority" >
                    Prioritas
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="priority" name="priority" class="form-control col-md-7 col-xs-12">
                        <option value="ALL">Semua</option>
                        <option value="Part - P1">Part - P1</option>
                        <option value="Part - P2">Part - P2</option>
                        <option value="Part - P3">Part - P3</option>
                        <option value="Non-Part - P1">Non-Part - P1</option>
                        <option value="Non-Part - P2">Non-Part - P2</option>
                        <option value="Non-Part - P3">Non-Part - P3</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    {{--<a class="btn btn-primary" href="{{ route('admin.issued_dockets') }}"> Batal</a>--}}
                    <a class="btn btn-success loading-animate" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Memproses Excel Anda">Unduh Excel</a>
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

        $('.loading-animate').on('click', function() {
            var $this = $(this);
            $this.button('loading');
            // setTimeout(function() {
            //     $this.button('reset');
            // }, 8000);
            $('#general-form').submit();
        });
    </script>
@endsection