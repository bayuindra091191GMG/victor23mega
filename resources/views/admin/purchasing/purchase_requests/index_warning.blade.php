@extends('admin.layouts.admin')

@section('title', 'Daftar Warning Purchase Request')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="pr-table">
            <thead>
            <tr>
                <th class="text-center">Tanggal PR</th>
                <th class="text-center">Nomor PR</th>
                <th class="text-center">Prioritas</th>
                <th class="text-center">Warning</th>
                <th class="text-center">Jatuh Tempo</th>
                <th class="text-center">Departemen</th>
                <th class="text-center">Dibuat Oleh</th>
                <th class="text-center">Status</th>
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
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.purchase_requests.warning') !!}'
                },
                order: [ [0, 'desc'] ],
                columns: [
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'priority', name: 'priority', class: 'text-center' },
                    { data: 'expired_warning', name: 'expired_warning', class: 'text-center' },
                    { data: 'expired_date', name: 'expired_date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'department', name: 'department', class: 'text-center', orderable: false, searchable: false },
                    { data: 'created_by', name: 'created_by', class: 'text-center', orderable: false, searchable: false },
                    { data: 'status', name: 'status', class: 'text-center', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                createdRow: function ( row, data, index ) {
                    if ( data['expired_warning'] === "SUDAH JATUH TEMPO" ) {
                        $('td', row).eq(3).addClass('danger');
                    } else {
                        $('td', row).eq(3).addClass('warning');
                    }
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
