<table>
    <thead>
    <tr>
        <th>No.ID</th>
        <th>Tanggal</th>
        <th>Kode Inventory</th>
        <th>Part Number</th>
        <th>Keterangan</th>
        <th>Unit Alat Berat</th>
        <th>UOM</th>
        <th>QTY</th>
        <th>COST</th>
    </tr>
    </thead>
    <tbody>
    @foreach($idHeaders as $header)
        <tr>
            <td>{{ $header->code }}</td>
            <td>{{ $header->date_string }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @php( $totalValueHeader = 0 )
        @foreach($header->issued_docket_details as $detail)
            @if($filterMachineryId > -1 && $detail->machinery_id !== $filterMachineryId)
                @continue
            @endif

            @php( $value = $detail->item->value ?? 0 )
            @php( $subTotalValue = $value * $detail->quantity )
            <tr>
                <td></td>
                <td></td>
                <td>{{ $detail->item->code }}</td>
                <td>{{ $detail->item->part_number }}</td>
                <td>{{ $detail->item->name }}</td>
                <td>{{ $detail->machinery->code ?? '-' }}</td>
                <td>{{ $detail->item->uom }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $subTotalValue }}</td>
            </tr>
            @php( $totalValueHeader += $subTotalValue )
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
        </tr>
    @endforeach
        <tr>
            <td colspan="8">TOTAL COST</td>
            <td>{{ $totalValue }}</td>
        </tr>
    </tbody>
</table>