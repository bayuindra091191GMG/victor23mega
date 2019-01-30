@extends('admin.layouts.admin')

@section('title', 'Daftar Otorisasi Menu')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="permission-table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Level Akses</th>
                    <th class="text-center">Otorisasi Menu</th>
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
            $('#permission-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: '{!! route('datatables.permission_menus') !!}',
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'role', name: 'role', class: 'text-center' },
                    { data: 'permission', name: 'permission', class: 'text-center' },
                    { data: 'action', name:'action', class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection
