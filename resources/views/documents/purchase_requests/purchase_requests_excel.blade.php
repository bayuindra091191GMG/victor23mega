<table>
    <thead>
    <tr>
        <th>No PR</th>
        <th>Tanggal</th>
        <th>No MR</th>
        <th>Departemen</th>
        <th>Status</th>
        <th>Pembuat Dokumen</th>
        <th>Kode Inventory</th>
        <th>Part Number Inventory</th>
        <th>Keterangan Inventory</th>
        <th>QTY</th>
        <th>UOM</th>
    </tr>
    </thead>
    <tbody>
    @php( $i = 1 )
    @php( $flag = 0 )
    @foreach($prHeaders as $header)
        @foreach($header->purchase_request_details as $detail)
            <tr>
                @if($flag === 0)
                    <td>{{ $header->code }}</td>
                    <td>{{ $header->date_string }}</td>
                    <td>{{ $header->material_request_header->code }}</td>
                    <td>{{ $header->department->name }}</td>
                    <td>{{ $header->status->description }}</td>
                    <td>{{ $header->createdBy->email }} - {{ $header->createdBy->employee->name }}</td>
                    @php( $flag++ )
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
                <td>{{ $detail->item->code }}</td>
                <td>{{ $detail->item->part_number ?? '-' }}</td>
                <td>{{ $detail->item->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->item->uom }}</td>
            </tr>
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
        </tr>
        @php( $flag = 0 )
        @php( $i++ )
    @endforeach
    </tbody>
</table>