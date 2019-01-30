@extends('admin.layouts.admin')

@section('title', 'Data Cost Codes')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-right">
            <a href="{{ route('admin.accounts.create') }}" class="btn btn-app">
                <i class="fa fa-plus"></i> Tambah
            </a>
            <a href="{{ route('admin.accounts.excel') }}" class="btn btn-app">
                <i class="fa fa-file-excel-o"></i> EXCEL
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="account_table">
            <thead>
            <tr>
                <th class="text-center" style="width: 10%;">Kode</th>
                <th class="text-center" style="width: 10%;">Lokasi</th>
                <th class="text-center" style="width: 10%;">Departemen</th>
                <th class="text-center" style="width: 10%;">Divisi</th>
                <th class="text-center" style="width: 10%;">Keterangan</th>
                <th class="text-center" style="width: 10%;">Dibuat Oleh</th>
                <th class="text-center" style="width: 10%;">Dibuat Tanggal</th>
                <th class="text-center" style="width: 10%;">Diubah Oleh</th>
                <th class="text-center" style="width: 10%;">Diubah Tanggal</th>
                <th class="text-center" style="width: 10%;">Tindakan</th>
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
            $('#account_table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: '{!! route('datatables.accounts') !!}',
                order: [ [0, 'asc'] ],
                columns: [
                    { data: 'code', name: 'code', class: 'text-center'},
                    { data: 'location', name: 'location', class: 'text-center'},
                    { data: 'department', name: 'department', class: 'text-center'},
                    { data: 'division', name: 'division', class: 'text-center'},
                    { data: 'description', name: 'description'},
                    { data: 'created_by', name: 'created_by', class: 'text-center', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', class: 'text-center', orderable: false, searchable: false,
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'updated_by', name: 'updated_by', class: 'text-center', orderable: false, searchable: false },
                    { data: 'updated_at', name: 'updated_at', class: 'text-center', orderable: false, searchable: false,
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'}
                ],
                language: {
                    url: "{{ URL::asset('indonesian.json') }}"
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
    @include('partials._deleteJs', ['routeUrl' => 'admin.accounts.delete', 'redirectUrl' => 'admin.accounts'])
@endsection
