@extends('admin.layouts.admin')

@section('title','Ubah Purchase Order Service')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.purchase_orders.update', $header->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left', 'enctype' => 'multipart/form-data', 'novalidate']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="po_code">
                    Nomor PO
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="po_code" name="po_code" class="form-control col-md-12 col-xs-12" value="{{ $header->code }}" readonly/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ $date }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pr_code">
                    Nomor PR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pr_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('pph')) parsley-error @endif"
                           name="pr_code" value="{{ $header->purchase_request_header->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="supplier" >
                    Vendor
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="supplier" name="supplier" class="form-control col-md-7 col-xs-12 @if($errors->has('supplier')) parsley-error @endif">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="service_price">
                    Harga Servis
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="service_price" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('service_price')) parsley-error @endif"
                           name="service_price">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="extra_discount">
                    Diskon
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="extra_discount" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_fee')) parsley-error @endif"
                           name="extra_discount">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="delivery_fee">
                    Ongkos Kirim
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="delivery_fee" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_fee')) parsley-error @endif"
                           name="delivery_fee">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ppn">
                    Tambah PPN
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="flat" id="ppn" name="ppn" @if(!empty($header->ppn_percent) && $header->ppn_percent > 0) checked @endif>
                            @if(!empty($header->ppn_percent) && $header->ppn_percent > 0) PPN tersimpan: {{ $header->ppn_percent }}% @endif
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pph">
                    PPh
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pph" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('pph')) parsley-error @endif"
                           name="pph">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment_term">
                    Payment Term (Hari)
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="payment_term" type="text" placeholder="JUMLAH HARI DALAM ANGKA" class="form-control col-md-2 col-xs-12 @if($errors->has('payment_term')) parsley-error @endif"
                           name="payment_term" value="{{ $header->payment_term }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="special_note">
                    Special Note
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="special_note" name="special_note" rows="5" class="form-control col-md-7 col-xs-12 @if($errors->has('special_note')) parsley-error @endif" style="resize: vertical">{{ $header->special_note }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note" >
                    Keterangan Servis
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="note" name="note" rows="5" style="resize: vertical;" class="form-control col-md-7 col-xs-12" readonly>{{ $header->purchase_order_details->first()->remark }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quotation1">
                    Lampiran Quotation Utama
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('quotation1', array('id' => 'quotation1', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quotation2">
                    Lampiran Quotation Tambahan 1
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('quotation2', array('id' => 'quotation2', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quotation3">
                    Lampiran Quotation Tambahan 2
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('quotation3', array('id' => 'quotation3', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.purchase_orders') }}"> Batal</a>
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
    {{ Html::script(mix('assets/admin/js/stringbuilder.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    {{ Html::script(mix('assets/admin/js/fileinput.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // FILEINPUT
        @if(!empty($pdfUrl1))
            $("#quotation1").fileinput({
                initialPreview: '{{ $pdfUrl1 }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#quotation1").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif

        @if(!empty($pdfUrl2))
            $("#quotation2").fileinput({
                initialPreview: '{{ $pdfUrl2 }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#quotation2").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif

        @if(!empty($pdfUrl3))
            $("#quotation3").fileinput({
                initialPreview: '{{ $pdfUrl3 }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#quotation3").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif

        $('#supplier').select2({
            placeholder: {
                id: '{{ $header->supplier_id }}',
                text: '{{ $header->supplier->name }}'
            },
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route('select.suppliers') }}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        _token: $('input[name=_token]').val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        // Add autonumeric
        servicePriceFormat = new AutoNumeric('#service_price', '{{ $header->purchase_order_details->first()->price }}', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        paymentTermFormat = new AutoNumeric('#payment_term', {
            minimumValue: '0',
            maximumValue: '999',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            modifyValueOnWheel: false
        });

        pphFormat = new AutoNumeric('#pph', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($header->pph_amount) && $header->pph_amount > 0)
            pphFormat.clear();

            pphFormat.set('{{ $header->pph_amount }}', {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        deliveryFeeFormat = new AutoNumeric('#delivery_fee', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($header->delivery_fee))
            deliveryFeeFormat.clear();

            var deliveryFee = '{{ $header->delivery_fee }}';
            var deliveryFeeClean = deliveryFee.replace(/\./g,'');

            deliveryFeeFormat.set(deliveryFeeClean, {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        extraDiscountFormat = new AutoNumeric('#extra_discount', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($header->extra_discount))
            extraDiscountFormat.clear();

            extraDiscountFormat.set('{{ $header->extra_discount }}', {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        function rupiahFormat(nStr) {
            // nStr += '';
            // x = nStr.split(',');
            // x1 = x[0];
            // x2 = x.length > 1 ? ',' + x[1] : '';
            // var rgx = /(\d+)(\d{3})/;
            // while (rgx.test(x1)) {
            //     x1 = x1.replace(rgx, '$1' + '.' + '$2');
            // }
            // return x1 + x2;
            var value = nStr.toLocaleString(
                "de-DE",
                { minimumFractionDigits: 2}
            );

            return value;
        }
    </script>
@endsection