@extends('admin.layouts.admin')

@section('title', 'Daftar Assigment MR')

@section('content')
    <div class="row">
        @include('partials._success')
        @include('partials._error')
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="users-table">
            <thead>
            <tr>
                <th class="text-center">Tanggal Assigment</th>
                <th class="text-center">No MR</th>
                <th class="text-center">Tanggal buat Dokumen</th>
                <th class="text-center">Di-assign ke</th>
                <th class="text-center">Di-assign oleh</th>
                <th class="text-center">Di-proses oleh</th>
                <th class="text-center">Tanggal diproses</th>
                <th class="text-center">Status Assigment</th>
                <th class="text-center">Tindakan</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    @include('partials._delete')
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/index.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/index.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.history_assigment_mr') !!}'
                },
                columns: [
                    { data: 'created_at', name: 'created_at', class: 'text-center' },
                    { data: 'mr_code', name: 'mr_code', class: 'text-center' },
                    { data: 'doc_created_at', name: 'doc_created_at', class: 'text-center'},
                    { data: 'assigned_user', name: 'assigned_user', class: 'text-center' },
                    { data: 'assigner_user', name: 'assigner_user', class: 'text-center' },
                    { data: 'processed_by', name: 'processed_by', class: 'text-center' },
                    { data: 'processed_date', name: 'processed_date', class: 'text-center' },
                    { data: 'status', name: 'status', class: 'text-center' }
                    // { data: 'action', name:'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
