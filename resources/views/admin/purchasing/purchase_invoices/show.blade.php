@extends('admin.layouts.admin')

@section('title','Data Invoice '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.purchase_invoices') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">
                <a class="btn btn-default" href="{{ route('admin.purchase_invoices.edit',[ 'purchase_invoice' => $header->id]) }}">UBAH</a>
                <a class='payment-modal btn btn-danger'>PELUNASAN</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">
                @if(\Illuminate\Support\Facades\Session::has('message'))
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            @include('partials._success')
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor Invoice
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->code }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor PO
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : <a style="text-decoration: underline;" href="{{ route('admin.purchase_orders.show', ['purchase_order' => $header->purchase_order_id]) }}" target="_blank">{{ $header->purchase_order_header->code }}</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Tanggal
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ \Carbon\Carbon::parse($header->date)->format('d M Y') }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Vendor
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : <a style="text-decoration: underline;" href="{{ route('admin.suppliers.edit', ['supplier' => $header->purchase_order_header->supplier_id]) }}">{{ $header->purchase_order_header->supplier->name }}</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Total Harga
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : Rp {{ $header->total_price_string }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Total Diskon
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $totalDiscountStr !== '0' ? 'Rp '. $totalDiscountStr : '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Ongkos Kirim
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ !empty($header->delivery_fee) && $header->delivery_fee > 0 ? 'Rp '. $header->delivery_fee_string : '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        PPN {{ !empty($header->ppn_percent) && $header->ppn_percent > 0 ? $header->ppn_percent. '%' : '' }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ !empty($header->ppn_percent) && $header->ppn_percent > 0 ? 'Rp '. $header->ppn_string : '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        PPh
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ !empty($header->pph_amount) && $header->pph_amount > 0 ? 'Rp '. $header->pph_string : '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Total Pembayaran
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : Rp {{ $header->total_payment_string ?? '-' }}
                    </div>
                </div>

                @if($mrType === 4)
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Keterangan Servis
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea rows="5" style="resize: none;" class="form-control col-md-7 col-xs-12" readonly>{{ $header->purchase_invoice_details->first()->remark }}</textarea>
                        </div>
                    </div>
                @endif

                @if($mrType !== 4)
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Total Pelunasan
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : Rp {{ $header->repayment_amount_string ?? '-' }}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-xs-12 column">
                            @if($repayment->count() > 0)
                                <h4 class="text-center">Detil Pelunasan</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr >
                                            <th class="text-center" style="width: 15%;">
                                                No
                                            </th>
                                            <th class="text-center" style="width: 15%;">
                                                Jumlah Pelunasan
                                            </th>
                                            <th class="text-center" style="width: 10%;">
                                                Dibuat Oleh
                                            </th>
                                            <th class="text-center" style="width: 10%;">
                                                Tanggal
                                            </th>
                                            <th class="text-center" style="width: 10%;">
                                                Tindakan
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($no = 1)
                                        @foreach($repayment as $data)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $no }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $data->repayment_amount_string }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $data->user->name }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $data->date_string }}
                                                </td>
                                                <td class="text-center">
                                                    <a class="delete-modal btn btn-xs btn-success" data-id="{{ $data->id }}" data-amount="{{ $data->repayment_amount_string }}" data-date="{{ $data->date_string }}"><i class="fa fa-pencil"></i></a>
                                                </td>
                                            </tr>
                                            @php($no++)
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <h4 class="text-center">Belum Ada Pelunasan</h4>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-xs-12 column">
                            <h4 class="text-center">Detil Inventory</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr >
                                        <th class="text-center" style="width: 15%;">
                                            Kode Inventory
                                        </th>
                                        <th class="text-center" style="width: 15%;">
                                            Nama Inventory
                                        </th>
                                        <th class="text-center" style="width: 10%;">
                                            UOM
                                        </th>
                                        <th class="text-center" style="width: 10%;">
                                            QTY
                                        </th>
                                        <th class="text-center" style="width: 10%;">
                                            Harga
                                        </th>
                                        <th class="text-center" style="width: 10%;">
                                            Diskon
                                        </th>
                                        <th class="text-center" style="width: 10%;">
                                            Subtotal
                                        </th>
                                        <th class="text-center" style="width: 20%;">
                                            Remark
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($header->purchase_invoice_details as $detail)
                                        <tr>
                                            <td class="text-center">
                                                {{ $detail->item->code }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->item->name }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->item->uom }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->quantity }}
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->price_string }}
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->discount_string }}
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->subtotal_string }}
                                            </td>
                                            <td>
                                                {{ $detail->remark }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </form>
        </div>
    </div>

    <div id="repaymentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin mengubah pelunasan ini?</h3>
                    <br />

                    <form role="form" class="form-horizontal form-label-left">
                        <input type="hidden" id="purchase-invoice-id" name="purchase-invoice-id"/>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="repayment-amount">
                                Jumlah
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input id="payment_edit" type="text" class="form-control col-md-7 col-xs-12"
                                       name="payment_edit" value="" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                                Tanggal
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input id="date" type="text" class="form-control col-md-7 col-xs-12"
                                       name="date" value="" required>
                            </div>
                        </div>
                    </form>

                    <br/>

                    <div class="modal-footer" id="payment-detail">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="submit" class="btn btn-success submit">
                            <span class='glyphicon glyphicon-send'></span> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin menambah pelunasan ini?</h3>
                    <br />

                    <form role="form" class="form-horizontal form-label-left">
                        <input type="hidden" id="purchase-header-invoice-id" name="purchase-invoice-id" value="{{ $header->id }}"/>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment-amount">
                                Jumlah
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input id="payment_add" type="text" class="form-control col-md-7 col-xs-12"
                                       name="payment_add" value="" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment-date">
                                Tanggal
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input id="payment-date" type="text" class="form-control col-md-7 col-xs-12"
                                       name="payment-date" value="" required>
                            </div>
                        </div>
                    </form>

                    <br/>

                    <div class="modal-footer" id="payment-header">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="submit" class="btn btn-success submit">
                            <span class='glyphicon glyphicon-send'></span> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
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
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        $('#payment-date').datetimepicker({
            format: "DD MMM Y"
        });

        //Add autonumeric
        paymentEditFormat = new AutoNumeric('#payment_edit', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        paymentAddFormat = new AutoNumeric('#payment_add', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        $(document).on('click', '.delete-modal', function(){
            $('#repaymentModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#purchase-invoice-id').val($(this).data('id'));
            $('#payment_edit').val($(this).data('amount'));
            $('#date').val($(this).data('date'));
        });

        $('#payment-detail').on('click', '.submit', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.purchase_invoices.repayment-update') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#purchase-invoice-id').val(),
                    'payment_edit': $('#payment_edit').val(),
                    'date': $('#date').val()
                },
                success: function(data) {
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal menghapus, data sudah terpakai!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location = '{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $header->id])}}';
                    }
                }
            });
        });

        //Payment
        $(document).on('click', '.payment-modal', function(){
            $('#paymentModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#payment-header').on('click', '.submit', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.purchase_invoices.repayment') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#purchase-header-invoice-id').val(),
                    'payment_add': $('#payment_add').val(),
                    'date': $('#payment-date').val()
                },
                success: function(data) {
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal menghapus, data sudah terpakai!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location = '{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $header->id])}}';
                    }
                }
            });
        });
    </script>
@endsection