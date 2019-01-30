@extends('admin.layouts.admin')

@section('title','Approve Material Request '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.material_requests.other') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">

                @if($header->status_id == 3)
                    <a class="close-modal btn btn-danger" data-id="{{ $header->id }}">TUTUP MR</a>
                @endif

                <a class="btn btn-default" href="{{ route('admin.material_requests.print',[ 'material_request' => $header->id]) }}" target="_blank">CETAK</a>

                @if($header->status_id == 3)
                    @if(!$isPrCreated)
                        <a class="btn btn-default" href="{{ route('admin.material_requests.other.edit',[ 'material_request' => $header->id]) }}">UBAH</a>
                        <a class="btn btn-success" href="{{ route('admin.purchase_requests.create',[ 'mr' => $header->id]) }}">PROSES PR</a>
                    @endif
                @endif

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">

                @if(\Illuminate\Support\Facades\Session::has('message'))
                    <div class="form-group">
                        <label class="control-label col-lg-3 col-md-12 col-sm-12 col-xs-12">
                        </label>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            @include('partials._success')
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        STATUS
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        @if($header->status_id == 3)
                            : <span style="font-weight: bold; color: green;">OPEN</span>
                        @elseif($header->status_id == 4)
                            : <span style="font-weight: bold; color: red;">CLOSED</span>
                        @elseif($header->status_id == 11)
                            : <span style="font-weight: bold; color: red;">CLOSED MANUAL</span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor MR
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->code }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Tanggal
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $date }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Departemen
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->department->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Unit Alat Berat
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->machinery->code ?? '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Prioritas
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->priority ?? '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        KM
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->km ?? '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        KM
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->hm ?? '-' }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Penggunaan MR
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->purpose === 'stock' ? 'Stock' : 'Non-Stock' }}
                    </div>
                </div>

                <hr/>

                @if($itemStocks->count() > 0)
                    <div class="form-group">
                        <label class="control-label col-lg-3 col-md-12 col-sm-12 col-xs-12">
                        </label>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="alert alert-success  fade in" role="alert">
                                Inventory tersedia di gudang sebagai berikut,
                                <br/>
                                <ul>
                                    @foreach($itemStocks as $stock)
                                        <li>{{ $stock->warehouse->name }} tersedia {{ $stock->item->code }} ({{ $stock->item->name }}) sebanyak {{ $stock->stock }} {{ $stock->item->uom }}</li>
                                    @endforeach
                                </ul>
                                <br/>
                                <a class="btn btn-default" href="{{ route('admin.issued_dockets.create', ['mr' => $header->id]) }}">PROSES ISSUED DOCKET</a>
                            </div>
                        </div>
                    </div>
                @endif

                <hr/>

                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-xs-12 column">
                        <h4 class="text-center">Detil Inventory</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr >
                                    <th class="text-center" style="width: 20%">
                                        Kode Inventory
                                    </th>
                                    <th class="text-center" style="width: 20%">
                                        Nama Inventory
                                    </th>
                                    <th class="text-center" style="width: 20%">
                                        Part Number Asli
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        UOM
                                    </th>
                                    <th class="text-center" style="width: 10%">
                                        QTY
                                    </th>
                                    <th class="text-center" style="width: 20%">
                                        Remark
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($header->material_request_details as $detail)
                                    <tr>
                                        <td class="text-center">
                                            {{ $detail->item->code }}
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->item->name }}
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->item->part_number ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->item->uom }}
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->quantity }}
                                        </td>
                                        <td>
                                            {{ $detail->remark ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="closeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin menutup dokumen MR ini secara manual?</h3>
                    <br />

                    <form role="form">
                        <input type="hidden" id="closed-id" name="closed-id"/>
                        <label for="reason">Alasan Hapus:</label>
                        <textarea id="reason" name="reason" rows="5" class="form-control col-md-7 col-xs-12" style="resize: vertical"></textarea>
                    </form>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Tidak
                        </button>
                        <button type="submit" class="btn btn-danger closed">
                            <span class='glyphicon glyphicon-trash'></span> Ya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        .box-section{
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 2px;
            padding: 10px;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.close-modal', function(){
            $('#closeModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#closed-id').val($(this).data('id'));
        });

        $('.modal-footer').on('click', '.closed', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.material_requests.close') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#closed-id').val(),
                    'reason': $('#reason').val()
                },
                success: function(data) {
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal menutup MR!!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location.reload();
                    }
                }
            });
        });
    </script>
@endsection