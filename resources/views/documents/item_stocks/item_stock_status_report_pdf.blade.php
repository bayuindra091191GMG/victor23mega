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
    <h3>Laporan Status Stok</h3>
    <span style="font-size: 10px;">Tanggal: {{ $date }}</span><br/>
    <span style="font-size: 10px;">Total Inventory : {{ $itemStocks->count() }}</span>
    <table class="table" style="font-size: 10px;">
        <thead>
        <tr>
            <th class="text-center" style="width: 10%;">No</th>
            <th class="text-center" style="width: 20%;">Kode</th>
            <th class="text-center" style="width: 20%;">Nama</th>
            <th class="text-center" style="width: 20%;">Part Number</th>
            <th class="text-center" style="width: 15%;">UOM</th>
            <th class="text-center" style="width: 15%;">Stok</th>

        </tr>
        </thead>
        <tbody>

        @php($i=1)
        @foreach($itemStocks as $item)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td class="text-left">{{ str_limit($item->code, 20) }}</td>
                <td class="text-left">{{ str_limit($item->name, 20) }}</td>
                <td class="text-left">{{ str_limit($item->part_number, 20) }}</td>
                <td class="text-center">{{ $item->uom }}</td>
                <td class="text-center">{{ $item->getGetStock() }}</td>
            </tr>
            @php($i++)
        @endforeach

        </tbody>
    </table>
</div>
<htmlpageheader name="page-header">

</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div class="text-right">{PAGENO}/{nb}</div>
</htmlpagefooter>
</body>
</html>
