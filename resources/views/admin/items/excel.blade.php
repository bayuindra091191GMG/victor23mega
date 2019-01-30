@extends('admin.layouts.admin')

{{--@section('title','Download Excel Inventory Stock')--}}

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
            <h2>Download Excel Inventory Master</h2>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.items.download_excel'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="category" >
                    Kategori Inventory
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="category" name="category" class="form-control">
                        <option value="-1" @if(empty(old('category'))) selected @endif>Semua</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    {{--<button type="submit" class="btn btn-success">Unduh Laporan</button>--}}
                    <a class="btn btn-success loading-animate" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Memproses Excel Anda">Unduh Excel</a>
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
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // Disable button
        // $(document).on('click', '.loading-animate', function() {
        //     $('#confirmModal').modal({
        //         backdrop: 'static',
        //         keyboard: false
        //     });
        // });

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