@extends('admin.layouts.admin')

@section('title', 'Ubah Data Gudang' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.warehouses.update', $warehouse->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @include('partials._success')
                    </div>
                </div>
            @endif

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
                    Kode
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ $warehouse->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                    Nama
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                           name="name" value="{{ $warehouse->name }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site" >
                    Site
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="site_name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif" value="{{ $warehouse->site->name }}" readonly>
                    <input type="hidden" name="site" value="{{ $warehouse->site_id }}" />
                    {{--<select id="site" name="site" class="form-control col-md-7 col-xs-12 @if($errors->has('site')) parsley-error @endif">--}}
                        {{--@foreach($sites as $site)--}}
                            {{--<option value="{{ $site->id }}" {{ $warehouse->site_id == $site->id ? "selected":"" }}>{{ $site->code. ' - '. $site->name }}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone">
                    Nomor Telepon
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="phone" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('phone')) parsley-error @endif"
                           name="phone" value="{{ $warehouse->phone }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pic" >
                    PIC
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="pic" name="pic" class="form-control col-md-7 col-xs-12 @if($errors->has('pic')) parsley-error @endif"></select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-warning" href="{{ route('admin.warehouses') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    <script>
        $('#pic').select2({
            placeholder: {
                id: '{{ $warehouse->pic ?? '-1' }}',
                text: '{{ !empty($warehouse->pic) ? $warehouse->user->email. ' - '. $warehouse->user->name : ' - Pilih User - ' }}'
            },
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: '{{ route('select.users') }}',
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
    </script>
@endsection