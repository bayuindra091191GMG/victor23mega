@extends('admin.layouts.admin')

@section('title', 'Daftar Vendor')

@section('content')

    <div class="row">
        <div class="nav navbar-right">
            @include('partials._success')

            @if(auth()->user()->id === 25)
                <a href="{{ route('admin.suppliers.create') }}" class="btn btn-app">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            @else
                @if(auth()->user()->roles->pluck('id')[0] == 1 ||
                auth()->user()->roles->pluck('id')[0] == 3 ||
                auth()->user()->roles->pluck('id')[0] == 14 ||
                auth()->user()->roles->pluck('id')[0] == 12 ||
                auth()->user()->roles->pluck('id')[0] == 15 ||
                auth()->user()->roles->pluck('id')[0] == 13)
                    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-app">
                        <i class="fa fa-plus"></i> Tambah
                    </a>
                @endif
            @endif

            <a href="{{ route('admin.suppliers.download_excel') }}" class="btn btn-app">
                <i class="fa fa-file-excel-o"></i> EXCEL
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="supplier-table">
            <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Tipe</th>
                <th class="text-center">Kategori</th>
                <th class="text-center">Email</th>
                <th class="text-center">Telpon</th>
                <th class="text-center">Contact Person</th>
                <th class="text-center">Kota</th>
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
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(function() {

            $('#supplier-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url:'{!! route('datatables.suppliers') !!}',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'code', name: 'code', class: 'text-center' },
                    { data: 'name', name: 'name', class: 'text-center' },
                    { data: 'type', name: 'type', class: 'text-center' },
                    { data: 'category', name: 'category', class: 'text-center' },
                    { data: 'email', name: 'email', class: 'text-center' },
                    { data: 'phone', name: 'phone', class: 'text-center' },
                    { data: 'contact_person', name: 'contact_person', class: 'text-center' },
                    { data: 'city', name: 'city', class: 'text-center' },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'}
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });

        $(document).on('click', '.delete-modal', function(){
            $('#deleteModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#deleted-id').val($(this).data('id'));
        });
    </script>
    @include('partials._deleteJs', ['routeUrl' => 'admin.suppliers.destroy', 'redirectUrl' => 'admin.suppliers'])
@endsection
