@extends('admin.layouts.admin')

@section('title', 'Daftar Payment Request')

@section('content')

    <div class="row">
        @include('partials._success')
        {{--<div class="nav navbar-right">--}}
            {{--<a href="{{ route('admin.payment_requests.choose-vendor') }}" class="btn btn-app">--}}
                {{--<i class="fa fa-plus"></i> Tambah--}}
            {{--</a>--}}
        {{--</div>--}}
        {{--<div class="clearfix"></div>--}}
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="rfp_table">
            <thead>
            <tr>
                <th class="text-center" style="width: 20%;">Nomor Payment Request</th>
                <th class="text-center" style="width: 10%;">Tanggal</th>
                <th class="text-center" style="width: 10%;">Tipe Payment</th>
                <th class="text-center" style="width: 15%;">Total</th>
                <th class="text-center" style="width: 20%;">Vendor</th>
                <th class="text-center" style="width: 10%;">Diminta Oleh</th>
                <th class="text-center" style="width: 15%;">Tindakan</th>
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
            $('#rfp_table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: '{!! route('datatables.payment_requests') !!}',
                order: [ [1, 'desc'] ],
                columns: [
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'type', name: 'type', class: 'text-center', orderable: false, searchable: false },
                    { data: 'amount', name: 'amount', class: 'text-right',
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
                    { data: 'supplier', name: 'supplier.name', class: 'text-center' },
                    { data: 'request_by', name: 'request_by', class: 'text-center', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
