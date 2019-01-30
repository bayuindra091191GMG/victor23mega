<!DOCTYPE html>
<html lang="en">
<head>
 <title>Delivery Order Report</title>
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
 <h3>Laporan Surat Jalan</h3>
    <span style="font-size: 12px;">Tanggal: {{ $start_date }} - {{ $finish_date }}</span><br/>
    <span style="font-size: 12px;">Total Surat Jalan: {{ $data->count() }}</span>
 <table class="table" style="font-size: 11px;">
  <thead>
  <tr>
      <th class="text-center">Kode</th>
      <th class="text-center">Keterangan</th>
      <th class="text-center">Part Number</th>
      <th class="text-center">QTY</th>

       {{--<th>Code</th>--}}
       {{--<th>No PR</th>--}}
       {{--<th>From</th>--}}
       {{--<th>To</th>--}}
       {{--<th>Alat Berat</th>--}}
       {{--<th>Keterangan</th>--}}
       {{--<th>Status</th>--}}
       {{--<th width="10%">Date</th>--}}
       {{--<th width="10%">Dibuat Oleh</th>--}}
  </tr>
  </thead>
  <tbody>
    @foreach($doHeaders as $header)
        <tr>
            <td colspan="4"><b>{{ $header->code }} - {{ $header->date_string }} - Site: {{ $header->fromWarehouse->site->name }} ke {{ $header->toWarehouse->site->name }} - Status: {{ $header->status->description }}</b></td>
        </tr>
        @foreach($header->delivery_order_details as $detail)
            <tr>
                <td class="text-center">{{ $detail->item->code }}</td>
                <td class="text-center">{{ str_limit($detail->item->name, 15) }}</td>
                <td class="text-center">{{ $detail->part_number ?? '-' }}</td>
                <td class="text-center">{{ $detail->quantity }} {{ $detail->item->uom }}</td>
            </tr>
        @endforeach
    @endforeach
  </tbody>
 </table>
</div>
<script type="text/php">
    if ( isset($pdf) ) {
        // OLD
        // $font = Font_Metrics::get_font("helvetica", "bold");
        // $pdf->page_text(72, 18, "{PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(255,0,0));
        // v.0.7.0 and greater
        $x = 770;
        $y = 550;
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
