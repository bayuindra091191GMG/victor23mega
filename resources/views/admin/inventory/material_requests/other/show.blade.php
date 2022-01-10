@extends('admin.layouts.admin')

@section('title','Data Material Request Part/Non-Part '. $header->code)

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
                    <a class="btn btn-default" href="{{ route('admin.material_requests.other.edit',[ 'material_request' => $header->id]) }}">UBAH</a>
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
                                <label class="control-label col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                </label>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    @include('partials._success')
                                </div>
                            </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Session::has('error'))
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @include('partials._error')
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

                            @if($isApproved)
                                <div class="form-group">
                                    <label class="col-md-3 col-sm-3 col-xs-12">
                                        Tanggal Approve
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        : {{ $header->approved_date !== null ? \Carbon\Carbon::parse($header->approved_date)->format('d M Y') : "" }}
                                    </div>
                                </div>
                            @endif
                        @endif

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

{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-3 col-sm-3 col-xs-12">--}}
{{--                                Di-assign ke--}}
{{--                            </label>--}}
{{--                            <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                : {{ !empty($assignmentMr) ? $assignmentMr->assignedUser->name : 'Belum di-assign' }}--}}
{{--                            </div>--}}
{{--                        </div>--}}

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
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->department->name }}
                            </div>
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-3 col-sm-3 col-xs-12">--}}
{{--                                Gudang--}}
{{--                            </label>--}}
{{--                            <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                : {{ $header->warehouse->name }}--}}
{{--                            </div>--}}
{{--                        </div>--}}

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
                                Unit Alat Berat
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
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
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->km ?? '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                HM
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

                        <div class="form-group">
                            <label class="col-md-3 col-sm-3 col-xs-12">
                                Request Oleh
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                : {{ $header->requested_by ?? '-' }}
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
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-right">
                        @if(!empty($header->pdf_path))
                            <a href="{{ route('admin.material_requests.pdf.download', ['material_request' => $header->id]) }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-download"></i>&nbsp;UNDUH BERITA ACARA
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <hr/>
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-xs-12 column">
                                <div class="row">
                                    <h4 class="text-center">Tracking MR</h4>
                                </div>
                                <div class="row">
                                    @if(!empty($trackedPrHeader))
                                        <div class="col-lg-2 col-md-2 col-xs-12 text-center">
                                            <h5>PR</h5>
                                            <a></a>
                                            <a href="{{ route('admin.purchase_requests.show', ['purchase_request' => $trackedPrHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $trackedPrHeader->code }}</a>
                                            @if($trackedPrHeader->status_id === 3)
                                                - <span style="color: green">{{ strtoupper($trackedPrHeader->status->description) }}</span><br/>
                                            @else
                                                - <span style="color: red">{{ strtoupper($trackedPrHeader->status->description) }}</span><br/>
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                            <h5>PO</h5>
                                            @if($trackedPoHeaders->count() > 0)
                                                @foreach($trackedPoHeaders as $poHeader)
                                                    <a href="{{ route('admin.purchase_orders.show', ['purchase_order' => $poHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $poHeader->code }}</a>
                                                    @if($poHeader->status_id === 3)
                                                        - <span style="color: green">{{ strtoupper($poHeader->status->description) }}</span><br/>
                                                    @else
                                                        - <span style="color: red">{{ strtoupper($poHeader->status->description) }}</span><br/>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-12 text-center">
                                            <h5>GR</h5>
                                            @if($trackedGrHeaders->count() > 0)
                                                @foreach($trackedGrHeaders as $grHeader)
                                                    <a href="{{ route('admin.item_receipts.show', ['item_receipt' => $grHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $grHeader->code }}</a><br/>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-xs-12 text-center">
                                            <h5>SJ</h5>
                                            @if($trackedSjHeaders->count() > 0)
                                                @foreach($trackedSjHeaders as $sjHeader)
                                                    <a href="{{ route('admin.delivery_orders.show', ['delivery_order' => $sjHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $sjHeader->code }}</a>
                                                    @if($sjHeader->status_id === 3)
                                                        - <span style="color: green">{{ strtoupper($sjHeader->status->description) }}</span><br/>
                                                    @else
                                                        - <span style="color: red">{{ strtoupper($sjHeader->status->description) }}</span><br/>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-xs-12 text-center">
                                            <h5>PI</h5>
                                            @if($trackedPiHeaders->count() > 0)
                                                @foreach($trackedPiHeaders as $piHeader)
                                                    <a href="{{ route('admin.purchase_invoices.show', ['purchase_invoice' => $piHeader->id]) }}" style="text-decoration: underline;" target="_blank">{{ $piHeader->code }}</a><br/>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif

                                    {{--<div class="col-lg-2 col-md-3 col-xs-12 text-center">--}}
                                        {{--<h5>ID</h5>--}}
                                        {{--@if($trackedIdHeaders->count() > 0)--}}
                                            {{--@foreach($trackedIdHeaders as $idHeader)--}}
                                                {{--<span>{{ $idHeader->code }}</span><br/>--}}
                                            {{--@endforeach--}}
                                        {{--@endif--}}
                                    {{--</div>--}}
                                </div>
                            </div>
                        </div>

                        <hr/>

                        {{--@if($header->status_id === 3)--}}
                            {{--@if($setting == 1)--}}
                                {{--@if($isApproved && !$isPrCreated)--}}
                                    {{--@if($itemStocks->count() > 0)--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label class="control-label col-lg-3 col-md-12 col-sm-12 col-xs-12">--}}
                                            {{--</label>--}}
                                            {{--<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">--}}
                                                {{--<div class="alert alert-success  fade in" role="alert">--}}
                                                    {{--Inventory tersedia di gudang sebagai berikut,--}}
                                                    {{--<br/>--}}
                                                    {{--<ul>--}}
                                                        {{--@foreach($itemStocks as $stock)--}}
                                                            {{--<li>{{ $stock->warehouse->name }} tersedia {{ $stock->item->code }} ({{ $stock->item->name }}) sebanyak {{ $stock->stock }} {{ $stock->item->uom }}</li>--}}
                                                        {{--@endforeach--}}
                                                    {{--</ul>--}}
                                                    {{--<br/>--}}
                                                    {{--<a class="btn btn-default" href="{{ route('admin.issued_dockets.create', ['mr' => $header->id]) }}">PROSES ISSUED DOCKET</a>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        {{--<hr/>--}}
                                    {{--@endif--}}
                                {{--@endif--}}
                            {{--@else--}}
                                {{--@if(!$isPrCreated)--}}
                                    {{--@if($itemStocks->count() > 0)--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label class="control-label col-lg-3 col-md-12 col-sm-12 col-xs-12">--}}
                                            {{--</label>--}}
                                            {{--<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">--}}
                                                {{--<div class="alert alert-success  fade in" role="alert">--}}
                                                    {{--Inventory tersedia di gudang sebagai berikut,--}}
                                                    {{--<br/>--}}
                                                    {{--<ul>--}}
                                                        {{--@foreach($itemStocks as $stock)--}}
                                                            {{--<li>{{ $stock->warehouse->name }} tersedia {{ $stock->item->code }} ({{ $stock->item->name }}) sebanyak {{ $stock->stock }} {{ $stock->item->uom }}</li>--}}
                                                        {{--@endforeach--}}
                                                    {{--</ul>--}}
                                                    {{--<br/>--}}
                                                    {{--<a class="btn btn-default" href="{{ route('admin.issued_dockets.create', ['mr' => $header->id]) }}">PROSES ISSUED DOCKET</a>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        {{--<hr/>--}}
                                    {{--@endif--}}
                                {{--@endif--}}
                            {{--@endif--}}
                        {{--@endif--}}

                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-xs-12 column">
                                <h4 class="text-center">Detil Inventory</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr >
                                            <th class="text-center" style="width: 15%">
                                                Kode Inventory
                                            </th>
                                            <th class="text-center" style="width: 15%">
                                                Nama Inventory
                                            </th>
                                            <th class="text-center" style="width: 15%">
                                                Part Number Asli
                                            </th>
                                            <th class="text-center" style="width: 10%">
                                                UOM
                                            </th>
                                            <th class="text-center" style="width: 10%">
                                                QTY
                                            </th>
                                            <th class="text-center" style="width: 10%">
                                                QTY Goods Receipt
                                            </th>
                                            <th class="text-center" style="width: 10%">
                                                QTY Issued Docket
                                            </th>
                                            <th class="text-center" style="width: 15%">
                                                Remark
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($header->material_request_details as $detail)
                                            <tr>
                                                <td>
                                                    {{ $detail->item->code }}
                                                </td>
                                                <td>
                                                    {{ $detail->item->name }}
                                                </td>
                                                <td>
                                                    {{ $detail->item->part_number ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $detail->item->uom }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $detail->quantity }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $detail->quantity_received }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $detail->quantity_issued }}
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