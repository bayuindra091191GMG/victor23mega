@extends('admin.layouts.admin')

@section('title','Data Issued Docket '. $header->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.issued_dockets') }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">
                <a class="btn btn-default" href="{{ route('admin.issued_dockets.print',[ 'material_request' => $header->id]) }}" target="_blank">CETAK</a>
                @if($header->status_id == 3)
                    <a class="btn btn-default" href="{{ route('admin.issued_dockets.edit',[ 'material_request' => $header->id]) }}">UBAH</a>
                @endif
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

                @if(count($errors))
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12 alert alert-danger alert-dismissible fade in" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor Issued Docket
                    </label>
                    <div class="col-md-6 col-sm-3 col-xs-12">
                        : {{ $header->code }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Tanggal
                    </label>
                    <div class="col-md-6 col-sm-3 col-xs-12">
                        : {{ \Carbon\Carbon::parse($header->date)->format('d M Y') }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Kode Cost
                    </label>
                    <div class="col-md-6 col-sm-3 col-xs-12">
                        : {{ $header->account->code ?? '-' }}
                    </div>
                </div>

                {{--<div class="form-group">--}}
                    {{--<label class="col-md-3 col-sm-3 col-xs-12">--}}
                        {{--Nomor MR--}}
                    {{--</label>--}}
                    {{--<div class="col-md-6 col-sm-3 col-xs-12">--}}
                        {{--@if($header->material_request_header->type == 1)--}}
                            {{--: <a style="text-decoration: underline;" href="{{ route('admin.material_requests.other.show', ['material_request' => $header->material_request_header_id]) }}" target="_blank">{{ $header->material_request_header->code }}</a>--}}
                        {{--@elseif($header->material_request_header->type == 2)--}}
                            {{--: <a style="text-decoration: underline;" href="{{ route('admin.material_requests.fuel.show', ['material_request' => $header->material_request_header_id]) }}" target="_blank">{{ $header->material_request_header->code }}</a>--}}
                        {{--@else--}}
                            {{--: <a style="text-decoration: underline;" href="{{ route('admin.material_requests.service.show', ['material_request' => $header->material_request_header_id]) }}" target="_blank">{{ $header->material_request_header->code }}</a>--}}
                        {{--@endif--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Status Retur
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $returStr ?? '-' }}
                    </div>
                </div>

                @if($header->type === 1)
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12">
                            Unit Alat Berat
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : {{ $header->machinery->code ?? '-' }}
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Departemen
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->department->name }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12" for="sn_chasis">
                        Divisi
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $header->division ?? '-' }}
                    </div>
                </div>

                @if($header->type === 1)
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
                            KM
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            : {{ $header->km ?? '-'}}
                        </div>
                    </div>
                @endif

                <hr>

                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <h4 class="text-center">Detil Inventory</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        Kode
                                    </th>
                                    <th class="text-center">
                                        Part Number
                                    </th>
                                    <th class="text-center">
                                        Keterangan
                                    </th>
                                    @if($header->type === 2)
                                        <th class="text-center">
                                            Unit Alat Berat
                                        </th>
                                    @endif
                                    <th class="text-center">
                                        UOM
                                    </th>
                                    <th class="text-center">
                                        QTY
                                    </th>
                                    <th class="text-center">
                                        QTY Retur
                                    </th>
                                    @if($header->type === 2)
                                        <th class="text-center">
                                            Shift
                                        </th>
                                        <th class="text-center">
                                            Jam
                                        </th>
                                        <th class="text-center">
                                            HM
                                        </th>
                                        <th class="text-center">
                                            KM
                                        </th>
                                        <th class="text-center">
                                            Fuelman
                                        </th>
                                        <th class="text-center">
                                            Operator
                                        </th>
                                    @endif
                                    <th class="text-center">
                                        Remark
                                    </th>
                                    <th class="text-center">
                                        Tindakan
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($header->issued_docket_details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->item->code }}
                                        </td>
                                        <td>
                                            {{ $detail->item->part_number ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $detail->item->name }}
                                        </td>
                                        @if($header->type === 2)
                                            <td class="text-center">
                                                {{ $detail->machinery->code }}
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            {{ $detail->item->uom }}
                                        </td>
                                        <td class="text-right">
                                            {{ $detail->quantity }}
                                        </td>
                                        <td class="text-right">
                                            {{ $detail->quantity_retur }}
                                        </td>
                                        @if($header->type === 2)
                                            <td class="text-center">
                                                {{ $detail->shift }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->time }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->hm }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->km }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->fuelman }}
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->operator }}
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            {{ $detail->remarks ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            <a class="retur-modal btn btn-warning" data-detail-id="{{ $detail->id }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}">
                                                RETUR
                                            </a>
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

    <!-- Modal form to return item -->
    <div id="returModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(['route' => 'admin.issued_docket_details.retur', 'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="item_retur">Inventory</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="item_retur" name="item_retur" readonly/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="qty_retur">
                                    QTY Retur
                                    <span class="required">*</span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="qty_retur" name="qty_retur" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="reason">
                                    Alasan
                                    <span class="required">*</span>
                                </label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="reason" name="reason" cols="40" rows="5" required></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="detail_id" name="detail_id"/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal">
                                <span class='glyphicon glyphicon-remove'></span> Batal
                            </button>
                            <button type="submit" class="btn btn-success add">
                                <span id="" class='glyphicon glyphicon-check'></span> Retur
                            </button>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
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
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    <script type="text/javascript">

        // Autonumeric
        qtyReturFormat = new AutoNumeric('#qty_retur', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        // Add new retur
        $(document).on('click', '.retur-modal', function() {
            $('.modal-title').text('Form Pengembalian Inventory');

            $('#detail_id').val($(this).data('detail-id'));
            $('#item_retur').val($(this).data('item-text'));

            $('#returModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    </script>
@endsection