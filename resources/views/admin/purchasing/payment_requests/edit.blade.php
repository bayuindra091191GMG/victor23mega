@extends('admin.layouts.admin')

@section('title','Ubah Payment Request '. $header->code)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.payment_requests.update', $header->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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

            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12 alert alert-success alert-dismissible fade in" role="alert">
                        <strong>{{ \Illuminate\Support\Facades\Session::get('message') }}</strong>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">
                    Nomor Payment Request
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ $header->code }}" readonly>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type" >
                    Tipe Payment
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @if(!empty($purchaseInvoices) && $purchaseInvoices->count() > 0)
                        <input id="type" type="text" class="form-control col-md-7 col-xs-12"
                               name="type" value="NORMAL" readonly>
                    @else
                        <select id="type" name="type" class="form-control col-md-7 col-xs-12" onchange="checkDp()">
                            <option value="dp" {{ $header->type === 'dp' ? 'selected' : '' }}>DOWN PAYMENT (DP)</option>
                            <option value="cbd" {{ $header->type === 'cbd' ? 'selected' : '' }}>CASH BEFORE DELIVERY (CBD)</option>
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
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="supplier_name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $header->supplier->name }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="bank_name" >
                    Nama Bank
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="bank_name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('bank_name')) parsley-error @endif"
                           name="bank_name" value="{{ $header->supplier->bank_name }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account_no" >
                    Nomor Rekening
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="account_no" type="text" min="0" class="form-control col-md-7 col-xs-12 @if($errors->has('account_no')) parsley-error @endif"
                           name="account_no" value="{{ $header->supplier->bank_account_number }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="account_name" >
                    Nama Rekening
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="account_name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('account_name')) parsley-error @endif"
                           name="account_name" value="{{ $header->supplier->bank_account_name }}" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note" >
                    Notes
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="note" name="note" rows="5" style="resize: vertical;" class="form-control col-md-7 col-xs-12">{{ $header->note }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-warning" href="{{ route('admin.payment_requests.show', ['payment_request' => $header->id]) }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
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
                                    <th class="text-center">
                                        No
                                    </th>
                                    <th class="text-center" style="width: 10%">
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
                                    <th class="text-center" style="width: 10%">
                                        Tanggal
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        Tindakan
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $idx = 0; ?>

                            @foreach($purchaseInvoices as $detail)
                                <?php $idx++; ?>
                                <tr class='item{{ $idx }}'>
                                    <td class='text-center'>
                                        {{ $idx }}
                                    </td>
                                    <td class='text-center'>
                                        <a style="text-decoration: underline" href="{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $detail->purchase_invoice_header_id]) }}" target="_blank">{{ $detail->purchase_invoice_header->code }}</a>
                                        <input type='hidden' name='item[]' value='{{ $detail->purchase_invoice_header_id }}'/>
                                    </td>
                                    <td class='text-center'>
                                        <a style="text-decoration: underline" href="{{ route('admin.purchase_orders.show', ['purchase_order' => $detail->purchase_invoice_header->purchase_order_id]) }}" target="_blank">{{ $detail->purchase_invoice_header->purchase_order_header->code }}</a>
                                    </td>
                                    <td class='text-center'>
                                        {{ $detail->purchase_invoice_header->purchase_order_header->supplier->name }}
                                    </td>
                                    <td class='text-right'>
                                        {{ $detail->purchase_invoice_header->repayment_amount_string }}
                                    </td>
                                    <td class='text-right'>
                                        {{ $detail->purchase_invoice_header->total_payment_string }}
                                    </td>
                                    <td class='text-center'>
                                        {{ $detail->purchase_invoice_header->date_string }}
                                    </td>
                                    <td class='text-center'>
                                        <a class="edit-modal btn btn-info" data-idx="{{ $idx }}" data-type="PI" data-detail-id="{{ $detail->id }}" data-pi-id="{{ $detail->purchase_invoice_header_id }}" data-pi-code="{{ $detail->purchase_invoice_header->code }}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a class="delete-modal btn btn-danger" data-idx="{{ $idx }}" data-type="PI" data-detail-id="{{ $detail->id }}" data-pi-id="{{ $detail->purchase_invoice_header_id }}" data-pi-code="{{ $detail->purchase_invoice_header->code }}">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
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
                                    <th class="text-center" style="width: 10%">
                                        Tindakan
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $idx = 0; ?>

                                @foreach($purchaseOrders as $detail)
                                    <?php $idx++; ?>
                                    <tr class='item{{ $idx }}'>
                                        <td class='text-center'>
                                            {{ $idx }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->purchase_order_header->code }}
                                            <input type='hidden' name='item[]' value='{{ $detail->purchase_order_id }}'/>
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->purchase_order_header->purchase_request_header->code }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->purchase_order_header->supplier->name }}
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->purchase_order_header->total_price_string }}
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->purchase_order_header->total_discount_string  }}
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->purchase_order_header->delivery_fee_string  }}
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->purchase_order_header->total_payment_string  }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->purchase_order_header->date_string }}
                                        </td>
                                        <td class='text-center'>
                                            <a class="edit-modal btn btn-info" data-idx="{{ $idx }}" data-type="PO" data-detail-id="{{ $detail->id }}" data-po-id="{{ $detail->purchase_order_id }}" data-po-code="{{ $detail->purchase_order_header->code }}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <a class="delete-modal btn btn-danger" data-idx="{{ $idx }}" data-type="PO" data-detail-id="{{ $detail->id }}" data-po-id="{{ $detail->purchase_order_id }}" data-po-code="{{ $detail->purchase_order_header->code }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <input id="index_counter" type="hidden" value="{{ $idx }}"/>

            <hr/>

            {{ Form::close() }}
        </div>
    </div>

    <!-- Modal form to add new detail -->
    <div id="addModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group" id="pi_add_form">
                            <label class="control-label col-sm-2" for="pi_add">Nomor PI:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="pi_add" name="pi_add"></select>
                                <p class="errorPi text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group" id="po_add_form">
                            <label class="control-label col-sm-2" for="po_add">Nomor PO:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="po_add" name="po_add"></select>
                                <p class="errorPo text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <input type="hidden" id="type_add" />
                    </form>
                    <div class="modal-footer">\
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-success add" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-check'></span> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to edit a detail -->
    <div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group" id="pi_edit_form">
                            <label class="control-label col-sm-2" for="pi_edit">Nomor PI:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="pi_edit" name="pi_edit"></select>
                                <p class="errorPi text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group" id="po_edit_form">
                            <label class="control-label col-sm-2" for="po_edit">Nomor PO:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="po_edit" name="po_edit"></select>
                                <p class="errorPo text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <input type="hidden" id="detail_edit" />
                        <input type="hidden" id="type_edit" />
                        <input type="hidden" id="idx_edit" />
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-primary edit" data-dismiss="modal">
                            <span class='glyphicon glyphicon-check'></span> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to delete a form -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin menghapus detail ini?</h3>
                    <br />
                    <form class="form-horizontal" role="form">
                        <div class="form-group" id="pi_delete_form">
                            <label class="control-label col-sm-2" for="pi_delete">Nomor PI:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pi_delete" readonly>
                            </div>
                        </div>
                        <div class="form-group" id="po_delete_form">
                            <label class="control-label col-sm-2" for="po_delete">Nomor PO:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="po_delete" readonly>
                            </div>
                        </div>
                        <input type="hidden" id="detail_delete" />
                        <input type="hidden" id="type_delete" />
                        <input type="hidden" id="idx_delete" />
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-danger delete" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-trash'></span> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
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
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // Auto Numeric
        amountFormat = new AutoNumeric('#amount', '{{ $amount }}', {
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

        // Add new detail
        $(document).on('click', '.add-modal', function() {
            $('#type_add').val($(this).data('type'));

            if($(this).data('type') === 'PI'){
                $('#po_add_form').hide();
                $('#pi_add_form').show();
                $('#pi_add').select2({
                    placeholder: {
                        id: '-1',
                        text: ' - Pilih Invoice - '
                    },
                    width: '100%',
                    minimumInputLength: 1,
                    ajax: {
                        url: '{{ route('select.purchase_invoices') }}',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term),
                                supplier : $(this).data('supplier')
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    }
                });
            }
            else{
                $('#pi_add_form').hide();
                $('#po_add_form').show();
                $('#po_add').select2({
                    placeholder: {
                        id: '-1',
                        text: ' - Pilih PO - '
                    },
                    width: '100%',
                    minimumInputLength: 1,
                    ajax: {
                        url: '{{ route('select.purchase_orders') }}',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term),
                                supplier : $(this).data('supplier')
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    }
                });
            }

            $('.modal-title').text('Ubah Detail');
            $('#addModal').modal('show');
        });
        $('.modal-footer').on('click', '.add', function() {
            var typeAdd = $('#type_add').val();

            if(typeAdd === 'PI' && $('#pi_add').val() == null){
                alert('Pilih nomor Invoice!');
            }

            if(typeAdd === 'PO' && $('#po_add').val() == null){
                alert('Pilih nomor PO!');
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('admin.payment_request_details.store') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'header_id': '{{ $header->id }}',
                    'pi_id': $('#pi_add').val(),
                    'po_id': $('#po_add').val()
                },
                success: function(data) {
                    $('.errorPi').addClass('hidden');
                    $('.errorPo').addClass('hidden');

                    if ((data.errors)) {
                        if(data.errors === 'pi_required'){
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Pilih nomor Invoice!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        if(data.errors === 'po_required'){
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Pilih nomor PO!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'pi_exists'){
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Nomor Invoice sudah terdaftar, mohon refresh browser!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'po_exists'){
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Nomor PO sudah terdaftar, mohon refresh browser!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else{
                            setTimeout(function () {
                                $('#addModal').modal('show');
                                toastr.error('Gagal simpan data!', 'Peringatan', {timeOut: 5000});
                            }, 500);

                            if (data.errors.pi_id) {
                                $('.errorPi').removeClass('hidden');
                                $('.errorPi').text(data.errors.pi_id);
                            }
                            if (data.errors.po_id) {
                                $('.errorPo').removeClass('hidden');
                                $('.errorPo').text(data.errors.po_id);
                            }
                        }
                    } else {
                        toastr.success('Berhasil simpan detail!', 'Sukses', {timeOut: 5000});

                        // Increase idx
                        var idx = $('#index_counter').val();
                        idx++;
                        $('#index_counter').val(idx);

                        var sbAdd = new stringbuilder();

                        if(typeAdd === 'PI'){
                            sbAdd.append("<tr class='item" + idx + "'>");
                            sbAdd.append("<td class='text-center'>" + idx + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_invoice_header.show_url + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_invoice_header.show_url_po + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_invoice_header.po_supplier_name + "</td>");
                            sbAdd.append("<td class='text-right'>" + data.purchase_invoice_header.repayment_amount_string + "</td>");
                            sbAdd.append("<td class='text-right'>" + data.purchase_invoice_header.total_payment_string + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_invoice_header.date_string + "</td>");
                            sbAdd.append("<td class='text-center'>");
                            sbAdd.append("<a class='edit-modal btn btn-info' data-idx='" + idx + "' data-type='PI' data-detail-id='" + data.id + "' data-pi-id='" + data.purchase_invoice_header_id + "' data-pi-code='" + data.purchase_invoice_header.code + "'><span class='glyphicon glyphicon-edit'></span></a>");
                            sbAdd.append("<a class='delete-modal btn btn-danger' data-idx='" + idx + "' data-type='PI' data-detail-id='" + data.id + "' data-pi-id='" + data.purchase_invoice_header_id + "' data-pi-code='" + data.purchase_invoice_header.code + "'><span class='glyphicon glyphicon-trash'></span></a>");
                            sbAdd.append("</td>");
                            sbAdd.append("</tr>");
                        }
                        else{
                            sbAdd.append("<tr class='item" + idx + "'>");
                            sbAdd.append("<td class='text-center'>" + idx + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_order_header.show_url + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_order_header.show_url_pr + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_order_header.supplier_name + "</td>");
                            sbAdd.append("<td class='text-right'>" + data.purchase_order_header.total_price_string + "</td>");
                            sbAdd.append("<td class='text-right'>" + data.purchase_order_header.total_discount_string + "</td>");
                            sbAdd.append("<td class='text-right'>" + data.purchase_order_header.delivery_fee_string + "</td>");
                            sbAdd.append("<td class='text-right'>" + data.purchase_order_header.total_payment_string + "</td>");
                            sbAdd.append("<td class='text-center'>" + data.purchase_order_header.date_string + "</td>");
                            sbAdd.append("<td class='text-center'>");
                            sbAdd.append("<a class='edit-modal btn btn-info' data-idx='" + idx + "' data-type='PO' data-detail-id='" + data.id + "' data-po-id='" + data.purchase_order_id + "' data-po-code='" + data.purchase_order_header.code + "'><span class='glyphicon glyphicon-edit'></span></a>");
                            sbAdd.append("<a class='delete-modal btn btn-danger' data-idx='" + idx + "' data-type='PO' data-detail-id='" + data.id + "' data-po-id='" + data.purchase_order_id + "' data-po-code='" + data.purchase_order_header.code + "'><span class='glyphicon glyphicon-trash'></span></a>");
                            sbAdd.append("</td>");
                            sbAdd.append("</tr>");
                        }

                        $('#detail_table').append(sbAdd.toString());
                    }
                },
            });
        });

        // Edit detail
        $(document).on('click', '.edit-modal', function() {
            $('#detail_edit').val($(this).data('detail-id'));
            $('#type_edit').val($(this).data('type'));
            $('#idx_edit').val($(this).data('idx'));

            if($(this).data('type') === 'PI'){
                $('#po_edit_form').hide();
                $('#pi_edit_form').show();
                $('#pi_edit').select2({
                    placeholder: {
                        id: $(this).data('pi-id'),
                        text: $(this).data('pi-code')
                    },
                    width: '100%',
                    minimumInputLength: 1,
                    ajax: {
                        url: '{{ route('select.purchase_invoices') }}',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term),
                                supplier : '{{ $header->supplier_id }}'
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    }
                });
            }
            else{
                $('#pi_edit_form').hide();
                $('#po_edit_form').show();
                $('#po_edit').select2({
                    placeholder: {
                        id: $(this).data('po-id'),
                        text: $(this).data('po-code')
                    },
                    width: '100%',
                    minimumInputLength: 1,
                    ajax: {
                        url: '{{ route('select.purchase_orders') }}',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term),
                                supplier : '{{ $header->supplier_id }}'
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    }
                });
            }

            $('.modal-title').text('Ubah Detail');
            $('#editModal').modal('show');
        });
        $('.modal-footer').on('click', '.edit', function() {
            var typeEdit = $('#type_edit').val();
            var idxEdit = $('#idx_edit').val();

            if((typeEdit === 'PI' && $('#pi_edit').val() == null) ||
                (typeEdit === 'PO' && $('#po_edit').val() == null)){
                $('#editModal').modal('hide');
            }
            else{
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.payment_request_details.update') }}',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'header_id': '{{ $header->id }}',
                        'detail_id' : $('#detail_edit').val(),
                        'type': typeEdit,
                        'pi_id': $('#pi_edit').val(),
                        'po_id': $('#po_edit').val()
                    },
                    success: function(data) {
                        $('.errorPi').addClass('hidden');
                        $('.errorPo').addClass('hidden');

                        if ((data.errors)) {
                            if(data.errors === 'pi_required'){
                                setTimeout(function () {
                                    $('#editModal').modal('show');
                                    toastr.error('Pilih nomor Invoice!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                                }, 500);
                            }
                            if(data.errors === 'po_required'){
                                setTimeout(function () {
                                    $('#editModal').modal('show');
                                    toastr.error('Pilih nomor PO!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                                }, 500);
                            }
                            else if(data.errors === 'pi_deleted'){
                                setTimeout(function () {
                                    $('#editModal').modal('show');
                                    toastr.error('Nomor Invoice sudah terhapus, mohon refresh browser!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                                }, 500);
                            }
                            else if(data.errors === 'po_deleted'){
                                setTimeout(function () {
                                    $('#editModal').modal('show');
                                    toastr.error('Nomor PO sudah terhapus, mohon refresh browser!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                                }, 500);
                            }
                            else{
                                setTimeout(function () {
                                    $('#editModal').modal('show');
                                    toastr.error('Gagal simpan data!', 'Peringatan', {timeOut: 5000});
                                }, 500);

                                if (data.errors.pi_id) {
                                    $('.errorPi').removeClass('hidden');
                                    $('.errorPi').text(data.errors.pi_id);
                                }
                                if (data.errors.po_id) {
                                    $('.errorPo').removeClass('hidden');
                                    $('.errorPo').text(data.errors.po_id);
                                }
                            }
                        } else {
                            toastr.success('Berhasil simpan detail!', 'Sukses', {timeOut: 5000});

                            var sbEdit = new stringbuilder();

                            if(typeEdit === 'PI'){
                                sbEdit.append("<tr class='item" + idxEdit + "'>");
                                sbEdit.append("<td class='text-center'>" + idxEdit + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_invoice_header.show_url + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_invoice_header.show_url_po + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_invoice_header.po_supplier_name + "</td>");
                                sbEdit.append("<td class='text-right'>" + data.purchase_invoice_header.repayment_amount_string + "</td>");
                                sbEdit.append("<td class='text-right'>" + data.purchase_invoice_header.total_payment_string + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_invoice_header.date_string + "</td>");
                                sbEdit.append("<td class='text-center'>");
                                sbEdit.append("<a class='edit-modal btn btn-info' data-idx='" + idxEdit + "' data-type='PI' data-detail-id='" + data.id + "' data-pi-id='" + data.purchase_invoice_header_id + "' data-pi-code='" + data.purchase_invoice_header.code + "'><span class='glyphicon glyphicon-edit'></span></a>");
                                sbEdit.append("<a class='delete-modal btn btn-danger' data-idx='" + idxEdit + "' data-type='PI' data-detail-id='" + data.id + "' data-pi-id='" + data.purchase_invoice_header_id + "' data-pi-code='" + data.purchase_invoice_header.code + "'><span class='glyphicon glyphicon-trash'></span></a>");
                                sbEdit.append("</td>");
                                sbEdit.append("</tr>");
                            }else{
                                sbEdit.append("<tr class='item" + idxEdit + "'>");
                                sbEdit.append("<td class='text-center'>" + idxEdit + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_order_header.show_url + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_order_header.show_url_pr + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_order_header.supplier_name + "</td>");
                                sbEdit.append("<td class='text-right'>" + data.purchase_order_header.total_price_string + "</td>");
                                sbEdit.append("<td class='text-right'>" + data.purchase_order_header.total_discount_string + "</td>");
                                sbEdit.append("<td class='text-right'>" + data.purchase_order_header.delivery_fee_string + "</td>");
                                sbEdit.append("<td class='text-right'>" + data.purchase_order_header.total_payment_string + "</td>");
                                sbEdit.append("<td class='text-center'>" + data.purchase_order_header.date_string + "</td>");
                                sbEdit.append("<td class='text-center'>");
                                sbEdit.append("<a class='edit-modal btn btn-info' data-idx='" + idxEdit + "' data-type='P0' data-detail-id='" + data.id + "' data-po-id='" + data.purchase_order_id + "' data-po-code='" + data.purchase_order_header.code + "'><span class='glyphicon glyphicon-edit'></span></a>");
                                sbEdit.append("<a class='delete-modal btn btn-danger' data-idx='" + idxEdit + "' data-type='P0' data-detail-id='" + data.id + "' data-po-id='" + data.purchase_order_id + "' data-po-code='" + data.purchase_order_header.code + "'><span class='glyphicon glyphicon-trash'></span></a>");
                                sbEdit.append("</td>");
                                sbEdit.append("</tr>");
                            }


                            $('.item' + idxEdit).replaceWith(sbEdit.toString());
                        }
                    },
                });
            }

        });

        // Delete detail
        $(document).on('click', '.delete-modal', function() {
            $('#detail_delete').val($(this).data('detail-id'));
            $('#type_delete').val($(this).data('type'));
            $('#idx_delete').val($(this).data('idx'));

            if($(this).data('type') === 'PI'){
                $('#po_delete_form').hide();
                $('#pi_delete_form').show();
                $('#pi_delete').val($(this).data('pi-code'));
            }
            else{
                $('#pi_delete_form').hide();
                $('#po_delete_form').show();
                $('#po_delete').val($(this).data('po-code'));
            }

            $('#deleteModal').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.payment_request_details.delete') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'header_id': '{{ $header->id }}',
                    'detail_id': $('#detail_delete').val(),
                    'type': $('#type_delete').val(),
                },
                success: function(data) {
                    alert(data.errors);
                    if ((data.errors)){
                        if(data.errors === 'pi_last'){
                            setTimeout(function () {
                                $('#deleteModal').modal('show');
                                toastr.error('Gagal menghapus detil!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'po_last'){
                            setTimeout(function () {
                                $('#deleteModal').modal('show');
                                toastr.error('Gagal menghapus detil!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'pi_deleted'){
                            setTimeout(function () {
                                $('#deleteModal').modal('show');
                                toastr.error('Nomor Invoice sudah terhapus, mohon refresh browser!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else if(data.errors === 'po_deleted'){
                            setTimeout(function () {
                                $('#deleteModal').modal('show');
                                toastr.error('Nomor PO sudah terhapus, mohon refresh browser!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else{
                            setTimeout(function () {
                                toastr.error('Gagal menghapus detil!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                    }
                    else{
                        toastr.success('Berhasil menghapus detail!', 'Sukses', {timeOut: 5000});
                        $('.item' + $('#idx_delete').val()).remove();
                    }
                }
            });
        });

    </script>
@endsection