@extends('admin.layouts.admin')

@section('title', 'Daftar Stock Card')

@section('content')

    <div class="row">
        @include('partials._success')
        {{--<div class="nav navbar-right">--}}
            {{--<a href="{{ route('admin.stock_ins.create') }}" class="btn btn-app">--}}
                {{--<i class="fa fa-plus"></i> Tambah--}}
            {{--</a>--}}
        {{--</div>--}}
        {{--<div class="clearfix"></div>--}}
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="stock-card-table">
            <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Referensi</th>
                <th class="text-center">Inventory</th>
                <th class="text-center">Gudang</th>
                <th class="text-center">QTY IN</th>
                <th class="text-center">QTY OUT</th>
                <th class="text-center">QTY SISA</th>
                <th class="text-center">Penginput</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script>
        $(function() {
            $('#stock-card-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: '{!! route('datatables.stock_cards') !!}',
                createdRow: function(row, data, dataIndex) {
                    var $dateCell = $(row).find('td:eq(1)'); // get first column
                    var dateOrder = $dateCell.text(); // get the ISO date
                    $dateCell
                        .attr('data-order', dateOrder) // set it to data-order
                        .text(moment(dateOrder).format('DD MMM YYYY')); // and set the formatted text
                },
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center' },
                    { data: 'reference', name: 'reference', class: 'text-center' },
                    { data: 'item', name: 'item' },
                    { data: 'warehouse', name: 'warehouse', class: 'text-center' },
                    { data: 'in_qty', name: 'in_qty', class: 'text-center' },
                    { data: 'out_qty', name: 'out_qty', class: 'text-center' },
                    { data: 'result_qty', name: 'result_qty', class: 'text-center' },
                    { data: 'created_by', name: 'created_by', class: 'text-center' }
                ],
                language: {
                    url: "{{ URL::asset('indonesian.json') }}"
                }
            });
        });
    </script>
@endsection
