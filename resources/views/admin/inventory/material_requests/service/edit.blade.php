@extends('admin.layouts.admin')

@section('title','Ubah Material Request Servis '. $header->code)

@section('content')
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.material_requests.update', $header->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pr_code">
                    Nomor MR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pr_code" type="text" class="form-control col-md-7 col-xs-12"
                           name="pr_code" value="{{ $header->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ $date }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department" >
                    Departemen
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="department" name="department" class="form-control col-md-7 col-xs-12 @if($errors->has('department')) parsley-error @endif">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $header->department_id == $department->id ? "selected":"" }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery" >
                    Unit Alat Berat
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="machinery" name="machinery" class="form-control col-md-7 col-xs-12 @if($errors->has('machinery')) parsley-error @endif">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="priority">
                    Prioritas
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="priority" name="priority" class="form-control col-md-7 col-xs-12 @if($errors->has('priority')) parsley-error @endif">

                        <select id="priority" name="priority" class="form-control col-md-7 col-xs-12 @if($errors->has('priority')) parsley-error @endif">
                            <option value="-1" @if($isOldPriority) selected @endif> - Pilih Prioritas - </option>
                            <option value="Part - P1" @if($header->priority === 'Part - P1') selected @endif>Part - P1</option>
                            <option value="Part - P2" @if($header->priority === 'Part - P2') selected @endif>Part - P2</option>
                            <option value="Part - P3" @if($header->priority === 'Part - P3') selected @endif>Part - P3</option>
                            <option value="Non-Part - P1" @if($header->priority === 'Non-Part - P1') selected @endif>Non-Part - P1</option>
                            <option value="Non-Part - P2" @if($header->priority === 'Non-Part - P2') selected @endif>Non-Part - P2</option>
                            <option value="Non-Part - P3" @if($header->priority === 'Non-Part - P3') selected @endif>Non-Part - P3</option>
                        </select>

                        {{--@if($isOldPriority)--}}
                            {{--<select id="priority" name="priority" class="form-control col-md-7 col-xs-12 @if($errors->has('priority')) parsley-error @endif">--}}
                                {{--<option value="-1" selected> - Pilih Prioritas - </option>--}}
                                {{--<option value="Part - P1">Part - P1</option>--}}
                                {{--<option value="Part - P2">Part - P2</option>--}}
                                {{--<option value="Part - P3">Part - P3</option>--}}
                                {{--<option value="Non-Part - P1">Non-Part - P1</option>--}}
                                {{--<option value="Non-Part - P2">Non-Part - P2</option>--}}
                                {{--<option value="Non-Part - P3">Non-Part - P3</option>--}}
                            {{--</select>--}}
                        {{--@else--}}
                            {{--<input id="priority" name="priority" type="text" class="form-control col-md-7 col-xs-12"--}}
                                   {{--value="{{ $header->priority }}" readonly />--}}
                        {{--@endif--}}

                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="km">
                    KM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="km" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('km')) parsley-error @endif"
                           name="km" value="{{ $header->km }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="hm">
                    HM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="hm" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('hm')) parsley-error @endif"
                           name="hm" value="{{ $header->hm  }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note" >
                    Keterangan Servis
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="note" name="note" rows="10" style="resize: vertical;" class="form-control col-md-7 col-xs-12">{{ $header->material_request_details->first()->remark }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="feedback">
                    Feedback
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="feedback" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('feedback')) parsley-error @endif"
                              name="feedback" readonly>{{ $header->feedback }}</textarea>
                </div>
            </div>

            <input type="hidden" name="type" id="type" value="4"/>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary" href="{{ route('admin.material_requests.other.show', ['material_request' => $header->id]) }}"> Batal</a>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    {{ Html::style(mix('assets/admin/css/fileinput.css')) }}
    <style>
        .box-section{
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 2px;
            padding: 10px;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    {{ Html::script(mix('assets/admin/js/fileinput.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // FILEINPUT
        @if(!empty($pdfUrl))
            $("#document").fileinput({
                initialPreview: '{{ $pdfUrl }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#document").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif

        $('#machinery').select2({
            placeholder: {
                id: '{{ $header->machinery_id ?? '-1' }}',
                text: '{{ $header->machinery_id !== null ? $header->machinery->code : ' - Pilih Alat Berat - ' }}'
            },
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route('select.machineries') }}',
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