@extends('admin.layouts.admin')

@section('title','Data Material Request Servis '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.material_requests.service') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">

                @if($header->status_id == 3)
                    <a class="close-modal btn btn-danger" data-id="{{ $header->id }}">TUTUP MR</a>
                @endif

                <a class="btn btn-default" href="{{ route('admin.material_requests.print',[ 'material_request' => $header->id]) }}" target="_blank">CETAK</a>

                @if($header->status_id === 3 && $approveMr)
                    <a class="btn btn-primary" href="{{ route('admin.approval_rules.mr_approval',[ 'material_request' => $header->id]) }}">APPROVE/REJECT</a>
                @endif

                @if($header->status_id === 3)
                    @if($setting == 1)
                        @if($isApproved && !$isPrCreated)
                            @if($isAuthorized)
                                <a class="btn btn-success" href="{{ route('admin.purchase_requests.create',[ 'mr' => $header->id]) }}">PROSES PR</a>
                            @endif
                        @endif
                    @else
                        @if(!$isPrCreated)
                            @if($isAuthorized)
                                <a class="btn btn-success" href="{{ route('admin.purchase_requests.create',[ 'mr' => $header->id]) }}">PROSES PR</a>
                            @endif
                        @endif
                    @endif
                @endif

                @if(!$isPrCreated)
                    <a class="btn btn-default" href="{{ route('admin.material_requests.service.edit',[ 'material_request' => $header->id]) }}">UBAH</a>
                @endif

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                        @if(\Illuminate\Support\Facades\Session::has('message'))
                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                    @include('partials._success')
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                STATUS
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                @if($isPrCreated && $header->purchase_request_headers->first()->status_id === 11)
                                    : <span style="font-weight: bold; color: red;">PR - CLOSED MANUAL</span>
                                @else
                                    @if($header->status_id == 3)
                                        : <span style="font-weight: bold; color: green;">OPEN</span>
                                    @elseif($header->status_id == 4)
                                        : <span style="font-weight: bold; color: red;">CLOSED</span>
                                    @elseif($header->status_id == 11)
                                        : <span style="font-weight: bold; color: red;">CLOSED MANUAL</span>
                                    @elseif($header->status_id == 13)
                                        : <span style="font-weight: bold; color: red;">REJECTED</span>
                                    @endif
                                @endif

                            </div>
                        </div>

                        @if($header->status_id == 11)
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Alasan Tutup
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->close_reason ?? '' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Tanggal Tutup
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ \Carbon\Carbon::parse($header->closed_at)->format('d M Y') ?? '' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Ditutup Oleh
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->closedBy->email ?? '' }}
                                </div>
                            </div>
                        @endif

                        @if($header->status_id == 13)
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Alasan Reject
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ $header->reject_reason ?? '' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Tanggal Reject
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    : {{ \Carbon\Carbon::parse($header->rejected_date)->format('d M Y') ?? '' }}
                                </div>
                            </div>
                        @endif

                        @if($setting == 1)
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 col-xs-12">
                                    Approved
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @if($status == 99)
                                        :

                                        @foreach($approvalData as $data)
                                            {!! $data !!},
                                        @endforeach

                                    @elseif($status < 99 && $status != 0)
                                        :

                                        @foreach($approvalData as $data)
                                            {!! $data !!},
                                        @endforeach

                                    @elseif($status == 0 || $status == 101)
                                        :

                                        @foreach($approvalData as $data)
                                            {!! $data !!},
                                        @endforeach

                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Nomor MR
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $header->code }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Tanggal
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $date }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Site
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->site->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Departemen
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $header->department->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Prioritas
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $header->priority ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Unit Alat Berat
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $header->machinery->code ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Engine Model
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->machinery->engine_model ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                SN Chasis
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->machinery->sn_chasis ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                SN Engine
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->machinery->sn_engine ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                KM
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $header->km ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                HM
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                : {{ $header->hm ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Keterangan Servis
                            </label>
                            <div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
                                <textarea rows="5" style="resize: none;" class="form-control col-md-7 col-xs-12" readonly>{{ $header->material_request_details->first()->remark }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Dibuat Oleh
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->createdBy->email }} - {{ $header->createdBy->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12" for="feedback">
                                Feedback
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                @if(!empty($header->feedback))
                                    : {{ $header->feedback }} <br/>
                                    @if($isFeedbackAuthorized)
                                        <button type="button" class="btn btn-info btn-small" data-toggle="modal" data-target="#feedbackModal">UBAH</button>
                                    @endif
                                @else
                                    :
                                    @if($isFeedbackAuthorized)
                                        <button type="button" class="btn btn-info btn-small" data-toggle="modal" data-target="#feedbackModal">TAMBAH</button>
                                    @endif
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        @if(!empty($header->pdf_path))
                            <a href="{{ route('admin.material_requests.pdf.download', ['material_request' => $header->id]) }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-download"></i>&nbsp;UNDUH BERITA ACARA
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        @if($isTrackingAvailable)
                            <hr/>
                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-xs-12 column">
                                    <div class="row">
                                        <h4 class="text-center">Tracking MR</h4>
                                    </div>
                                    <div class="row">
                                        @if(!empty($trackedPrHeader))
                                            <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                                <h5>PR</h5>
                                                <a href="{{ route('admin.purchase_requests.show', ['purchase_request' => $trackedPrHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $trackedPrHeader->code }}</a><br/>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                                <h5>PO</h5>
                                                @if($trackedPoHeaders->count() > 0)
                                                    @foreach($trackedPoHeaders as $poHeader)
                                                        <a href="{{ route('admin.purchase_orders.show', ['purchase_order' => $poHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $poHeader->code }}</a><br/>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                                <h5>PI</h5>
                                                @if($trackedPiHeaders->count() > 0)
                                                    @foreach($trackedPiHeaders as $piHeader)
                                                        <a href="{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $piHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $piHeader->code }}</a><br/>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
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

    <div id="feedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                {{ Form::open(['route'=>['admin.material_requests.feedback', $header->id],'method' => 'post','id' => 'general-form']) }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="text-center">TAMBAH/UBAH FEEDBACK</h4>
                </div>
                <div class="modal-body">
                    <textarea id="feedback" name="feedback" class="form-control" rows="5" placeholder="Isi feedback anda maksimum 500 karakter">{{ $header->feedback }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <span class='glyphicon glyphicon-remove'></span> BATAL
                    </button>
                    <input type="submit" class="btn btn-success" value="SIMPAN" />
                </div>
                {{ Form::close() }}
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