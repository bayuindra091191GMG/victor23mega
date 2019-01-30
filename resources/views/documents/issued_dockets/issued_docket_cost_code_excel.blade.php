<table>
    <thead>
    <tr>
        <th>Cost Code</th>
        <th>Keterangan</th>
        <th>Lokasi</th>
        <th>No ID</th>
        <th>Tanggal</th>
        <th>Departemen</th>
        <th>Dibuat Oleh</th>
        <th>Cost Value</th>
        <th>Cost Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @foreach($costCodes as $costCode)
        @php( $totalCostValue = 0 )
        @foreach($costCode->issued_docket_headers as $header)
            @php( $totalCostValue += $header->total_value )
        @endforeach
        <tr>
            <td>{{ $costCode->code }}</td>
            <td>{{ $costCode->description }}</td>
            <td>{{ $costCode->location }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($costCode->issued_docket_headers as $header)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $header->code }}</td>
                <td>{{ $header->date_string }}</td>
                <td>{{ $header->department->name }}</td>
                <td>{{ $header->createdBy->email }}</td>
                <td>{{ $header->total_value }}</td>
                <td></td>
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
            <td>{{ $totalCostValue }}</td>
        </tr>
    @endforeach
    </tbody>
</table>