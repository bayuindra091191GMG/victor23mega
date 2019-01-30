@extends('admin.layouts.admin')

{{--@section('title','Download Report Purchase Invoice')--}}

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
            <h2>Laporan Pembelian Site</h2>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.purchase_invoices.site.download_report'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site" >
                    Site
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="site" name="site" class="form-control col-md-7 col-xs-12 @if($errors->has('site')) parsley-error @endif">
                        <option value="-1" @if(empty(old('site'))) selected @endif>Semua</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site') == $site->id ? "selected":"" }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mr_type" >
                    Jenis MR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="mr_type" name="mr_type" class="form-control col-md-7 col-xs-12 @if($errors->has('mr_type')) parsley-error @endif">
                        <option value="-1" @if(empty(old('mr_type'))) selected @endif>Semua</option>
                        <option value="1" @if(old('mr_type') === "1") selected @endif>Part/Non-Part</option>
                        <option value="2" @if(old('mr_type') === "2") selected @endif>BBM</option>
                        <option value="3" @if(old('mr_type') === "3") selected @endif>Oli</option>
                        <option value="4" @if(old('mr_type') === "4") selected @endif>Servis</option>
                    </select>
                </div>
            </div>
            <input type="hidden" id="is_preview" name="is_preview" value="false" />

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-success preview-submit">Preview PDF</a>
                    <a class="btn btn-success loading-animate" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Memproses Laporan Anda">Unduh Laporan</a>
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
        $(document).on('click', '.preview-submit', function() {
            $('#is_preview').val('true');
            $('#general-form').submit();
        });

        // Datetimepicker
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