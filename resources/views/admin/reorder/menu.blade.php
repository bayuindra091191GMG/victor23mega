@extends('admin.layouts.admin')

@section('title','Menu Reorder MR')

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                {{--<a class="btn btn-default" href="{{ route('admin.items') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>--}}
            </div>
            <div class="navbar-right">

                {{--<div class="dropdown">--}}
                    {{--<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">OPSI--}}
                        {{--<span class="caret"></span></button>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li><a href="{{ route('admin.items.edit',[ 'purchase_request' => $selectedItem->id]) }}">UBAH</a></li>--}}
                        {{--<li><a href="{{ route('admin.items.invoice_report',['item' => $selectedItem->id]) }}">Laporan Pembelian</a></li>--}}
                        {{--<li><a href="{{ route('admin.stock_cards.report',['item' => $selectedItem->id]) }}">Laporan Stock Card</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">
                @if(\Illuminate\Support\Facades\Session::has('message'))
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            @include('partials._success')
                        </div>
                    </div>
                @endif
                <hr style="border-top: 1px solid black;"/>
                <div class="form-group">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <a href="{{ route('admin.reorder.list') }}" class="btn btn-success w-100"><i class="fas fa-list">&nbsp;&nbsp;DAFTAR INVENTORY REORDER</i></a>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <span>Memuat daftar Inventory yang sudah dibawah atau sama dengan minimum stok.</span>
                    </div>
                </div>
                <hr style="border-top: 1px solid black;"/>
                <div class="form-group">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <button id="btn-calibrate" class="btn btn-success w-100"><i id="icon-calibrate" class="fas fa-sync-alt">&nbsp;&nbsp;KALIBRASI INVENTORY REORDER</i></button>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <span>Melakukan kalibrasi kuantitas issued inventory semua gudang, nilai minimum stok dan maksimum stok.</span>
                        <br/>
                        <span style="color: red; font-weight: bold;">Proses kalibrasi membutuhkan waktu rata-rata 15 sampai 20 menit. Disarankan melakukan kalibrasi pada malam hari atau kondisi server sedang idle.</span>
                    </div>
                </div>
                <hr style="border-top: 1px solid black;"/>
            </form>
        </div>
    </div>

    <!-- Modal form to delete a form -->
    <div id="calibrateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin melakukan kalibrasi inventory stock?</h3>
                    <br />
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Tidak
                        </button>
                        <button type="button" class="btn btn-success calibrate" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-trash'></span> Ya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <style>
        /*.box-section{*/
            /*background-color: #ffffff;*/
            /*border: 1px solid #ccc;*/
            /*border-radius: 2px;*/
            /*padding: 10px;*/
        /*}*/

        .w-100{
            width: 100%;
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">
        $(document).on('click', '#btn-calibrate', function(e) {
            e.preventDefault();
            $('#calibrateModal').modal({backdrop: 'static', keyboard: false})
        });

        $('.modal-footer').on('click', '.calibrate', function() {
            $('#btn-calibrate').prop('disabled', true);

            $('#icon-calibrate').html('');
            $('#icon-calibrate').addClass('fa-spin');

            $.ajax({
                type: 'GET',
                url: '{{ route('admin.reorder.calibrate') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                },
                success: function(data) {
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal kalibrasi!', 'Peringatan', {timeOut: 5000});
                        }, 500);
                    }
                    else{
                        toastr.success('Berhasil membuat antrian proses kalibrasi. Mohon menunggu 15-20 menit dan kondisi server tidak sibuk! ', 'Sukses', {timeOut: 5000});
                    }
                }
            });
        });
    </script>
@endsection