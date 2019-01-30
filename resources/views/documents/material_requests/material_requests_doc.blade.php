<!DOCTYPE html>
<html lang="en">
<head>
    <title>Material Request Document</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>

<div class="container" style="font-family: 'Times New Roman', Times, serif; width: 900px;">
		<span style='position:absolute;z-index:5;margin-left:0px;margin-top:16px;width:204px;
		height:42px'>
			<img width=204 height=42 src="{{ URL::asset('assets/images/image001.png') }}">
		</span>
    <br/>
    <h4 style="text-align: center;"><b><u>Material Request</u></b></h4>
    <h5 style="text-align: center; margin-top: -10px;">(Permintaan Barang)</h5>

    <table>
        <tr>
            <td width="50%">
                No. MR/Date
            </td>
            <td>
                : {{ $materialRequest->code }} {{ $materialRequest->date_string }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Departemen
            </td>
            <td>
                : {{ $materialRequest->department->name }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Prioritas
            </td>
            <td>
                : {{ $materialRequest->priority }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Unit Type
            </td>
            <td>
                : {{ $materialRequest->machinery->machinery_type_name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Unit Code
            </td>
            <td>
                : {{ $materialRequest->machinery->code ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                S/N Chasis
            </td>
            <td>
                : {{ $materialRequest->machinery->sn_chasis ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                S/N Engine
            </td>
            <td>
                : {{ $materialRequest->machinery->sn_engine ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                HM
            </td>
            <td>
                : {{ $materialRequest->hm ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                KM
            </td>
            <td>
                : {{ $materialRequest->km ?? '-' }}
            </td>
        </tr>
    </table>
    <br/>

    <table class="table" border="1">
        <thead>
        <tr align="center">
            <td width="10%"><b>NO</b></td>
            <td width="15%"><b>KODE</b></td>
            <td width="15%"><b>PART NUMBER</b></td>
            <td width="20%"><b>DESCRIPTION<br/>(Uraian)</b></td>
            <td width="10%"><b>QTY<br/>(Jumlah)</b></td>
            <td width="10%"><b>UNIT<br/>(Satuan)</b></td>
            <td width="20%"><b>REMARKS<br/>(Keterangan)</b></td>
        </tr>
        </thead>
        <tbody>
        @php($i=1)
        @foreach($materialRequest->material_request_details as $detail)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td>{{ $detail->item->code }}</td>
                <td>{{ $detail->item->part_number }}</td>
                <td>{{ $detail->item->name }}</td>
                <td class="text-right">{{ $detail->quantity }}</td>
                <td class="text-center">{{ $detail->item->uom }}</td>
                <td>{{ $detail->remark ?? '-' }}</td>
            </tr>
            @php($i++)
        @endforeach
        </tbody>
    </table>

    <table class="table" border="1" style="font-weight: bold; text-align: center;">
        <tr>
            <td>Requested by,</td>
            <td>Checked by,</td>
            <td>Checked by,</td>
            <td>Knowledge by,</td>
            <td colspan="2">Approved by,</td>
        </tr>
        <tr>
            <td height="80px;">
                @if(!empty($materialRequest->createdBy->img_path))
                    <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $materialRequest->createdBy->img_path) }}" >
                @endif
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2">
                @if($isApproved && !empty($approvedBy->img_path))
                    <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $approvedBy->img_path) }}" >
                @endif
            </td>
        </tr>
        <tr>
            <td>&nbsp;{{ $materialRequest->createdBy->name }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;{{ $approvedBy->name ?? '' }}</td>
        </tr>
    </table>
</div>

</body>
</html>
