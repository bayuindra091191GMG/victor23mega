<table>
    <thead>
    <tr>
        <th>No PO</th>
        <th>Tanggal</th>
        <th>No MR</th>
        <th>Vendor</th>
        <th>Part Code</th>
        <th>Part Number</th>
        <th>Keterangan</th>
        <th>QTY</th>
        <th>UOM</th>
        <th>Harga</th>
        <th>Diskon</th>
        <th>Subtotal</th>
        <th>Diskon Tambahan</th>
        <th>Total Harga</th>
        <th>PPN</th>
        <th>PPh</th>
        <th>Ongkos Kirim</th>
        <th>Total Pembayaran</th>
    </tr>
    </thead>
    <tbody>
    @php($i=1)
    @foreach($poHeaders as $item)
{{--        <tr>--}}
{{--            <td>{{ $item->code }}</td>--}}
{{--            <td>{{ $item->date_string }}</td>--}}
{{--            <td>{{ $item->purchase_request_header->material_request_header->code }}</td>--}}
{{--            <td>{{ $item->supplier->name }}</td>--}}
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
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--            <td></td>--}}
{{--        </tr>--}}
        @foreach($item->purchase_order_details as $detail)
            <tr>
                <td>{{ $item->code }}</td>
                <td>{{ $item->date_string }}</td>
                <td>{{ $item->purchase_request_header->material_request_header->code }}</td>
                <td>{{ $item->supplier->name }}</td>
                <td>{{ $detail->item->code }}</td>
                <td>{{ $detail->item->part_number ?? '-' }}</td>
                <td>{{ $detail->item->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->item->uom }}</td>
                <td>{{ $detail->price }}</td>
                <td>{{ !empty($detail->discount) && $detail->discount > 0 ? $detail->discount_amount : '0' }}</td>
                <td>{{ $detail->subtotal }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $item->extra_discount > 0 ? $item->extra_discount : 0 }}</td>
            <td>{{ $item->total_payment_before_tax }}</td>
            <td>{{ $item->ppn_amount > 0 ? $item->ppn_amount : 0 }}</td>
            <td>{{ $item->pph_amount > 0 ? $item->pph_amount : 0 }}</td>
            <td>{{ $item->delivery_fee > 0 ? $item->delivery_fee : 0 }}</td>
            <td>{{ $item->total_payment }}</td>
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
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            Total Semua PO
        </td>
        <td>
            {{ $total }}
        </td>
    </tr>
    </tbody>
</table>