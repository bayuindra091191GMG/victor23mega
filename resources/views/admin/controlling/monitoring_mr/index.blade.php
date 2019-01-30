@extends('admin.layouts.admin')

@section('title', 'Material Request Monitoring')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @include('partials._success')
        </div>
        <div class="col-md-12">
            <hr/>
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label for="filter_status">Status MR:</label>
                    <select id="filter_status" class="form-control">
                        <option value="0" @if($filterStatus == '0') selected @endif>Semua</option>
                        <option value="3" @if($filterStatus == '3') selected @endif>Open</option>
                        <option value="4" @if($filterStatus == '4') selected @endif>Close</option>
                        <option value="11" @if($filterStatus == '11') selected @endif>Close Manual</option>
                        <option value="13" @if($filterStatus == '13') selected @endif>Reject</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter_site">Site:</label>
                    <select id="filter_site" class="form-control">
                        <option value="0" @if($filterSite == '0') selected @endif>Semua</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" @if($filterSite == $site->id) selected @endif>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter_priority">Prioritas:</label>
                    <select id="filter_priority" class="form-control">
                        <option value="ALL" @if($filterPriority === 'ALL') selected @endif>Semua</option>
                        <option value="Part - P1" {{ $filterPriority === "Part - P1" ? "selected":"" }}>Part - P1</option>
                        <option value="Part - P2" {{ $filterPriority === "Part - P2" ? "selected":"" }}>Part - P2</option>
                        <option value="Part - P3" {{ $filterPriority === "Part - P3" ? "selected":"" }}>Part - P3</option>
                        <option value="Non-Part - P1" {{ $filterPriority === "Non-Part - P1" ? "selected":"" }}>Non-Part - P1</option>
                        <option value="Non-Part - P2" {{ $filterPriority === "Non-Part - P2" ? "selected":"" }}>Non-Part - P2</option>
                        <option value="Non-Part - P3" {{ $filterPriority === "Non-Part - P3" ? "selected":"" }}>Non-Part - P3</option>
                    </select>
                </div>
            </form>
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label for="date_start">
                        Tanggal Mulai
                    </label>
                    <input id="date_start" type="text" class="form-control" value="{{ $filterDateStart }}">
                </div>
                <div class="form-group">
                    <label for="date_end">
                        Tanggal Akhir
                    </label>
                    <input id="date_end" type="text" class="form-control" value="{{ $filterDateEnd }}">
                </div>
                <div class="form-group">
                    <a id="btn_filter" class="btn btn-primary" style="margin: 0 !important;">FILTER</a>
                </div>
                <div class="form-group">
                    <a id="btn_reset" class="btn btn-primary" style="margin: 0 !important;">RESET</a>
                </div>
            </form>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive" id="table_container">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                   width="100%" id="monitoring_table">
                <thead>
                <tr>
                    <th class="text-center">Nomor MR</th>
                    <th class="text-center">Tanggal MR</th>
                    <th class="text-center">Site</th>
                    <th class="text-center">Departemen</th>
                    <th class="text-center">Prioritas</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Kode Inventory Belum PO</th>
                    <th class="text-center">Part Number Belum PO</th>
                    <th class="text-center">Qty Belum PO</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Kode Inventory Sudah PO</th>
                    <th class="text-center">Part Number Sudah PO</th>
                    <th class="text-center">Qty Sudah PO</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga Satuan</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">PPN 10%</th>
                    <th class="text-center">Total Harga + PPN</th>
                    <th class="text-center">Handle By</th>
                    <th class="text-center">Nomor PR</th>
                    <th class="text-center">Nomor PO</th>
                    <th class="text-center">Tgl PO</th>
                    <th class="text-center">Supplier</th>
                    <th class="text-center">Lokasi Supplier</th>
                    <th class="text-center">Nomor GR</th>
                    <th class="text-center">Lead Time GR</th>
                    <th class="text-center">Nomor SJ</th>
                    <th class="text-center">Tgl Kirim</th>
                    <th class="text-center">Tgl Diterima Site</th>
                    <th class="text-center">Status SJ</th>
                    <th class="text-center">Lead Time SJ Ter-Confirm</th>
                    {{--<th class="text-center">Nomor ID</th>--}}
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

    <style>
        #table_container{
            overflow: hidden;
            overflow-x: scroll;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script>
        // Date Picker
        $('#date_start').datetimepicker({
            format: "DD MMM Y"
        });

        $('#date_end').datetimepicker({
            format: "DD MMM Y"
        });

        $(function() {
            $('#monitoring_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                pageLength: 25,
                ajax: {
                    url: '{!! route('datatables.controlling.monitor.mr') !!}',
                    data: {
                        'date_start': '{{ $filterDateStart }}',
                        'date_end': '{{ $filterDateEnd }}',
                        'status': '{{ $filterStatus }}',
                        'site': '{{ $filterSite }}',
                        'priority': '{{ $filterPriority }}'
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    { data: 'code_mr', name: 'code_mr', class: 'text-left' },
                    { data: 'date_mr', name: 'date_mr', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'site', name: 'site', class: 'text-left' },
                    { data: 'department', name: 'department', class: 'text-left' },
                    { data: 'priority', name: 'priority', class: 'text-left' },
                    { data: 'machinery', name: 'machinery', class: 'text-left' },
                    { data: 'item_code_unpoed', name: 'item_code_unpoed', class: 'text-left' },
                    { data: 'item_part_number_unpoed', name: 'item_part_number_unpoed', class: 'text-left' },
                    { data: 'qty_unpoed', name: 'qty_unpoed', class: 'text-right' },
                    { data: 'uom_unpoed', name: 'uom_unpoed', class: 'text-center' },
                    { data: 'inventory_id', name: 'inventory_id', class: 'text-left' },
                    { data: 'part_number', name: 'part_number', class: 'text-left' },
                    { data: 'qty', name: 'qty', class: 'text-right' },
                    { data: 'unit', name: 'unit', class: 'text-left' },
                    { data: 'price', name: 'price', class: 'text-right' },
                    { data: 'subtotal', name: 'subtotal', class: 'text-right' },
                    { data: 'ppn', name: 'ppn', class: 'text-right' },
                    { data: 'total', name: 'total', class: 'text-right' },
                    { data: 'user', name: 'user', class: 'text-center' },
                    { data: 'code_pr', name: 'code_pr', class: 'text-left' },
                    { data: 'code_po', name: 'code_po', class: 'text-left' },
                    { data: 'po_date', name: 'po_date', class: 'text-left' },
                    { data: 'supplier_name', name: 'supplier_name', class: 'text-left' },
                    { data: 'supplier_location', name: 'supplier_location', class: 'text-left' },
                    { data: 'code_gr', name: 'code_gr', class: 'text-left' },
                    { data: 'lead_time_gr', name: 'lead_time_gr', class: 'text-center' },
                    { data: 'code_sj', name: 'code_sj', class: 'text-left' },
                    { data: 'start_sj', name: 'start_sj', class: 'text-left' },
                    { data: 'finish_sj', name: 'finish_sj', class: 'text-left' },
                    { data: 'status_sj', name: 'status_sj', class: 'text-center' },
                    { data: 'lead_time_sj', name: 'lead_time_sj', class: 'text-center' }
                    // { data: 'code_id', name: 'code_id', class: 'text-left' },
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json",
                    decimal: ",",
                    thousands: "."
                }
            });
        });

        $(document).on("click", "#btn_filter", function(){
            var dateStart = $('#date_start').val();
            var dateEnd = $('#date_end').val();
            var status = $('#filter_status').val();
            var site = $('#filter_site').val();
            var priority = $('#filter_priority').val();

            var url = '{{ route('admin.controlling.monitor.mr.index') }}';
            window.location = url + '?status=' + status + '&date_start=' + dateStart + "&date_end=" + dateEnd + "&site=" + site + "&priority=" + priority;
        });

        $(document).on("click", "#btn_reset", function(){
            var url = '{{ route('admin.controlling.monitor.mr.index') }}';
            window.location = url;
        });
    </script>
@endsection
