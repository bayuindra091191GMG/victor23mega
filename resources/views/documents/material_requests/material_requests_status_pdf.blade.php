<!DOCTYPE html>
<html lang="en">
<head>
    <title>Material Request Status</title>
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
    <h3>Laporan Material Request</h3>
    <span style="font-size: 10px;">Tanggal: {{ $start_date }} - {{ $finish_date }}</span><br/>
    <span style="font-size: 10px;">Total MR: {{ $mrHeaders->count() }}</span>
    <table class="table nowrap" style="font-size: 10px;">
        <thead>
        <tr>
            <th class="text-center">No. MR</th>
            <th class="text-center">No. PR </th>
            <th class="text-center">No. PO</th>
            <th class="text-center">No. GR</th>
            <th class="text-center">Lead Time GR</th>
            <th class="text-center">No. SJ</th>
            <th class="text-center">Status. SJ</th>
            <th class="text-center">Lead Time SJ</th>
            {{--<th class="text-center">No. ID</th>--}}
        </tr>
        {{--<tr>--}}
        {{--<th class="text-center">Nomor PR</th>--}}
        {{--<th class="text-center">Tanggal</th>--}}
        {{--<th class="text-center">Departemen</th>--}}
        {{--<th class="text-center">Status</th>--}}
        {{--</tr>--}}
        </thead>
        <tbody>
        @foreach($mrHeaders as $mr)
            <tr>
                <td class="text-left">{{ $mr->code }} - {{ $mr->date_string }}</td>
                <td class="text-left">
                    @if(empty($mr->purchase_request_headers->first()))
                        -
                    @else
                        {{ $mr->purchase_request_headers->first()->code }}
                    @endif
                </td>
                @php( $poHeaders = $mr->purchase_request_headers->first()->purchase_order_headers ?? null )
                <td class="text-left">
                    @php( $grHeaders = new \Illuminate\Database\Eloquent\Collection() )
                    @if(empty($poHeaders) || $poHeaders->count() == 0)
                        -
                    @else
                        @foreach($poHeaders as $po)
                            {{ $po->code }}<br/>
                            @foreach($po->item_receipt_headers as $grHeader)
                                @php( $grHeaders->add($grHeader) )
                            @endforeach
                        @endforeach
                    @endif
                </td>
                <td class="text-left">
                    @php( $sjHeaders = new \Illuminate\Database\Eloquent\Collection() )
                    @if($grHeaders->count() == 0)
                        -
                    @else
                        @foreach($grHeaders as $gr)
                            {{ $gr->code }} - {{ $gr->date_string }}<br/>
                            @foreach($gr->delivery_order_headers as $sjHeader)
                                @php( $sjHeaders->add($sjHeader) )
                            @endforeach
                        @endforeach
                    @endif
                </td>
                <td class="text-center">
                    @if($grHeaders->count() == 0)
                        -
                    @else
                        @foreach($grHeaders as $gr)
                            @if(!empty($gr->lead_time))
                                {{ $gr->lead_time }} Hari<br/>
                            @else
                                @php( $mrDate = \Carbon\Carbon::parse($mr->date) )
                                @php( $grDate = \Carbon\Carbon::parse($gr->date) )
                                {{ $mrDate->diffInDays($grDate) }} Hari<br/>
                            @endif
                        @endforeach
                    @endif
                </td>
                <td class="text-left">
                    @if($sjHeaders->count() == 0)
                        -
                    @else
                        @foreach($sjHeaders as $sj)
                            {{ $sj->code }} - {{ $sj->date_string }}<br/>
                        @endforeach
                    @endif
                </td>
                <td class="text-center">
                    @if($sjHeaders->count() == 0)
                        -
                    @else
                        @foreach($sjHeaders as $sj)
                            {{ strtoupper($sj->status->description) }}<br/>
                        @endforeach
                    @endif
                </td>
                <td class="text-center">
                    @if($sjHeaders->count() == 0)
                        -
                    @else
                        @foreach($sjHeaders as $sj)
                            @if($sj->status_id === 4)
                                @if(!empty($sj->lead_time))
                                    {{ $sj->lead_time }} Hari<br/>
                                @else
                                    @php( $mrDate = \Carbon\Carbon::parse($mr->date) )
                                    @php( $sjDate = \Carbon\Carbon::parse($sj->confirm_date) )
                                    {{ $mrDate->diffInDays($sjDate) }} Hari<br/>
                                @endif
                            @else
                                -
                            @endif
                        @endforeach
                    @endif
                </td>
                {{--<td class="text-center">--}}
                    {{--@if($mr->issued_docket_headers->count() == 0)--}}
                        {{-----}}
                    {{--@else--}}
                        {{--@foreach($mr->issued_docket_headers as $id)--}}
                            {{--{{ $id->code }} - {{ $id->date_string }}<br/><br/>--}}
                        {{--@endforeach--}}
                    {{--@endif--}}
                {{--</td>--}}
                {{--<td class="text-left">--}}
                    {{--@if($mr->issued_docket_headers->count() == 0)--}}
                        {{-----}}
                    {{--@else--}}
                        {{--@foreach($mr->issued_docket_headers as $id)--}}
                            {{--{{ $id->code }} - {{ $id->date_string }}<br/>--}}
                        {{--@endforeach--}}
                    {{--@endif--}}
                {{--</td>--}}
            </tr>
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
