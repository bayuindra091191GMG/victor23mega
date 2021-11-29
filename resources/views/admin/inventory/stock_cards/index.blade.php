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
        <div class="col-lg-12 col-sm-12">
            <div class="nav navbar-left">
                <form class="form-inline" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label for="filter-status">Dari Tanggal:</label>
                        <input id="date_start" type="text" class="form-control"
                               name="date_start" value="{{ $filterDateStart }}">
                    </div>
                    <div class="form-group">
                        <label for="filter-status">Sampai Tanggal:</label>
                        <input id="date_end" type="text" class="form-control"
                               name="date_end" value="{{ $filterDateEnd }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-lg-12 col-sm-12">
            <button type="button" class="btn btn-success" onclick="filter();">FILTER</button>
            <a type="button" class="btn btn-success" href="{{ route('admin.stock_cards') }}">RESET FILTER</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                   width="100%" id="stock-card-table">
                <thead>
                <tr>
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
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script>
        // Datetimepicker
        $('#date_start').datetimepicker({
            format: "DD MMM Y"
        });
        $('#date_end').datetimepicker({
            format: "DD MMM Y"
        });

        $(function() {
            $('#stock-card-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.stock_cards') !!}',
                    data: {
                        'date_start': '{{ $filterDateStart }}',
                        'date_end': '{{ $filterDateEnd }}'
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    var $dateCell = $(row).find('td:eq(0)'); // get first column
                    var dateOrder = $dateCell.text(); // get the ISO date
                    $dateCell
                        .attr('data-order', dateOrder) // set it to data-order
                        .text(moment(dateOrder).format('DD MMM YYYY HH:mm')); // and set the formatted text
                },
                columns: [
                    { data: 'created_at', name: 'created_at', class: 'text-center' },
                    { data: 'reference', name: 'reference' },
                    { data: 'item', name: 'item.code' },
                    { data: 'warehouse', name: 'warehouse.name', class: 'text-center' },
                    { data: 'in_qty', name: 'in_qty', class: 'text-center' },
                    { data: 'out_qty', name: 'out_qty', class: 'text-center' },
                    { data: 'result_qty', name: 'result_qty', class: 'text-center' },
                    { data: 'created_by', name: 'createdBy.name', class: 'text-center' }
                ],
                language: {
                    url: "{{ URL::asset('indonesian.json') }}"
                }
            });
        });

        function filter(){
            let dateStart = $('#date_start').val();
            let dateEnd = $('#date_end').val();

            window.location = '{{ route('admin.stock_cards') }}?date_start=' + dateStart + '&date_end=' + dateEnd;
        }
    </script>
@endsection
