@extends('admin.layouts.admin')

@section('title', 'Pilih Purchase Order Terlebih Dahulu')

@section('content')

    <div class="row">
        <div class="navbar-left">
            <a class="btn btn-default" href="{{ route('admin.purchase_invoices') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="pr-table">
            <thead>
            <tr>
                <th class="text-center">Tindakan</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Nomor PO</th>
                <th class="text-center">Nomor PR</th>
                <th class="text-center">Nama Vendor</th>
                <th class="text-center">Total PO</th>
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
                ajax: {
                    url: '{!! route('datatables.purchase_orders') !!}',
                    data: {
                        'mode': 'before_create_pi'
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'pr_code', name: 'pr_code', class: 'text-center', orderable: false, searchable: false },
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
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
