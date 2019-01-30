<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase Request Document</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container" style="font-family: 'Times New Roman', Times, serif; width: 800px;">
		<span style='position:absolute;z-index:5;margin-left:0px;margin-top:16px;width:204px;
		height:42px'>
			<img width=204 height=42 src="{{ URL::asset('assets/images/image001.png') }}">
		</span>
    <br/>
    <h4 style="text-align: center;"><b><u>Purchase Request</u></b></h4>
    <h5 style="text-align: center; margin-top: -10px;">(Permintaan Pembelian)</h5>

    <table>
        <tr>
            <td width="50%">
                No. PR/Date
            </td>
            <td>
                : {{ $purchaseRequest->code }} - {{ $purchaseRequest->date_string }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                No. MR/Date
            </td>
            <td>
                : {{ $purchaseRequest->material_request_header->code }} - {{ $purchaseRequest->material_request_header->date_string }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Department
            </td>
            <td>
                : {{ $purchaseRequest->department->name }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Prioritas
            </td>
            <td>
                : {{ $purchaseRequest->priority }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Unit Type
            </td>
            <td>
                : {{ $purchaseRequest->machinery->machinery_type_name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Unit Code
            </td>
            <td>
                : {{ $purchaseRequest->machinery->code ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                S/N Chasis
            </td>
            <td>
                : {{ $purchaseRequest->machinery->sn_chasis ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                S/N Engine
            </td>
            <td>
                : {{ $purchaseRequest->machinery->sn_engine ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                HM
            </td>
            <td>
                :
                @if($purchaseRequest->hm != null)
                    {{ $purchaseRequest->hm }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td width="50%">
                KM
            </td>
            <td>
                :
                @if($purchaseRequest->km != null)
                    {{ $purchaseRequest->km }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>
    <br/>

    <table class="table" border="1">
        <thead>
        <tr align="center">
            <td width="10%"><b>NO<br/>(No)</b></td>
            <td width="15%"><b>PART NUMBER<br/>(Nomor Part)</b></td>
            <td width="25%"><b>DESCRIPTION<br/>(Uraian)</b></td>
            <td width="10%"><b>QTY<br/>(Jumlah)</b></td>
            <td width="10%"><b>UNIT<br/>(Satuan)</b></td>
            <td width="30%"><b>REMARKS<br/>(Keterangan)</b></td>
        </tr>
        </thead>
        <tbody>
            @php($i=1)
            @foreach($purchaseRequestDetails as $detail)
                <tr>
                    <td align="center">{{ $i }}</td>
                    <td>{{ $detail->item->part_number ?? "-" }}</td>
                    <td>{{ $detail->item->name }}</td>
                    <td align="right">{{ $detail->quantity }}</td>
                    <td align="center">{{ $detail->item->uom }}</td>
                    <td>{{ $detail->remark }}</td>
                </tr>
                @php($i++)
            @endforeach
        </tbody>
    </table>

    <table class="table" border="0" style="font-weight: bold; text-align: center;">
        <tr>
            <td>Requested by,</td>
            <td style="border-top: none !important; border-bottom: none !important;"></td>
            <td style="border-top: none !important; border-bottom: none !important;"></td>
            <td style="border-top: none !important; border-bottom: none !important;"></td>
            <td colspan="2">Approved by,</td>
        </tr>
        <tr>
            <td height="80px;">
                @if(!empty($purchaseRequest->createdBy->img_path))
                    <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $purchaseRequest->createdBy->img_path) }}" >
                @endif
            </td>
            <td style="border-top: none !important; border-bottom: none !important;">&nbsp;</td>
            <td style="border-top: none !important; border-bottom: none !important;">&nbsp;</td>
            <td style="border-top: none !important; border-bottom: none !important;">&nbsp;</td>
            {{--<td>--}}
                {{--@if($setting == 1)--}}
                    {{--<img src="{{ URL::asset('storage/img_sign/'.$approvalUser[0]->user->img_path) }}"  width="100px"/>--}}
                {{--@else--}}
                    {{--&nbsp;--}}
                {{--@endif--}}
            {{--</td>--}}
            <td colspan="2">
                @if(!empty($approvalUser->img_path))
                    <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $approvalUser->img_path) }}" >
                @endif
            </td>
        </tr>
        <tr>
            <td height="20px;">{{ $purchaseRequest->createdBy->name }}</td>
            <td style="border-top: none !important; border-bottom: none !important;">&nbsp;</td>
            <td style="border-top: none !important; border-bottom: none !important;">&nbsp;</td>
            <td style="border-top: none !important; border-bottom: none !important;">&nbsp;</td>
            {{--<td>--}}
                {{--@if($setting == 1)--}}
                    {{--{{ $approvalUser[0]->user->name }}--}}
                {{--@else--}}
                    {{--&nbsp;--}}
                {{--@endif--}}
            {{--</td>--}}
            <td colspan="2">Andri</td>
        </tr>
    </table>
</div>

</body>
</html>
