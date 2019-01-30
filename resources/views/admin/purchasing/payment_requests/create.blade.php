@extends('admin.layouts.admin')

@section('title','Buat Payment Request Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.payment_requests.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                    Nomor Payment Request
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ $autoNumber }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_number"></label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="flat" id="auto_number" name="auto_number" checked="checked"> Auto Number
                        </label>
                    </div>
                </div>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type" >
                    Tipe Payment
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @if(!empty($purchaseInvoices) && $purchaseInvoices->count() > 0)
                        <input id="type" type="text" class="form-control col-md-7 col-xs-12"
                               name="type" value="NORMAL" readonly>
                    @else
                        <select id="type" name="type" class="form-control col-md-7 col-xs-12" onchange="checkDp()">
                            <option value="dp" @if(!empty($purchaseOrders) && $purchaseOrders->count() > 0) selected @endif>DOWN PAYMENT (DP)</option>
                            <option value="cbd">CASH BEFORE DELIVERY (CBD)</option>
                        </select>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amount">
                    Jumlah Permintaan
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="amount" name="amount" type="text" class="form-control col-md-7 col-xs-12"/>
                    <input type="hidden" id="total_payment" name="total_payment" value="{{ $totalPayment }}" required/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="supplier_name">
                    Vendor
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="supplier_name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $vendor->name }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="bank_name" >
                    Nama Bank
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="bank_name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('bank_name')) parsley-error @endif"
                           name="bank_name" value="{{ $vendor->bank_name }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account_no" >
                    Nomor Rekening
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="account_no" type="text" min="0" class="form-control col-md-7 col-xs-12 @if($errors->has('account_no')) parsley-error @endif"
                           name="account_no" value="{{ $vendor->bank_account_number }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account_name" >
                    Nama Rekening
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="account_name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('account_name')) parsley-error @endif"
                           name="account_name" value="{{ $vendor->bank_account_name }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note" >
                    Notes
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="note" name="note" rows="5" style="resize: vertical;" class="form-control col-md-7 col-xs-12">{{ old('note') }}</textarea>
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-xs-12 box-section">

                    @if(!empty($purchaseInvoices) && $purchaseInvoices->count() > 0)
                        <h3 class="text-center">Detil Invoice</h3>
                    @endif

                    @if(!empty($purchaseOrders) && $purchaseOrders->count() > 0)
                        <h3 class="text-center">Detil PO</h3>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail_table">
                            <thead>
                            @if(!empty($purchaseInvoices) && $purchaseInvoices->count() > 0)
                                <input type="hidden" value="pi" name="flag" />
                                <tr >
                                    <th class="text-center" style="width: 10%">
                                        No
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Nomor Invoice
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Nomor PO
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Nama Vendor
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Pelunasan
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Total Invoice
                                    </th>
                                    <th class="text-center" style="width: 15%">
                                        Tanggal
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @php( $idx = 0 )

                            @foreach($purchaseInvoices as $detail)
                                @php( $idx++ )
                                <tr class='item{{ $idx }}'>
                                    <td class='text-center'>
                                        {{ $idx }}
                                    </td>
                                    <td class='text-center'>
                                        <a style="text-decoration: underline" href="{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $detail->id]) }}" target="_blank">{{ $detail->code }}</a>
                                        <input type='hidden' name='item[]' value='{{ $detail->id }}'/>
                                    </td>
                                    <td class='text-center'>
                                        <a style="text-decoration: underline" href="{{ route('admin.purchase_orders.show', ['purchase_order' => $detail->purchase_order_id]) }}" target="_blank">{{ $detail->purchase_order_header->code }}</a>
                                    </td>
                                    <td class='text-center'>
                                        {{ $detail->purchase_order_header->supplier->name }}
                                    </td>
                                    <td class='text-center'>
                                        {{ $detail->repayment_amount_string }}
                                    </td>
                                    <td class='text-center'>
                                        {{ $detail->total_payment_string }}
                                    </td>
                                    <td class='text-center'>
                                        {{ $detail->date_string }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            @endif
                            @if(!empty($purchaseOrders) && $purchaseOrders->count() > 0)
                                <thead>
                                <input type="hidden" value="po" name="flag" />
                                <tr >
                                    <th class="text-center">
                                        No
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Nomor PO
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Nomor PR
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Nama Vendor
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Total Harga
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Total Diskon
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Ongkos Kirim
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Total PO
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Tanggal
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @php( $idx = 0 )

                                @foreach($purchaseOrders as $detail)
                                    @php( $idx++ )
                                    <tr class='item{{ $idx }}'>
                                        <td class='text-center'>
                                            {{ $idx }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->code }}
                                            <input type='hidden' name='item[]' value='{{ $detail->id }}'/>
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->purchase_request_header->code }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->supplier->name }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->total_price_string }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->total_discount_string  }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->delivery_fee_string  }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->total_payment_string  }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->date_string }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <input id="index_counter" type="hidden" value="{{ $idx }}"/>
            <input id="supplier" type="hidden" name="supplier" value="{{ $vendor->id }}"/>

            <hr/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.payment_requests') }}"> Batal</a>
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

        // Auto Numeric
        amountFormat = new AutoNumeric('#amount', '{{ $totalPayment }}', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        var typeElement = document.getElementById("dp_amount");
        if(document.getElementById("type").value === 'dp'){
            typeElement.setAttribute("required", "");
        }

        function checkDp(){
            var value = document.getElementById("type").value;
            if(value === 'dp'){
                typeElement.setAttribute("required", "");
            }
            else{
                typeElement.removeAttribute("required");
            }
        }

//        // Delete detail
//        var deletedId = "0";
//        $(document).on('click', '.delete-modal', function() {
//            $('.modal-title').text('Hapus Detail');
//            deletedId = $(this).data('id');
//            $('#item_delete').val($(this).data('item-text'));
//            $('#qty_delete').val($(this).data('qty'));
//            $('#remark_delete').val($(this).data('remark'));
//            $('#deleteModal').modal('show');
//        });
//        $('.modal-footer').on('click', '.delete', function() {
//            $('.item' + deletedId).remove();
//        });
    </script>
@endsection