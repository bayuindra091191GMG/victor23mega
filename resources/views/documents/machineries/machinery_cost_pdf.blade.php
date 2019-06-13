<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase Order Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('documents.partials._pdf_head')

    <style>
        .table>tbody>tr>td{
            padding: 2px;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Laporan Cost Alat Berat</h3>
    <span style="font-size: 10px;">Tanggal: {{ $start_date }} - {{ $finish_date }}</span><br/>
    <span style="font-size: 10px;">Alat Berat: {{ $machinery->code }} ({{ $machinery->machinery_category->name }} - {{ $machinery->machinery_brand->name }})</span><br/>
    <span style="font-size: 10px;">Total ID : {{ $idHeaders->count() }}</span>
    <table class="table" style="font-size: 10px;">
        <thead>
        <tr>
            <th class="text-center" style="width: 15%;">Issued Docket</th>
            <th class="text-center" style="width: 15%;">Tanggal</th>
            <th class="text-center" style="width: 10%;">Kode</th>
            <th class="text-center" style="width: 20%;">Nama</th>
            <th class="text-center" style="width: 10%;">QTY</th>
            <th class="text-center" style="width: 15%;">Harga</th>
            <th class="text-center" style="width: 15%;">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="7">COST SPARE PART</td>
        </tr>
        @php($i=1)
        @foreach($idHeaders as $header)
            @foreach($header->issued_docket_details as $detail)
                @if($detail->quantity_retur < $detail->quantity)
                    <tr>
                        <td class="text-center">{{ $header->code }}</td>
                        <td class="text-center">{{ $header->date_string }}</td>
                        <td class="text-center">{{ $detail->item->code }}</td>
                        <td class="text-center">{{ $detail->item->name }}</td>
                        <td class="text-center">{{ $detail->quantity }} {{ $detail->item->uom }}</td>
                        <td class="text-right">{{ $detail->item_value_str }}</td>
                        <td class="text-right">{{ $detail->subtotal_value_str }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
        <tr>
            <td colspan="7">COST BBM</td>
        </tr>
        @foreach($idDetailFuels as $detailFuel)
            @if($detailFuel->quantity_retur < $detailFuel->quantity)
                <tr>
                    <td class="text-center">{{ $detailFuel->issued_docket_header->code }}</td>
                    <td class="text-center">{{ $detailFuel->issued_docket_header->date_string }}</td>
                    <td class="text-center">{{ $detailFuel->item->code }}</td>
                    <td class="text-center">{{ $detailFuel->item->name }}</td>
                    <td class="text-center">{{ $detailFuel->quantity }} {{ $detailFuel->item->uom }}</td>
                    <td class="text-right">{{ $detailFuel->item_value_str }}</td>
                    <td class="text-right">{{ $detailFuel->subtotal_value_str }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan="6" class="text-right">
                <b>Total Semua Cost</b>
            </td>
            <td class="text-right">
                {{ $total }}
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/php">
    if ( isset($pdf) ) {
        // OLD
        // $font = Font_Metrics::get_font("helvetica", "bold");
        // $pdf->page_text(72, 18, "{PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(255,0,0));
        // v.0.7.0 and greater
        $x = 520;
        $y = 800;
        $text = "{PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->get_font("helvetica", "bold");
        $size = 8;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
</body>
</html>
