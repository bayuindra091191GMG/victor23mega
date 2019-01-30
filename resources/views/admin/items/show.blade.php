@extends('admin.layouts.admin')

@section('title','Data Inventory '. $selectedItem->name. ' ('. $selectedItem->code.')')

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.items') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">

                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">OPSI
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.items.edit',[ 'purchase_request' => $selectedItem->id]) }}">UBAH</a></li>
                        <li><a href="{{ route('admin.items.invoice_report',['item' => $selectedItem->id]) }}">Laporan Pembelian</a></li>
                        <li><a href="{{ route('admin.stock_cards.report',['item' => $selectedItem->id]) }}">Laporan Stock Card</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">
                @if(\Illuminate\Support\Facades\Session::has('message'))
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            @include('partials._success')
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Kode Inventory
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $selectedItem->code }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nama Inventory
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $selectedItem->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Total QTY Stok
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ !empty($selectedItem->stock) && $selectedItem->stock > 0 ? $selectedItem->stock : '0'  }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Total QTY Stok On Order
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ !empty($selectedItem->stock_on_order) && $selectedItem->stock_on_order > 0 ? $selectedItem->stock_on_order : '0'  }}
                    </div>
                </div>

                {{--<div class="form-group">--}}
                    {{--<label class="col-md-3 col-sm-3 col-xs-12">--}}
                        {{--Minimum Stok--}}
                    {{--</label>--}}
                    {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                        {{--: {{ $selectedItem->stock_minimum  }}--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="form-group">--}}
                    {{--<label class="col-md-3 col-sm-3 col-xs-12">--}}
                        {{--Notifikasi Stok--}}
                    {{--</label>--}}
                    {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                        {{--: {{ $selectedItem->stock_notification === 1 ? 'AKTIF' : 'TIDAK AKTIF'  }}--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        UOM
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $selectedItem->uom }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Kategori Inventory
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $selectedItem->group->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Tanggal Dibuat
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ \Carbon\Carbon::parse($selectedItem->created_at)->format('d M Y') }}
                    </div>
                </div>
                <hr>

                <div class="form-group text-center">
                    <h3>Gudang dan Stok</h3>
                </div>
                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-xs-12 column">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr >
                            <tr >
                                <th class="text-center" style="width: 20%">
                                    Gudang
                                </th>
                                <th class="text-center" style="width: 20%">
                                    Lokasi/Rak
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Stok On Hand
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Minimum Stok
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Maksimum Stok
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Notifikasi Stok
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Tindakan
                                </th>
                            </tr>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($itemStocks as $stock)
                                <tr class="item{{ $stock->id }}">
                                    <td>
                                        {{ $stock->warehouse->name }}
                                    </td>
                                    <td>
                                        {{ $stock->location }}
                                    </td>
                                    <td class="text-right">
                                        {{ $stock->stock }}
                                    </td>
                                    <td class="text-right">
                                        {{ $stock->stock_min }}
                                    </td>
                                    <td class="text-right">
                                        {{ $stock->stock_max }}
                                    </td>
                                    <td class="text-center">
                                        {{ $stock->is_stock_warning ? 'AKTIF' : 'NON-AKTIF' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.item_stocks.option.edit', ['item_stock' => $stock->id]) }}" class="btn btn-default">PENGATURAN</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xs-12 text-center">
                        <a href="{{ route('admin.item_stocks.option.create', ['item' => $selectedItem->id]) }}" class="btn btn-primary">TAMBAH PENGATURAN GUDANG BARU</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal form to edit stock location -->
    {{--<div id="editModal" class="modal fade" role="dialog">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}

                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal">×</button>--}}
                    {{--<h4 class="modal-title"></h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--{{ Form::open(['route'=>['admin.item_stocks.location.update'],'method' => 'post', 'id' => 'general-form', 'class'=>'form-horizontal form-label-left', 'novalidate']) }}--}}
                    {{--<div class="form-horizontal">--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="item_name">Inventory:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="item_name" readonly>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="warehouse_name">Gudang:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="warehouse_name" readonly>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="control-label col-sm-2" for="location">Lokasi/Rak:</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control" id="location" name="location">--}}
                            {{--</div>--}}

                            {{--<input type="hidden" id="item_stock_id" name="item_stock_id"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-warning" data-dismiss="modal">--}}
                            {{--<span class='glyphicon glyphicon-remove'></span> Batal--}}
                        {{--</button>--}}
                        {{--<button type="submit" class="btn btn-success">--}}
                            {{--<span class='glyphicon glyphicon-check'></span> Simpan--}}
                        {{--</button>--}}
                    {{--</div>--}}
                    {{--{{ Form::close() }}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    <style>
        .box-section{
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 2px;
            padding: 10px;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script type="text/javascript">
        $(document).on('click', '.edit-location-modal', function() {
            $('#item_stock_id').val($(this).data('id'));
            $('#item_name').val($(this).data('item-name'));
            $('#warehouse_name').val($(this).data('warehouse-name'));
            $('#location').val($(this).data('location'));

            $('.modal-title').text('Ubah Lokasi/Rak Inventory');
            $('#editModal').modal('show');
        });
    </script>
@endsection