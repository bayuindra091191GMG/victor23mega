@extends('admin.layouts.admin')

@section('title', 'Daftar Inventory')

@section('content')

    <div class="row">
        @include('partials._success')
        <div class="nav navbar-left">
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label for="filter-status">Status Stok:</label>
                    <select id="filter-status" class="form-control" onchange="filterStock(this)">
                        <option value="0" @if($filterStock == '0') selected @endif>Semua</option>
                        <option value="1" @if($filterStock == '1') selected @endif>Tersedia</option>
                        <option value="2" @if($filterStock == '2') selected @endif>Kosong</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="nav navbar-right">
            <a href="{{ route('admin.items.create') }}" class="btn btn-app" target="_blank">
                <i class="fa fa-plus"></i> Tambah
            </a>
            <a href="{{ route('admin.items.excel') }}" class="btn btn-app">
                <i class="fa fa-file-excel-o"></i> EXCEL
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive" cellspacing="0"
               width="100%" id="items-table">
            <thead>
            <tr>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Satuan Unit</th>
                @if(!$isSite)<th class="text-center">Harga Beli</th>@endif
                <th class="text-center">Stok On Hand Total</th>
                <th class="text-center">Stok On Order</th>
                <th class="text-center">Kategori Inventory</th>
                <th class="text-center">Tipe Alat Berat</th>
                <th class="text-center">Deskripsi</th>
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
            $('#items-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.items') !!}',
                    data: {
                        'stock': '{{ $filterStock }}'
                    }
                },
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'part_number', name: 'part_number' },
                    { data: 'uom', name: 'uom', class: 'text-center'},
                    @if(!$isSite) { data: 'value', name: 'value', class: 'text-right'}, @endif
                    { data: 'stock', name: 'stock', class: 'text-right'},
                    { data: 'stock_on_order', name: 'stock_on_order', class: 'text-right'},
                    { data: 'group', name: 'group', class: 'text-center', searchable: false, sortable: false},
                    { data: 'machinery_type', name: 'machinery_type', class: 'text-center'},
                    { data: 'description', name: 'description' },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
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

        function filterStock(e){
            // Get stock filter value
            var stock = e.value;

            var url = '{{ route('admin.items') }}';

            window.location = url + '?stock=' + stock;
        }
    </script>
    @include('partials._deleteJs', ['routeUrl' => 'admin.items.destroy', 'redirectUrl' => 'admin.items'])
@endsection
