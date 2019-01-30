<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase Order Document</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container" style="width: 750px;">
	<span style='z-index:5;margin-left:0px;margin-top:16px;width:204px;
	height:42px'>
		<img width=204 height=42 src="{{ URL::asset('assets/images/image001.png') }}">
	</span>
    <br/>
    <table width="100%" style="font-size: 10px;">
        <tr>
            <td width="70%"><b>PT. Victor Dua Tiga Mega</b></td>
            <td style="font-size: 18px; text-align: center;"><b>Purchase Order</b></td>
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
            <td width="55%">To: {{ $purchaseOrder->supplier->name }}</td>
            <td width="11%">No PO</td>
            <td>: {{ $purchaseOrder->code }}</td>
        </tr>
    </table>

    <table width="50%" border="1" style="float: left;">
        <tr>
            <td height="120px">
                Attn: {{ $purchaseOrder->supplier->address }}
                <br/>
                Telp: {{ $purchaseOrder->supplier->phone1 }}
            </td>
        </tr>
    </table>
    <table width="45%" style="float: right;">
        <tr>
            <td>Tgl PO</td>
            <td>: {{ $purchaseOrder->date_string }}</td>
        </tr>
        <tr>
            <td>No PR</td>
            <td>: {{ $purchaseOrder->purchase_request_header->code }}</td>
        </tr>
        <tr>
            <td>Tgl PR</td>
            <td>: {{ $purchaseOrder->purchase_request_header->date_string }}</td>
        </tr>
        <tr>
            <td>No MR</td>
            <td>: {{ $purchaseOrder->purchase_request_header->material_request_header->code }}</td>
        </tr>
        <tr>
            <td>Tgl MR</td>
            <td>: {{ $purchaseOrder->purchase_request_header->material_request_header->date_string }}</td>
        </tr>
        {{--<tr>--}}
            {{--<td>No RFQ</td>--}}
            {{--<td>--}}
                {{--:--}}
                {{--@if($purchaseOrder->quotation_id != null)--}}
                    {{--{{ $purchaseOrder->quotation_header->code }}--}}
                {{--@else--}}
                    {{-----}}
                {{--@endif--}}
            {{--</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td>Tgl RFQ</td>--}}
            {{--<td>--}}
                {{--:--}}
                {{--@if($purchaseOrder->quotation_id != null)--}}
                    {{--{{ $purchaseOrder->quotation_header->date_string }}--}}
                {{--@else--}}
                    {{-----}}
                {{--@endif--}}
            {{--</td>--}}
        {{--</tr>--}}
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
        @foreach($purchaseOrder->purchase_order_details as $detail)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td class="text-center">{{ $detail->item->code }}</td>
                <td class="text-center">{{ $detail->item->name }}</td>
                <td class="text-center">{{ $detail->quantity }}</td>
                <td class="text-center">{{ $detail->item->uom ?? '-' }}</td>
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

            @if($mrType === 4)
                <td colspan="2">Keterangan Servis</td>
                <td colspan="3">{{ $purchaseOrder->purchase_order_details->first()->remark }}</td>
            @else
                <td colspan="2"></td>
                <td colspan="3"></td>
            @endif

            <td colspan="2">Sub Total</td>
            <td class="text-right">{{ $purchaseOrder->total_payment_before_tax_string }}</td>
        </tr>
        <tr>
            <td colspan="2">Packing</td>
            <td colspan="3">&nbsp;</td>
            <td colspan="2">Diskon</td>
            <td class="text-right">{{ !empty($purchaseOrder->extra_discount_string) ? '- '. $purchaseOrder->extra_discount_string : '0' }}</td>
        </tr>
        <tr>
            <td colspan="2">Delivery Time</td>
            <td colspan="3">&nbsp;</td>
            <td colspan="2">Delivery Charge</td>
            <td class="text-right">{{ !empty($purchaseOrder->delivery_fee_string) ? '+ '. $purchaseOrder->delivery_fee_string : '0' }}</td>
        </tr>
        <tr>
            <td colspan="2">Delivery Place</td>
            <td colspan="3">&nbsp;</td>
            <td colspan="2">PPh ps 23</td>
            <td class="text-right">{{ !empty($purchaseOrder->pph_amount) ? '- '. $purchaseOrder->pph_string : '0'}}</td>
        </tr>
        <tr>
            <td colspan="2">Payment Term</td>
            <td colspan="3">{{ $purchaseOrder->payment_term ?? '-' }} Hari</td>
            <td colspan="2">PPN 10%</td>
            <td class="text-right">{{ !empty($purchaseOrder->ppn_amount) ? '+ '. $purchaseOrder->ppn_string : '0'}}</td>
        </tr>
        <tr>
            <td colspan="2">Special Note</td>
            <td colspan="3">{{ $purchaseOrder->special_note ?? '-' }}</td>
            <td colspan="2">After Tax Total</td>
            <td class="text-right">{{ $purchaseOrder->total_payment_string }}</td>
        </tr>
        {{--<tr>--}}
            {{--<td colspan="5" rowspan="5" align="left">--}}
                {{--Packing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <br/><br/>--}}
                {{--Delivery Time&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <br/><br/>--}}
                {{--Delivery Place&nbsp;&nbsp;&nbsp;&nbsp;: <br/><br/>--}}
                {{--Payment Term&nbsp;&nbsp;&nbsp;&nbsp;: <br/><br/>--}}
                {{--Special Note&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:--}}
            {{--</td>--}}
            {{--<td rowspan="4" align="left">--}}
                {{--Sub Total <br/><br/>--}}
                {{--Delivery Charge <br/><br/>--}}
                {{--PPh ps 23<br/><br/>--}}
                {{--PPN 10%--}}
            {{--</td>--}}
            {{--<td align="left" height="10px"><b>9.000.000</b></td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td height="10px">20.000</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td height="10px">100.000</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td height="10px">900.000</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td><b>After Tax Total</b></td>--}}
            {{--<td>10.020.000</td>--}}
        {{--</tr>--}}
    </table>

    <br/>
    {{--<table class="table" border="1" style="font-size: 11px; font-weight: bold; text-align: center;">--}}
        {{--<tr>--}}
            {{--<td>Created by,</td>--}}
            {{--<td>Approved by,</td>--}}
            {{--<td>Approved by,</td>--}}
            {{--<td>Approved by,</td>--}}
            {{--<td>Approved by,</td>--}}
            {{--<td>Approved by,</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td height="80px;"></td>--}}
            {{--<td>&nbsp;</td>--}}
            {{--<td>&nbsp;</td>--}}
            {{--<td>&nbsp;</td>--}}
            {{--<td>&nbsp;</td>--}}
            {{--<td>&nbsp;</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td>&nbsp;</td>--}}
            {{--<td>&nbsp;Head Department</td>--}}
            {{--<td>&nbsp;Manager</td>--}}
            {{--<td>&nbsp;General Manager</td>--}}
            {{--<td>&nbsp;Direktur</td>--}}
            {{--<td>&nbsp;Direktur</td>--}}
        {{--</tr>--}}
    {{--</table>--}}
    <table class="table" border="1" style="font-weight: bold; text-align: center; font-size: 12px;">
        <tr>
            <td style="width: 16%">Created by,</td>
            @if($approvals->count() > 0)
                @foreach($approvals as $approval)
                    <td style="width: 16%">Approved by,</td>
                @endforeach
            @else
                <td style="width: 16%">Approved by,</td>
                <td style="width: 16%">Approved by,</td>
                <td style="width: 16%">Approved by,</td>
                <td style="width: 16%">Approved by,</td>
                <td style="width: 16%">Approved by,</td>
            @endif

        </tr>
        <tr>
            <td height="80px;">
                @if(!empty($purchaseOrder->createdBy->img_path))
                    <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $purchaseOrder->createdBy->img_path) }}" >
                @endif
            </td>
            @if($approvals->count() > 0)
                {{--@for($i = 0; $i<4; $i++)--}}
                    {{--<td>--}}
                        {{--@if(!empty($approvals[$i]->user->img_path))--}}
                            {{--<img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $approvals[$i]->user->img_path) }}" >--}}
                        {{--@else--}}
                            {{--&nbsp;--}}
                        {{--@endif--}}
                    {{--</td>--}}
                {{--@endfor--}}
                @foreach($approvals as $approval)
                    @if(!empty($approval->user->img_path))
                        <td>
                            <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $approval->user->img_path) }}" >
                        </td>
                    @endif
                @endforeach
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr>
            {{--<td>{{ $purchaseOrder->createdBy->name }}</td>--}}
            <td>Purchasing Staff</td>
            @if($approvals->count() > 0)
                {{--@for($u = 0; $u<4; $u++)--}}
                    {{--<td>--}}
                        {{--{{ $approvals[$u]->user->name }}--}}
                        {{--{{ $approvals[$u]->user->roles->pluck('name')->implode(',') }}--}}
                    {{--</td>--}}
                {{--@endfor--}}
                @foreach($approvals as $approval)
                    <td>{{ $approval->user->roles->pluck('name')->implode(',') }}</td>
                @endforeach
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
    </table>
</div>

</body>
</html>
