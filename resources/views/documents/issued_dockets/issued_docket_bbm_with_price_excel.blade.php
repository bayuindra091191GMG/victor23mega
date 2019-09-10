<table>
    <thead>
    <tr>
        <th>No.ID</th>
        <th>Tanggal</th>
        <th>Site</th>
        <th>Cost Code</th>
        <th>Kode Inventory</th>
        <th>Unit Alat Berat</th>
        <th>Shift</th>
        <th>Jam</th>
        <th>HM</th>
        <th>KM</th>
        <th>Fuelman</th>
        <th>Operator</th>
        <th>QTY</th>
        <th>Cost</th>
    </tr>
    </thead>
    <tbody>
    @foreach($idHeaders as $header)
{{--        <tr>--}}
{{--            <td>{{ $header->code }}</td>--}}
{{--            <td>{{ $header->date_string }}</td>--}}
{{--            <td>{{ $header->site->name }}</td>--}}
{{--            @php( $costCode = !empty($header->account_id) ? $header->account->code : "Tidak Ada" )--}}
{{--            <td>{{ $costCode }}</td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--        </tr>--}}
        @php( $totalQtyHeader = 0 )
        @php( $totalValueHeader = 0 )
        @foreach($header->issued_docket_details as $detail)
            @if($detail->item_id === $itemId)
                @if($filterMachineryId > -1 && $detail->machinery_id !== $filterMachineryId)
                    @continue
                @endif

                @php( $value = $detail->item->value ?? 0 )
                @php( $subTotalValue = $value * $detail->quantity )
                <tr>
                    <td>{{ $header->code }}</td>
                    <td>{{ $header->date_string }}</td>
                    <td>{{ $header->site->name }}</td>
                    @php( $costCode = !empty($header->account_id) ? $header->account->code : "Tidak Ada" )
                    <td>{{ $costCode }}</td>
                    <td>{{ $detail->item->code }}</td>
                    <td>{{ $detail->machinery->code }}</td>
                    <td>{{ $detail->shift }}</td>
                    <td>{{ $detail->time }}</td>
                    <td>{{ $detail->hm }}</td>
                    <td>{{ $detail->km }}</td>
                    <td>{{ $detail->fuelman }}</td>
                    <td>{{ $detail->operator }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $subTotalValue }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
        <tr>
            <td colspan="12">TOTAL</td>
            <td> {{ $totalQty }}</td>
            <td> {{ $totalValue}}</td>
        </tr>
    </tbody>
</table>