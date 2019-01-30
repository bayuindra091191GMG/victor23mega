@extends('admin.layouts.admin')

@section('title', 'Daftar User')

@section('content')
    <div class="row">
        @include('partials._success')
        @include('partials._error')
        <div class="nav navbar-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <form class="form-inline" style="margin-bottom: 10px;">
            <div class="form-group">
                <label>Status:</label>
                <select id="filter-status" class="form-control" onchange="filterStatus(this)">

                    @if(!empty($filterStatus) && $filterStatus == '1')
                        <option value='1' selected>Aktif</option>
                    @else
                        <option value='1'>Aktif</option>
                    @endif

                    @if(!empty($filterStatus) && $filterStatus == '2')
                        <option value='2' selected>NonAktif</option>
                    @else
                        <option value='2'>NonAktif</option>
                    @endif

                </select>
            </div>
        </form>
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="users-table">
            <thead>
            <tr>
                <th class="text-center">ID Login</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Email</th>
                <th class="text-center">Departemen</th>
                <th class="text-center">Site</th>
                <th class="text-center">Level Akses</th>
                <th class="text-center">Status</th>
                <th class="text-center">Tanggal Dibuat</th>
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
                    url: '{!! route('datatables.users') !!}',
                    data: {
                        'status' : '{{ $filterStatus  }}'
                    }
                },
                columns: [
                    { data: 'email', name: 'email' },
                    { data: 'name', name: 'name' },
                    { data: 'email_address', name: 'email_address' },
                    { data: 'department', name: 'department', class: 'text-center' },
                    { data: 'site', name: 'site', class: 'text-center' },
                    { data: 'role', name: 'role', class: 'text-center' },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center' },
                    { data: 'action', name:'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });


        function filterStatus(e){
            // Get status filter value
            var status = e.value;

            var url = "/admin/users?status=" + status;

            window.location = url;
        }

        $(document).on('click', '.delete-modal', function(){
            $('#deleteModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#deleted-id').val($(this).data('id'));
        });
    </script>
    @include('partials._deleteJs', ['routeUrl' => 'admin.users.destroy', 'redirectUrl' => 'admin.users'])
@endsection
