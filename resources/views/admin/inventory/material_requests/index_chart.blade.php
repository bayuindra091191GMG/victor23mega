@extends('admin.layouts.admin')

@section('title', 'Daftar Material Request Prioritas '. ($type === 0 ? 'Part' : 'Non-Part'). ' Bulan '. $month)

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
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="mr-table">
            <thead>
            <tr>
                <th>Nomor MR</th>
                <th>Tanggal</th>
                <th>Tipe MR</th>
                <th>Prioritas</th>
                <th>Departemen</th>
                <th>Kode Unit</th>
                <th>Dibuat Oleh</th>
                <th>Status Dokumen</th>
                <th>Tindakan</th>
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
            $('#mr-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.material_requests_index_chart') !!}',
                    data: {
                        'type': '{{$type}}',
                        'status': '{{ $status }}',
                        'month' : '{{ $month }}'
                    }
                },
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
                    { data: 'type', name: 'type', class: 'text-center', searchable: false, sortable: false },
                    { data: 'priority', name: 'priority', class: 'text-center' },
                    { data: 'department', name: 'department', class: 'text-center', searchable: false, sortable: false },
                    { data: 'machinery', name: 'machinery', class: 'text-center', searchable: false, sortable: false },
                    { data: 'created_by', name: 'created_by', class: 'text-center', searchable: false, sortable: false },
                    { data: 'status', name: 'status', class: 'text-center', searchable: false, sortable: false },
                    { data: 'action', name: 'action',orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
