@extends('admin.layouts.admin')

@section('title','Data Surat Jalan '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.delivery_orders') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">
                @if($header->status_id == 3)

                    @if(!$isPartial)
                        <a class="confirm-modal btn btn-success" data-id="{{ $header->id }}">KONFIRMASI PENUH</a>
                    @endif

                    <a href="{{ route('admin.delivery_orders.partial_confirm', ['id' => $header->id]) }}" class="btn btn-default">KONFIRMASI PARSIAL</a>
                    <a class="cancel-modal btn btn-danger" data-id="{{ $header->id }}">BATAL</a>
                @endif
                {{--<a class="btn btn-default" href="{{ route('admin.delivery_orders.edit',[ 'delivery_order' => $header->id]) }}">UBAH</a>--}}
                <a class="btn btn-default" href="{{ route('admin.delivery_orders.print',[ 'delivery_order' => $header->id]) }}">CETAK</a>
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
                            : <span style="font-weight: bold; color: green;">OPEN</span>
                        @elseif($header->status_id == 4)
                            : <span style="font-weight: bold; color: red;">CLOSED</span>
                        @elseif($header->status_id == 5)
                            : <span style="font-weight: bold; color: red;">BATAL</span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor Surat Jalan
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
                        Nomor Goods Receipt (GR)
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @if(!empty($header->item_receipt_id))
                            : <a style="text-decoration: underline;" href="{{ route('admin.item_receipts.show', ['item_receipt' => $header->item_receipt_id]) }}" target="_blank">{{ $header->item_receipt_header->code }}</a>
                        @else
                            : -
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor PO
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @if(!empty($header->item_receipt_id))
                            : <a style="text-decoration: underline;" href="{{ route('admin.purchase_orders.show', ['purchase_order' => $header->item_receipt_header->purchase_order_id]) }}" target="_blank">{{ $header->item_receipt_header->purchase_order_header->code }}</a>
                        @else
                            : -
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor PR
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @if(!empty($header->item_receipt_id))
                            : <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $header->item_receipt_header->purchase_order_header->purchase_request_id]) }}" target="_blank">{{ $header->item_receipt_header->purchase_order_header->purchase_request_header->code }}</a>
                        @else
                            : -
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor MR
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @if(!empty($header->item_receipt_id))
                            : <a style="text-decoration: underline;" href="{{ $mrShowUrl }}" target="_blank">{{ $header->item_receipt_header->purchase_order_header->purchase_request_header->material_request_header->code }}</a>
                        @else
                            : -
                        @endif
                    </div>
                </div>

                @if($header->status_id === 4)
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Dikonfirmasi Datang Oleh
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : {{ $header->confirmBy->email ?? '-' }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Tanggal Konfirmasi Datang
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : {{ \Carbon\Carbon::parse($header->confirm_date)->format('d M Y') }}
                        </div>
                    </div>
                @elseif($header->status_id === 5)
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Dibatalkan Oleh
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : {{ $header->cancelBy->email ?? '-' }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Tanggal Batal
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : {{ \Carbon\Carbon::parse($header->cancel_date)->format('d M Y') }}
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Site Keberangkatan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->fromWarehouse->site->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Gudang Keberangkatan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->fromWarehouse->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Site Tujuan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->toWarehouse->site->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Gudang Tujuan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->toWarehouse->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Unit Alat Berat
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->machinery->code ?? '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Keterangan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->remark ?? '-' }}
                    </div>
                </div>

                <hr/>

                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-xs-12 column">
                        <h4 class="text-center">Detil Inventory</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr >
                                    <th class="text-center" style="width: 10%">
                                        No
                                    </th>
                                    <th class="text-center" style="width: 25%">
                                        Kode Inventory
                                    </th>
                                    <th class="text-center" style="width: 25%">
                                        Nama Inventory
                                    </th>
                                    <th class="text-center" colspan="2" style="width: 20%">
                                        QTY
                                    </th>
                                    <th class="text-center" style="width: 20%">
                                        Keterangan
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @php( $idx = 1 )
                                @foreach($header->delivery_order_details as $detail)
                                    <tr>
                                        <td class="text-center">
                                            {{ $idx }}
                                        </td>
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
                                        <td>
                                            {{ $detail->remark ?? '-' }}
                                        </td>
                                    </tr>
                                    @php( $idx++ )
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="confirm_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin mengkonfirmasi barang datang?</h3>
                    <br />

                    <form role="form">
                        <input type="hidden" id="confirmed-id" name="confirmed-id"/>
                    </form>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Tidak
                        </button>
                        <button type="submit" class="btn btn-success confirm">
                            <span class='glyphicon glyphicon-check'></span> Ya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cancel_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin membatalkan surat jalan?</h3>
                    <br />

                    <form role="form">
                        <input type="hidden" id="canceled-id" name="canceled-id"/>
                    </form>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Tidak
                        </button>
                        <button type="submit" class="btn btn-success cancel">
                            <span class='glyphicon glyphicon-check'></span> Ya
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
        $(document).on('click', '.confirm-modal', function(){
            $('#confirm_modal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#confirmed-id').val($(this).data('id'));
        });

        $('.modal-footer').on('click', '.confirm', function() {
            $('.confirm').prop("disabled", true);
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.delivery_orders.confirm') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#confirmed-id').val(),
                },
                success: function(data) {
                    if ((data.errors)){
                        $('.confirm').prop("disabled", false);
                        setTimeout(function () {
                            toastr.error('Gagal konfirmasi', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location = '{{ route('admin.delivery_orders') }}';
                    }
                }
            });
        });

        $(document).on('click', '.cancel-modal', function(){
            $('#cancel_modal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#canceled-id').val($(this).data('id'));
        });

        $('.modal-footer').on('click', '.cancel', function() {
            $('.cancel').prop("disabled", true);
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.delivery_orders.cancel') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#canceled-id').val(),
                },
                success: function(data) {
                    if ((data.errors)){
                        $('.cancel').prop("disabled", false);
                        setTimeout(function () {
                            toastr.error('Gagal membatalkan', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location = '{{ route('admin.delivery_orders') }}';
                    }
                }
            });
        });
    </script>
@endsection