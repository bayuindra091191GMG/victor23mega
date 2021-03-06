@extends('admin.layouts.admin')

@section('title', 'Ubah Cost Code '. $account->code )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.accounts.update', $account->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code" >
                    Kode Cost
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12"
                           name="code" value="{{ $account->code }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="location" >
                    Lokasi
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="location" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('location')) parsley-error @endif"
                           name="location" value="{{ $account->location }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department" >
                    Departemen
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="department" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif"
                           name="department" value="{{ $account->department }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="division" >
                    Divisi
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="division" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('division')) parsley-error @endif"
                           name="division" value="{{ $account->division }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description" >
                    Keterangan
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="description" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('description')) parsley-error @endif"
                           name="description" value="{{ $account->description }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="brand" >
                    Brand
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="brand" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('brand')) parsley-error @endif"
                           name="brand" value="{{ $account->brand }}" />
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="remark" >
                    Remark
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="remark" name="remark" rows="5" class="form-control col-md-7 col-xs-12">{{ $account->remark }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-warning" href="{{ route('admin.accounts') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Ubah</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection