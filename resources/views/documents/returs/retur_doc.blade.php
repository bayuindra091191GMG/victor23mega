<!DOCTYPE html>
<html lang="en">
<head>
    <title>Retur Document</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container" style="width: 680px;">
	<span style='z-index:5;margin-left:0px;margin-top:16px;width:204px;
	height:42px'>
		<img width=204 height=42 src="{{ URL::asset('assets/images/image001.png') }}">
	</span>
    <br/>
    <table width="100%" style="font-size: 10px;">
        <tr>
            <td width="70%"><b>PT. Victor Dua Tiga Mega</b></td>
            <td style="font-size: 18px; text-align: center;"><b>Retur</b></td>
        </tr>
        <tr>
            <td>Rukan Arta Gading Niaga Blok F No. 19</td>
        </tr>
        <tr>
            <td>Kelapa Gading , Jakarta utara, 14240</td>
        </tr>
        <tr>
            <td>Tlp. 021-45860888, Fax: 021-45849888</td>
        </tr>
        <tr>
            <td><b>NPWP : 02.062.623.0-018.000</b></td>
        </tr>
    </table>
    <br/>

    <table width="100%">
        <tr>
            <td width="55%">To: {{ $retur->purchase_invoice_header->purchase_order_header->supplier->name }}</td>
            <td width="11%">No Retur</td>
            <td>: {{ $retur->code }}</td>
        </tr>
    </table>

    <table width="50%" border="1" style="float: left;">
        <tr>
            <td height="120px">
                Attn:
                <br/>
                Telp: {{ $retur->purchase_invoice_header->purchase_order_header->supplier->phone }}
            </td>
        </tr>
    </table>
    <table width="45%" style="float: right;">
        <tr>
            <td width="25%">Tanggal</td>
            <td>: {{ $retur->date_string }}</td>
        </tr>
        <tr>
            <td>No Invoice</td>
            <td>: {{ $retur->purchase_invoice_header->code }}</td>
        </tr>
        <tr>
            <td>Tgl Invoice</td>
            <td>: {{ $retur->purchase_invoice_header->date_string }}</td>
        </tr>
    </table>

    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <h5>Please supply the following items.</h5>
    <table class="table" border="1" width="100%" style="font-size: 11px;">
        <tr>
            <th class="text-center" width="10%">No</th>
            <th class="text-center" width="15%">Part Number</th>
            <th class="text-center" width="20%">Nama Inventory</th>
            <th class="text-center" width="10%">QTY</th>
            <th class="text-center" width="10%">UOM</th>
            <th class="text-center" width="10%">Harga</th>
            <th class="text-center" width="10%">Diskon</th>
            <th class="text-center" width="15%">Subtotal</th>
        </tr>
        @php($i = 1)
        @foreach($retur->retur_details as $detail)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td class="text-center">{{ $detail->item->code }}</td>
                <td class="text-center">{{ $detail->item->name }}</td>
                <td class="text-center">{{ $detail->quantity }}</td>
                <td class="text-center">{{ $detail->item->uom }}</td>
                <td class="text-right">{{ $detail->price_string }}</td>
                <td class="text-center">{{ !empty($detail->discount) ? $detail->discount_string : '0' }}</td>
                <td class="text-right">{{ $detail->subtotal_string }}</td>
            </tr>
            @php($i++)
        @endforeach
        @if($i < 6)
            @for($a = 0; $a < 6-$i; $a++)
                <tr>
                    <td>&nbsp;</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
            @endfor
        @endif

        <tr>
            <td colspan="2">Packing</td>
            <td colspan="3">&nbsp;</td>
            <td colspan="2">Sub Total</td>
            <td class="text-right">{{ $retur->total_price_string }}</td>
        </tr>
        <tr>
            <td colspan="2">Delivery Time</td>
            <td colspan="3">&nbsp;</td>
            <td colspan="2">Diskon</td>
            <td class="text-right">{{ !empty($retur->extra_discount_string) ? '- '. $retur->extra_discount_string : '0' }}</td>
        </tr>
        <tr>
            <td colspan="2">Delivery Place</td>
            <td colspan="3">&nbsp;</td>
            <td colspan="2">Delivery Charge</td>
            <td class="text-right">{{ !empty($retur->delivery_fee_string) ? '+ '. $retur->delivery_fee_string : '0' }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="3"></td>
            <td colspan="2">PPN 10%</td>
            <td class="text-right">{{ !empty($retur->ppn_amount) ? '+ '. $retur->ppn_string : '0'}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="3"></td>
            <td colspan="2">After Tax Total</td>
            <td class="text-right">{{ $retur->total_payment_string }}</td>
        </tr>
    </table>

    <br/>
    <table class="table" border="1" style="font-weight: bold; text-align: center;">
        <tr>
            <td>Prepared by,</td>
            {{--<td>Checked by,</td>--}}
            <td>Approved by,</td>
            <td>Approved by,</td>
        </tr>
        <tr>
            <td height="80px;" width="30%">&nbsp;</td>
            {{--<td>&nbsp;</td>--}}
            <td width="30%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
        </tr>
        <tr>
            <td>{{ $retur->createdBy->name }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $now }}</td>
            <td>{{ $now }}</td>
            <td>{{ $now }}</td>
        </tr>
    </table>
</div>

</body>
</html>
