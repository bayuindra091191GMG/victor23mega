@extends('admin.layouts.admin')

@section('title','Data Purchase Order '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.purchase_orders') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">

                @if($header->status_id == 3)
                    <a class="close-modal btn btn-danger" data-id="{{ $header->id }}">TUTUP PO</a>
                @endif

                @if($permission || $header->status_id != 3)
                    <a class="btn btn-default" href="{{ route('admin.purchase_orders.print',[ 'purchase_order' => $header->id]) }}" target="_blank">CETAK</a>
                @endif

                @if($header->status_id === 3 && $isApproving)
                    <a class="btn btn-default" href="{{ route('admin.approval_rules.po_approval',[ 'approval_rule' => $header->id]) }}">APPROVE</a>
                @endif

                @if($header->status_id === 3)
                    @if($setting === 1)
                        @if($permission)

                            @if($mrType !== 4)
                                <a class="btn btn-success" href="{{ route('admin.item_receipts.create',[ 'po' => $header->id]) }}">PROSES GOODS RECEIPT</a>
                            @endif
                        @endif

                        @if(!$isAtLeastOneApproved)
                            <a class="btn btn-default" href="{{ route('admin.purchase_orders.edit',[ 'purchase_order' => $header->id]) }}">UBAH</a>
                        @endif

                        @else
                        @if($mrType !== 4)
                            <a class="btn btn-success" href="{{ route('admin.item_receipts.create',[ 'po' => $header->id]) }}">PROSES GOODS RECEIPT</a>
                        @endif
                        {{--<a class="btn btn-default" href="{{ route('admin.purchase_orders.edit',[ 'purchase_order' => $header->id]) }}">UBAH</a>--}}

                    @endif
                @endif

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                        @if(\Illuminate\Support\Facades\Session::has('message'))
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @include('partials._success')
                                </div>
                            </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Session::has('error'))
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @include('partials._error')
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                STATUS
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                @if($header->status_id == 3)
                                    : <span style="font-weight: bold; color: green;">OPEN
                                    @if($header->is_all_received)
                                        - SUDAH DITERIMA
                                    @endif
                                    @if($header->is_all_invoiced)
                                        - SUDAH DI-INVOICE
                                    @endif
                                        </span>
                                @elseif($header->status_id == 4)
                                    : <span style="font-weight: bold; color: red;">CLOSED</span>
                                @elseif($header->status_id == 11)
                                    : <span style="font-weight: bold; color: red;">CLOSED MANUAL</span>
                                @elseif($header->status_id == 13)
                                    : <span style="font-weight: bold; color: red;">REJECTED</span>
                                @endif
                            </div>
                        </div>

                        @if($header->status_id == 11)
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Alasan Tutup
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->close_reason ?? '' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Tanggal Tutup
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->closing_date }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Ditutup Oleh
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->closedBy->email ?? '' }}
                                </div>
                            </div>
                        @endif

                        @if($setting == 1)
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Approved
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @if($status == 99)
                                        :

                                        @foreach($approvalData as $data)
                                            {!! $data !!},
                                        @endforeach

                                    @elseif($status < 99)
                                        :

                                        @foreach($approvalData as $data)
                                            {!! $data !!},
                                        @endforeach

                                    @elseif($status == 101)
                                        :
                                        @foreach($approvalData as $data)
                                            {!! $data !!},
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Tanggal Approve
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->approved_date !== null ? \Carbon\Carbon::parse($header->approved_date)->format('d M Y') : "" }}
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Nomor PO
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->code }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Tanggal
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $date }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Nomor PR
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $header->purchase_request_id]) }}" target="_blank">{{ $header->purchase_request_header->code }}</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Nomor MR
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : <a style="text-decoration: underline;" href="{{ $mrUrl }}" target="_blank">{{ $header->purchase_request_header->material_request_header->code }}</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Gudang
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->warehouse->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Prioritas
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->purchase_request_header->priority }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Vendor
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : <a style="text-decoration: underline;" href="{{ route('admin.suppliers.edit', ['supplier' => $header->supplier_id]) }}">{{ $header->supplier->name }}</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                {{ $mrType === 4 ? 'Total Harga Servis' : 'Total Harga'  }}
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
                                Total PO
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : Rp {{ $header->total_payment_string }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Payment Term
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ !empty($header->payment_term) ? $header->payment_term. ' Hari' : '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Special Note
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->special_note ?? '-' }}
                            </div>
                        </div>

                        @if($mrType === 4)
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Keterangan Servis
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="5" style="resize: none;" class="form-control col-md-7 col-xs-12" readonly>{{ $header->purchase_order_details->first()->remark }}</textarea>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        @if(!empty($header->quotation_pdf_path_1))
                            <a href="{{ route('admin.purchase_orders.pdf1.download', ['purchase_order' => $header->id]) }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-download"></i>&nbsp;UNDUH QUOTATION UTAMA
                            </a>
                        @endif

                        @if(!empty($header->quotation_pdf_path_2))
                            <a href="{{ route('admin.purchase_orders.pdf2.download', ['purchase_order' => $header->id]) }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-download"></i>&nbsp;UNDUH QUOTATION TAMBAHAN 1
                            </a>
                        @endif

                        @if(!empty($header->quotation_pdf_path_3))
                            <a href="{{ route('admin.purchase_orders.pdf3.download', ['purchase_order' => $header->id]) }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-download"></i>&nbsp;UNDUH QUOTATION TAMBAHAN 2
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        @if($isTrackingAvailable)
                            <hr/>
                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-xs-12 column">
                                    <div class="row">
                                        <h4 class="text-center">Tracking PO</h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                            <h5>Goods Receipt</h5>
                                            @if($trackedGrHeaders->count() > 0)
                                                @foreach($trackedGrHeaders as $grHeader)
                                                    <a href="{{ route('admin.item_receipts.show', ['item_receipt' => $grHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $grHeader->code }}</a><br/>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                            <h5>Surat Jalan</h5>
                                            @if($trackedSjHeaders->count() > 0)
                                                @foreach($trackedSjHeaders as $sjHeader)
                                                    <a href="{{ route('admin.delivery_orders.show', ['delivery_order' => $sjHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $sjHeader->code }}</a>
                                                    @if($sjHeader->status_id === 3)
                                                        - <span style="color: green">{{ strtoupper($sjHeader->status->description) }}</span><br/>
                                                    @else
                                                        - <span style="color: red">{{ strtoupper($sjHeader->status->description) }}</span><br/>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                            <h5>Purchase Invoice</h5>
                                            @if($trackedPiHeaders->count() > 0)
                                                @foreach($trackedPiHeaders as $piHeader)
                                                    <a href="{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $piHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $piHeader->code }}</a><br/>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                            <h5>Request For Payment</h5>
                                            @if($trackedRFPHeaders->count() > 0)
                                                @foreach($trackedRFPHeaders as $rfpHeader)
                                                    <a href="{{ route('admin.payment_requests.show', ['payment_request' => $rfpHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $rfpHeader->code }}</a><br/>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <hr/>

                    </div>
                </div>


                @if($mrType !== 4)
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-xs-12">
                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-xs-12 column">
                                    <h4 class="text-center">Detil Inventory</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr >
                                                <th class="text-center" style="width: 10%;">
                                                    Kode Inventory
                                                </th>
                                                <th class="text-center" style="width: 10%;">
                                                    Nama Inventory
                                                </th>
                                                <th class="text-center" style="width: 10%">
                                                    Part Number Asli
                                                </th>
                                                <th class="text-center" style="width: 10%;">
                                                    UOM
                                                </th>
                                                <th class="text-center" style="width: 5%;">
                                                    QTY
                                                </th>
                                                <th class="text-center" style="width: 5%;">
                                                    QTY Diterima
                                                </th>
                                                <th class="text-center" style="width: 5%;">
                                                    QTY Ter-Invoice
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
                                                <th class="text-center" style="width: 15%;">
                                                    Remark
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($header->purchase_order_details as $detail)
                                                <tr>
                                                    <td>
                                                        {{ $detail->item->code }}
                                                    </td>
                                                    <td>
                                                        {{ $detail->item->name }}
                                                    </td>
                                                    <td>
                                                        {{ $detail->item->part_number ?? '-' }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $detail->item->uom }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $detail->quantity }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $detail->received_quantity }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $detail->quantity_invoiced }}
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
                                                <tr>
                                                    <td colspan="9" class="text-right">TOTAL :</td>
                                                    <td class="text-right">{{ $header->total_payment_before_tax_string }}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                @endif

            </form>
        </div>
    </div>

    <div id="closeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin menutup dokumen PO ini secara manual?</h3>
                    <br />

                    <form role="form">
                        <input type="hidden" id="closed-id" name="closed-id"/>
                        <label for="reason">Alasan Hapus:</label>
                        <textarea id="reason" name="reason" rows="5" class="form-control col-md-7 col-xs-12" style="resize: vertical"></textarea>
                    </form>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Tidak
                        </button>
                        <button type="submit" class="btn btn-danger closed">
                            <span class='glyphicon glyphicon-trash'></span> Ya
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
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.close-modal', function(){
            $('#closeModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#closed-id').val($(this).data('id'));
        });

        $('.modal-footer').on('click', '.closed', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.purchase_orders.close') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#closed-id').val(),
                    'reason': $('#reason').val()
                },
                success: function(data) {
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal menutup PR!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location.reload();
                    }
                }
            });
        });
    </script>
@endsection