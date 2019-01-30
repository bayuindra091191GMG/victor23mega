<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Request Document</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Custom Style -->
    <style type="text/css">
        @media print {
            td.heading {
                background-color: #e6e9ef !important;
                -webkit-print-color-adjust: exact;
            }}
    </style>
</head>
<body>
<div class="container" style="width: 750px; font-size: 10px; font-family: "Times New Roman", Times, serif;">
<h5 align="center"><b>PAYMENT REQUEST</b></h5>
<h6 align="center" style="margin-top: -6px;"><b>( Permintaan Pembayaran )</b></h6>
<hr style="display: block; height: 1px; background: transparent; width: 100%; border: none; border-top: solid 1px #aaa; margin-top:-6px;" />
<table width="100%">
    <tr>
        <td width="19%">
            NOMOR
        </td>
        <td width="1%">
            :
        </td>
        <td width="80%">
            {{ $paymentRequest->code }}
        </td>
    </tr>
    <tr>
        <td>
            TANGGAL
        </td>
        <td>
            :
        </td>
        <td>
            {{ $paymentRequest->date_string }}
        </td>
    </tr>
    <tr>
        <td>
            TIPE RFP
        </td>
        <td>
            :
        </td>
        <td>
            {{ $type }}
        </td>
    </tr>
    <tr>
        <td>
            REQUESTED BY
        </td>
        <td>
            :
        </td>
        <td>
            {{ $paymentRequest->createdBy->name }}
        </td>
    </tr>
    <tr>
        <td>
            CARA PEMBAYARAN
        </td>
        <td>
            :
        </td>
        <td>
            Transfer
        </td>
    </tr>
    <tr>
        <td>
            TOTAL
        </td>
        <td>
            :
        </td>
        <td>
            @if($paymentRequest->type === 'dp')
                Rp {{ !empty($paymentRequest->dp_amount) ? number_format($paymentRequest->dp_amount, 0, ",", ".") : number_format($paymentRequest->total_amount, 0, ",", ".") }}
            @else
                Rp{{ number_format($paymentRequest->total_amount, 0, ",", ".") }}
            @endif
        </td>
    </tr>
</table>

<!-- Keterangan PO / PI -->
<hr style="display: block; height: 1px; background: transparent; width: 100%; border: none; border-top: solid 1px #aaa;" />
<h6>Document</h6>
@php($totalAll = 0)
<table class="table">
    <tr>
        <th>
            No
        </th>
        <th>
            @if($poDetails->count() != 0)
                No PO
            @elseif($piDetails->count() != 0)
                No PI
            @endif
        </th>
        <th>
            No PR
        </th>
        <th>
            Tgl PR
        </th>
        <th>
            Sub Total
        </th>
    </tr>
    @php($no = 1)
    @if($poDetails != null)
        @foreach($poDetails as $detail)
            <tr>
                <td>
                    {{ $no }}
                </td>
                <td>
                    {{ $detail->purchase_order_header->code }}
                </td>
                <td>
                    {{ $detail->purchase_order_header->purchase_request_header->code }}
                </td>
                <td>
                    {{ $detail->purchase_order_header->purchase_request_header->date_string }}
                </td>
                <td>
                    Rp{{ $detail->purchase_order_header->total_payment_string }}
                </td>
            </tr>
            @php($no++)
            @php($totalAll += $detail->purchase_order_header->total_payment)
        @endforeach
    @endif

    @if($piDetails != null)
        @foreach($piDetails as $detail)
            <tr>
                <td>
                    {{ $no }}
                </td>
                <td>
                    {{ $detail->purchase_invoice_header->code }}
                </td>
                <td>
                    {{ $detail->purchase_invoice_header->purchase_order_header->purchase_request_header->code }}
                </td>
                <td>
                    {{ $detail->purchase_invoice_header->purchase_order_header->purchase_request_header->date_string }}
                </td>
                <td>
                    Rp{{ $detail->purchase_invoice_header->total_payment_string }}
                </td>
            </tr>
            @php($no++)
            @php($totalAll += $detail->purchase_invoice_header->total_payment)
        @endforeach
    @endif
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td align="right"><b>Total</b></td>
        <td><b>Rp{{ number_format($totalAll, 0, ",", ".") }}</b></td>
    </tr>
</table>
<hr style="display: block; height: 1px; background: transparent; width: 100%; border: none; border-top: solid 1px #aaa;" />

<h6>Rincian</h6>
<table width="100%" class="table">
    <tr>
        <th>
            NO
        </th>
        <th>
            KETERANGAN
        </th>
        <th>
            JUMLAH
        </th>
    </tr>
    <tr>
        <td>
            1
        </td>
        <td>
            NAMA BANK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $paymentRequest->requester_bank_name }} <br/>
            NO REKENING&nbsp;&nbsp;: {{ $paymentRequest->requester_bank_account }} <br/>
            ATAS NAMA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>{{ $paymentRequest->requester_account_name }}</b>
        </td>
        <td>
            @if($paymentRequest->type === 'dp')
                <b>Rp {{ !empty($paymentRequest->dp_amount) ? number_format($paymentRequest->dp_amount, 0, ",", ".") : number_format($paymentRequest->total_amount, 0, ",", ".") }}</b>
            @else
                <b>Rp {{ number_format($paymentRequest->total_amount, 0, ",", ".") }}</b>
            @endif
        </td>
    </tr>
    <tr>
        <td>

        </td>
        <td align="right">
            <b>TOTAL</b>
        </td>
        <td>
            @if($paymentRequest->type === 'dp')
                <b>Rp {{ !empty($paymentRequest->dp_amount) ? number_format($paymentRequest->dp_amount, 0, ",", ".") : number_format($paymentRequest->total_amount, 0, ",", ".") }}</b>
            @else
                <b>Rp {{ number_format($paymentRequest->total_amount, 0, ",", ".") }}</b>
            @endif
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td width="19%">
            TERBILANG
        </td>
        <td width="1%">
            :
        </td>
        <td width="80%" class="heading" style="background: #e6e9ef;">
            <i><b>{{ $terbilang }}</b></i>
        </td>
    </tr>
    <tr>
        <td width="19%">
            NOTE
        </td>
        <td width="1%">
            :
        </td>
        <td width="80%" align="justify">
            <b>{{ $paymentRequest->note }}</b>
        </td>
    </tr>
</table>
<br/>

<table class="table" border="1" style="font-weight: bold; text-align: center;">
    <tr>
        <td colspan="2">Created by,</td>
        <td>Checked by,</td>
        <td>Checked by,</td>
        <td>Knowledge by,</td>
        <td>Knowledge by,</td>
        <td colspan="2">Approved by,</td>
    </tr>
    <tr>
        <td colspan="2" height="100px;">
            @if(!empty($paymentRequest->createdBy->img_path))
                <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $paymentRequest->createdBy->img_path) }}" >
            @endif
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;{{ $paymentRequest->createdBy->name }}</td>
        <td>&nbsp;Head Department</td>
        <td>&nbsp;Manager Purchasing</td>
        <td>&nbsp;General Manager</td>
        <td>&nbsp;Director</td>
        <td colspan="2">&nbsp;Director</td>
    </tr>
</table>
</div>
</body>