@extends('admin.layouts.admin')

@section('title', 'Daftar Purchase Request')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-left">
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label>Filter:</label>
                    <input type="text" class="form-control" value="{{ $filter }}" readonly>
                </div>
                <div class="form-group">
                    <label>Bulan:</label>
                    <input type="text" class="form-control" value="{{ $month }}" readonly>
                </div>
            </form>
        </div>
        <div class="nav navbar-right">
            <a href="{{ route('admin.purchase_requests.before_create') }}" class="btn btn-app">
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
                <th class="text-center">Nomor PR</th>
                <th class="text-center">Nomor MR</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Departemen</th>
                <th class="text-center">Prioritas</th>
                <th class="text-center">Status PO</th>
                <th class="text-center">Kode Unit</th>
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
                    url: '{!! route('datatables.purchase_request_index_chart') !!}',
                    data: {
                        'status': '{{ $status }}',
                        'month' : '{{ $month }}'
                    }
                },
                order: [ [2, 'desc'] ],
                columns: [
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'mr_code', name: 'mr_code', class: 'text-center', orderable: false, searchable: false },
                    { data: 'date', name: 'date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'department', name: 'department', class: 'text-center', orderable: false, searchable: false },
                    { data: 'priority', name: 'priority', class: 'text-center' },
                    { data: 'is_all_poed', name: 'is_all_poed', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                if(data === 1){
                                    return 'SUDAH PO';
                                }
                                else{
                                    return 'BELUM PO';
                                }
                            }
                            return data;
                        }
                    },
                    { data: 'machinery', name: 'machinery', class: 'text-center', orderable: false, searchable: false },
                    { data: 'created_by', name: 'created_by', class: 'text-center', orderable: false, searchable: false },
                    { data: 'status', name: 'status', class: 'text-center', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
