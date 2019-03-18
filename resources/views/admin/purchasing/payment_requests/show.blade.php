@extends('admin.layouts.admin')

@section('title','Data RFP '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.payment_requests') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">
                <a class="btn btn-default" href="{{ route('admin.payment_requests.print',[ 'payment_request' => $header->id]) }}" target="_blank">CETAK</a>
                <a class="btn btn-default" href="{{ route('admin.payment_requests.edit',[ 'payment_request' => $header->id]) }}">UBAH</a>
                <a class="btn btn-danger" onclick="deleteRfp();" style="cursor: pointer;">HAPUS</a>
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
                        Nomor Payment Request
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
                        : {{ $header->date_string }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Tipe Payment
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $type }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Vendor
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->supplier->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nama Bank
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->requester_bank_name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor Rekening
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->requester_bank_account }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nama Rekening
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->requester_account_name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Note
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->note ?? '-' }}
                    </div>
                </div>

                @if($header->type === 'dp')
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Total Biaya Non-DP
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : Rp {{ $header->total_amount_string }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Total DP yang Diminta
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : Rp {{ !empty($header->dp_amount) && $header->dp_amount > 0 ? $header->dp_amount_string : $header->total_amount_string }}
                        </div>
                    </div>

                @else
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Total Biaya
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : Rp {{ $header->total_amount_string }}
                        </div>
                    </div>
                @endif

                <hr>

                @php($i = 1)
                @if($flag == "pi")
                    <div class="form-group">
                        <label class="text-center col-lg-12 col-md-12 col-xs-12">Detil Purchase Invoice</label>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-xs-12 column">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Nomor PI</th>
                                        <th class="text-center">Tanggal PI</th>
                                        <th class="text-center">Nomor PO</th>
                                        <th class="text-center">Tanggal PO</th>
                                        <th class="text-center">Nomor PR</th>
                                        <th class="text-center">Tanggal PR</th>
                                        <th class="text-center">Pelunasan</th>
                                        <th class="text-center">Total Invoice</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($purchaseInvoices as $detail)
                                        <tr>
                                            <td class="text-center">
                                                {{ $i }}
                                            </td>
                                            <td class="text-left">
                                                <a style="text-decoration: underline" href="{{ route('admin.purchase_invoices.show',['purchase_invoice' => $detail->purchase_invoice_header_id]) }}">{{ $detail->purchase_invoice_header->purchase_order_header->code }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->purchase_invoice_header->date_string }}
                                            </td>
                                            <td class="text-left">
                                                <a style="text-decoration: underline" href="{{ route('admin.purchase_orders.show',['purchase_order' => $detail->purchase_invoice_header->purchase_order_id]) }}">{{ $detail->purchase_invoice_header->purchase_order_header->code }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->purchase_invoice_header->purchase_order_header->date_string }}
                                            </td>
                                            <td class="text-left">
                                                <a style="text-decoration: underline" href="{{ route('admin.purchase_requests.show',['purchase_request' => $detail->purchase_invoice_header->purchase_order_header->purchase_request_id]) }}">{{ $detail->purchase_invoice_header->purchase_order_header->purchase_request_header->code }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->purchase_invoice_header->purchase_order_header->purchase_request_header->date_string }}
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->purchase_invoice_header->repayment_amount_string }}
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->purchase_invoice_header->total_payment_string }}
                                            </td>

                                        </tr>
                                        @php($i++)
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if($flag == "po")
                    <div class="form-group">
                        <label class="text-center col-lg-12 col-md-12 col-xs-12">Detil Purchase Invoice</label>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-xs-12 column">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nomor PO</th>
                                    <th class="text-center">Tanggal PO</th>
                                    <th class="text-center">Nomor PR</th>
                                    <th class="text-center">Tanggal PR</th>
                                    <th class="text-center">Total PO</th>
                                    <th class="text-center">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrders as $detail)
                                        <tr>
                                            <td class="text-center">
                                                {{ $i }}
                                            </td>
                                            <td class="text-left">
                                                <a style="text-decoration: underline" href="{{ route('admin.purchase_orders.show',['purchase_order' => $detail->purchase_order_id]) }}">{{ $detail->purchase_order_header->code }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->purchase_order_header->date_string }}
                                            </td>
                                            <td class="text-left">
                                                <a style="text-decoration: underline" href="{{ route('admin.purchase_requests.show',['purchase_request' => $detail->purchase_order_header->purchase_request_id]) }}">{{ $detail->purchase_order_header->purchase_request_header->code }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->purchase_order_header->purchase_request_header->date_string }}
                                            </td>
                                            <td class="text-right">
                                                {{ $detail->purchase_order_header->total_payment_string }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->purchase_order_header->status->description }}
                                            </td>
                                        </tr>
                                        @php($i++)
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </form>
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

                    {{ Form::open(['route'=>['admin.payment_requests.destroy'],'method' => 'post','id' => 'form-delete-rfp' ]) }}

                    <h3 class="text-center">Apakah anda yakin ingin menghapus Payment Request {{ $header->code }}?</h3>
                    <br />
                    <input type="hidden" name="deleted_id" value="{{ $header->id }}"/>
                    <div class="modal-footer">
                        <a class="btn btn-warning" style="cursor: pointer;" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Tidak
                        </a>
                        <a class="btn btn-danger delete" style="cursor: pointer;" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-trash'></span> Ya
                        </a>
                    </div>

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
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
    <script type="text/javascript">
        function deleteRfp(){
            $('#deleteModal').modal('show');
        }
        $(document).on('click', '.delete', function() {
            $('#form-delete-rfp').submit();
        });

    </script>
@endsection