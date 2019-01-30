@extends('admin.layouts.admin')

@section('title', 'Daftar Purchase Order')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-left">
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label for="filter-status">Status:</label>
                    <select id="filter-status" class="form-control" onchange="filterStatus(this)">
                        <option value="0" @if($filterStatus == '0') selected @endif>Semua</option>
                        <option value="3" @if($filterStatus == '3') selected @endif>Open</option>
                        <option value="4" @if($filterStatus == '4') selected @endif>Close</option>
                        <option value="11" @if($filterStatus == '11') selected @endif>Close Manual</option>
                        <option value="13" @if($filterStatus == '13') selected @endif>Reject</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="nav navbar-right">
            <a href="{{ route('admin.purchase_orders.before_create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="pr-table">
            <thead>
            <tr>
                <th class="text-center">Nomor PO</th>
                <th class="text-center">Nomor PR</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Prioritas</th>
                <th class="text-center">Nama Vendor</th>
                <th class="text-center">Total PO</th>
                <th class="text-center">Status Approval</th>
                <th class="text-center">Status Dokumen</th>
                <th class="text-center">Tindakan</th>
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
            $('#pr-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: '{!! route('datatables.purchase_orders') !!}',
                    data: {
                        'status': '{{ $filterStatus }}'
                    }
                },
                columns: [
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'pr_code', name: 'pr_code', class: 'text-center', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'priority', name: 'priority', class: 'text-center', orderable: false, searchable: false },
                    { data: 'supplier', name: 'supplier', class: 'text-center', orderable: false, searchable: false },
                    { data: 'total_payment', name: 'total_payment', class: 'text-right',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return data.toLocaleString(
                                    "de-DE",
                                    {minimumFractionDigits: 2}
                                );
                            }
                            return data;
                        }
                    },
                    { data: 'is_approved', name: 'is_approved', class: 'text-center' },
                    { data: 'status', name: 'status', class: 'text-center', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json",
                    decimal: ",",
                    thousands: "."
                }
            });
        });

        function filterStatus(e){
            // Get status filter value
            var status = e.value;

            var url = '{{ route('admin.purchase_orders') }}';

            window.location = url + '?status=' +status;
        }
    </script>
@endsection
