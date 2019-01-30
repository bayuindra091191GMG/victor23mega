@extends('admin.layouts.admin')

@section('title','Data RFQ Vendor '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.quotations') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">
                <a class="btn btn-default" href="{{ route('admin.quotations.edit',[ 'quotation' => $header->id]) }}">UBAH</a>
                <a class="btn btn-default" href="{{ route('admin.quotations.print',[ 'quotation' => $header->id]) }}" target="_blank">CETAK</a>
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
                        Nomor RFQ
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
                        : {{ \Carbon\Carbon::parse($header->date)->format('d M Y') }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor PR
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $header->purchase_request_id]) }}">{{ $header->purchase_request_header->code }}</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Vendor
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        :
                        @if(!empty($header->supplier_id))
                            <a style="text-decoration: underline;" href="{{ route('admin.suppliers.edit', ['supplier' => $header->supplier_id]) }}">{{ $header->supplier->name }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Total Harga
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ !empty($header->total_price) ? 'Rp '. $header->total_price_string : '-' }}
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
                        Total RFQ
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : Rp {{ $header->total_payment_string }}
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

                                @foreach($header->quotation_details as $detail)
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
                                            {{ $detail->discount_amount_string ?? '0' }}
                                        </td>
                                        <td class="text-right">
                                            {{ $detail->subtotal_string }}
                                        </td>
                                        <td>
                                            {{ $detail->remark ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
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

    </script>
@endsection