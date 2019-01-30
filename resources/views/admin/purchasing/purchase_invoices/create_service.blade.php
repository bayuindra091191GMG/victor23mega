@extends('admin.layouts.admin')

@section('title','Buat Purchase Invoice Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.purchase_invoices.service.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                    Nomor Invoice
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ $autoNumber }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_number"></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="flat" id="auto_number" name="auto_number" checked="checked"> Auto Number
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="po_code" >
                    Nomor PO
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="po_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="po_code" value="{{ $purchaseOrder->code }}" readonly>
                    <input type="hidden" id="po_id" name="po_id" value="{{ $purchaseOrder->id }}">
                </div>
                {{--<div class="col-md-2 col-sm-2 col-xs-12">--}}
                {{--<a class="get-po-data btn btn-info">--}}
                {{--Ambil Data--}}
                {{--</a>--}}
                {{--@if(!empty($purchaseOrder))--}}
                {{--<a class="clear-po-data btn btn-info">--}}
                {{--Reset Data--}}
                {{--</a>--}}
                {{--@endif--}}
                {{--</div>--}}
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ old('date') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="service_price">
                    Harga Servis
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="service_price" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('service_price')) parsley-error @endif"
                           name="service_price" value="{{ $purchaseOrder->purchase_order_details->first()->price_string }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="extra_discount">
                    Diskon
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="extra_discount" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('extra_discount')) parsley-error @endif"
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
                            <input type="checkbox" class="flat" id="ppn" name="ppn" @if(!empty($purchaseOrder) && !empty($purchaseOrder->ppn_amount)) checked @endif> PPN sekarang: 10%
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
                           name="pph" value="{{ old('pph') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment_term">
                    Payment Term (Hari)
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="payment_term" type="text" placeholder="Hari Dalam Angka" class="form-control col-md-2 col-xs-12 @if($errors->has('payment_term')) parsley-error @endif"
                           name="payment_term" value="{{ $purchaseOrder->payment_term ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note" >
                    Keterangan Servis
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="note" name="note" rows="5" style="resize: vertical;" class="form-control col-md-7 col-xs-12" readonly>{{ $purchaseOrder->purchase_order_details->first()->remark }}</textarea>
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.purchase_invoices') }}"> Batal</a>
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
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // Auto Numbering
        $('#auto_number').change(function(){
            if(this.checked){
                $('#code').val('{{ $autoNumber }}');
                $('#code').prop('readonly', true);
            }
            else{
                $('#code').val('');
                $('#code').prop('readonly', false);
            }
        });

        // Add autonumeric
        pphFormat = new AutoNumeric('#pph', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($purchaseOrder) && !empty($purchaseOrder->pph_amount) && $purchaseOrder->pph_amount > 0)
            pphFormat.clear();

            pphFormat.set('{{ $purchaseOrder->pph_amount }}', {
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

        @if(!empty($purchaseOrder) && !empty($purchaseOrder->delivery_fee) && $purchaseOrder->delivery_fee > 0)
            deliveryFeeFormat.clear();

            deliveryFeeFormat.set('{{ $purchaseOrder->delivery_fee }}', {
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

        @if(!empty($purchaseOrder) && !empty($purchaseOrder->extra_discount) && $purchaseOrder->extra_discount > 0)
            extraDiscountFormat.clear();

            extraDiscountFormat.set('{{ $purchaseOrder->extra_discount }}', {
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
            // x2 = x.length > 1 ? '.' + x[1] : '';
            // var rgx = /(\d+)(\d{3})/;
            // while (rgx.test(x1)) {
            //     x1 = x1.replace(rgx, '$1' + '.' + '$2');
            // }
            // return x1 + x2;
            return nStr.toLocaleString(
                "de-DE",
                {minimumFractionDigits: 2}
            );
        }
    </script>
@endsection