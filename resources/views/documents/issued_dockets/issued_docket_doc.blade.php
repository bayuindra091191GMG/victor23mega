<!DOCTYPE html>
<html lang="en">
<head>
    <title>Issued Docket Document</title>
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
			<img width=204 height=42 src="http://bayu159753.com/public/assets/images/image001.png">
		</span>
    <br/>
    <h4 style="text-align: center;"><b><u>Issued Docket</u></b></h4>

    <table>
        <tr>
            <td width="50%">
                No. ID
            </td>
            <td>
                : {{ $header->code }} - {{ $header->date_string }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Department
            </td>
            <td>
                : {{ $header->department->name }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Gudang Pengambilan
            </td>
            <td>
                : {{ $header->warehouse->name }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Unit Code
            </td>
            <td>
                : {{ $header->machinery->code ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                HM
            </td>
            <td>
                : {{ $header->hm }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                KM
            </td>
            <td>
                : {{ $header->km }}
            </td>
        </tr>
    </table>
    <br/>

    <table class="table" border="1">
        <thead>
        <tr align="center">
            <td><b>NO<br/>(No)</b></td>
            <td width="20%"><b>CODE<br/>(Kode Part)</b></td>
            <td width="20%"><b>PART NUMBER<br/>(Nomor Part)</b></td>
            <td width="20%"><b>DESCRIPTION<br/>(Uraian)</b></td>
            <td width="10%"><b>UNIT<br/>(Satuan)</b></td>
            <td width="10%"><b>QTY<br/>(Jumlah)</b></td>
            <td width="20%"><b>REMARKS<br/>(Keterangan)</b></td>
        </tr>
        </thead>
        <tbody>
        @php($i=1)
        @foreach($header->issued_docket_details as $detail)
            <tr align="center">
                <td>{{ $i }}</td>
                <td>{{ $detail->item->code }}</td>
                <td>{{ $detail->item->part_number }}</td>
                <td>{{ $detail->item->name }}</td>
                <td>{{ $detail->item->uom }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->remark }}</td>
            </tr>
            @php($i++)
        @endforeach
        </tbody>
    </table>

    <table class="table" border="0" style="font-weight: bold; text-align: center;">
        <tr>
            <td colspan="2">Diminta Oleh,</td>
            <td colspan="2">Disetujui Oleh,</td>
            <td>Diketahui Oleh,</td>
            <td>Dikeluarkan Oleh</td>
        </tr>
        <tr>
            <td colspan="2" height="80px;">
                @if(!empty($header->createdBy->img_path))
                    <img style="height: 70px; width: auto;" src="{{ URL::asset('/storage/img_sign/'. $header->createdBy->img_path) }}" >
                @endif
            </td>
            <td colspan="2">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" height="20px;">{{ $header->createdBy->name }}</td>
            <td colspan="2">Head Department</td>
            <td>Project Manager</td>
            <td>Warehouse</td>
        </tr>
    </table>
</div>

</body>
</html>
