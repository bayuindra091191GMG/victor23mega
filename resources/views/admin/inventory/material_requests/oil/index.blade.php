@extends('admin.layouts.admin')

@section('title', 'Daftar Material Request Oli')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-right">
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
            <a href="{{ route('admin.material_requests.oil.create') }}" class="btn btn-app" target="_blank">
                <i class="fa fa-plus"></i> Tambah
            </a>
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
                <th>Penggunaan</th>
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
                    url: '{!! route('datatables.material_requests') !!}',
                    data: {
                        'type': 'oil',
                        'status': '{{ $filterStatus }}'
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
                    { data: 'purpose', name: 'purpose', class: 'text-center' },
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

        function filterStatus(e){
            // Get status filter value
            var status = e.value;

            var url = '{{ route('admin.material_requests.oil') }}';

            window.location = url + '?status=' +status;
        }
    </script>
@endsection
