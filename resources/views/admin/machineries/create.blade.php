@extends('admin.layouts.admin')

@section('title', 'Tambah Alat Berat' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.machineries.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                    Kode Alat Berat
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ old('code') }}"  required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery_category" >
                    Kategori Alat Berat
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="machinery_category" name="machinery_category" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery_category')) parsley-error @endif">
                        <option value="-1" @if(empty(old('machinery_category'))) selected @endif> - Pilih kategori alat berat - </option>
                        @foreach($machineryCategories as $machineryCategory)
                            <option value="{{ $machineryCategory->id }}" {{ old('machinery_category') == $machineryCategory->id ? "selected":"" }}>{{ $machineryCategory->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery_brand" >
                    Brand Alat Berat
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="machinery_brand" name="machinery_brand" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery_brand')) parsley-error @endif">
                        <option value="-1" @if(empty(old('machinery_brand'))) selected @endif> - Pilih merek alat berat - </option>
                        @foreach($machineryBrands as $machineryBrand)
                            <option value="{{ $machineryBrand->id }}" {{ old('machinery_brand') == $machineryBrand->id ? "selected":"" }}>{{ $machineryBrand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery_type" >
                    Unit Model
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="machinery_type" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery_type')) parsley-error @endif"
                           name="machinery_type" value="{{ old('machinery_type') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="engine_type">
                    Engine Model
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="engine_type" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('engine_type')) parsley-error @endif"
                           name="engine_type" value="{{ old('engine_type') }}" >
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sn_engine">
                    S/N Engine
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sn_engine" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('sn_engine')) parsley-error @endif"
                           name="sn_engine" value="{{ old('sn_engine') }}" >
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sn_chasis">
                    S/N Chasis
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sn_chasis" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('sn_chasis')) parsley-error @endif"
                           name="sn_chasis" value="{{ old('sn_chasis') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="production_year">
                    Tahun Produksi
                </label>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <input id="production_year" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('production_year')) parsley-error @endif"
                           name="production_year" value="{{ old('production_year') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="purchase_date">
                    Tanggal Pembelian
                </label>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <input id="purchase_date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('purchase_date')) parsley-error @endif"
                           name="purchase_date" value="{{ old('purchase_date') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="location">
                    Lokasi
                </label>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <input id="location" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('location')) parsley-error @endif"
                           name="location" value="{{ old('location') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description" >
                    Keterangan Tambahan
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="description" name="description" rows="5" class="form-control col-md-7 col-xs-12 @if($errors->has('description')) parsley-error @endif" style="resize: vertical">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status" >
                    Status
                </label>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <select id="status" name="status" class="form-control col-md-7 col-xs-12 @if($errors->has('status')) parsley-error @endif">
                        <option value="6" @if(empty(old('status')) || ( !empty(old('status')) && old('status') == "6")) selected @endif>RFU</option>
                        <option value="9" {{ old('status') == "9" ? "selected":"" }}>BDN</option>
                        <option value="9" {{ old('status') == "15" ? "selected":"" }}>Standby</option>
                        <option value="9" {{ old('status') == "16" ? "selected":"" }}>SCRAP</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary" href="{{ route('admin.machineries') }}"> Batal</a>
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
    <script>

        $('#production_year').datetimepicker({
            format: "YYYY",
            viewMode: "years"
        });

        $('#purchase_date').datetimepicker({
            format: "DD MMM Y"
        });
    </script>
@endsection