@extends('admin.layouts.admin')

@section('title','Ubah Purchase Order '. $header->code)

@section('content')
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.purchase_orders.update', $header->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left', 'enctype' => 'multipart/form-data', 'novalidate']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="po_code">
                    Nomor PO
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="po_code" name="po_code" class="form-control col-md-12 col-xs-12" value="{{ $header->code }}" readonly/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ $date }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pr_code">
                    Nomor PR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pr_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('pph')) parsley-error @endif"
                           name="pr_code" value="{{ $header->purchase_request_header->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="supplier" >
                    Vendor
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="supplier" name="supplier" class="form-control col-md-7 col-xs-12 @if($errors->has('supplier')) parsley-error @endif">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="extra_discount">
                    Diskon
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="extra_discount" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_fee')) parsley-error @endif"
                           name="extra_discount">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="delivery_fee">
                    Ongkos Kirim
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="delivery_fee" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_fee')) parsley-error @endif"
                           name="delivery_fee">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ppn">
                    Tambah PPN
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="flat" id="ppn" name="ppn" @if(!empty($header->ppn_percent) && $header->ppn_percent > 0) checked @endif>
                            @if(!empty($header->ppn_percent) && $header->ppn_percent > 0) PPN tersimpan: {{ $header->ppn_percent }}% @endif
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pph">
                    PPh
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pph" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('pph')) parsley-error @endif"
                           name="pph">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment_term">
                    Payment Term (Hari)
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="payment_term" type="text" placeholder="JUMLAH HARI DALAM ANGKA" class="form-control col-md-2 col-xs-12 @if($errors->has('payment_term')) parsley-error @endif"
                           name="payment_term" value="{{ $header->payment_term }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="special_note">
                    Special Note
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <textarea id="special_note" name="special_note" rows="5" class="form-control col-md-7 col-xs-12 @if($errors->has('special_note')) parsley-error @endif" style="resize: vertical">{{ $header->special_note }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quotation1">
                    Lampiran Quotation Utama
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('quotation1', array('id' => 'quotation1', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quotation2">
                    Lampiran Quotation Tambahan 1
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('quotation2', array('id' => 'quotation2', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quotation3">
                    Lampiran Quotation Tambahan 2
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::file('quotation3', array('id' => 'quotation3', 'class' => 'file-loading', 'accept' => '.pdf, .PDF', 'data-show-preview' => 'false')) !!}
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-danger" href="{{ route('admin.purchase_orders.show', ['purchase_order' => $header->id]) }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 box-section">
            <h3 class='text-center'>Detil Inventory</h3>
            {{--<button class="add-modal btn btn-info" data-header-id="{{ $header->id }}">--}}
                {{--<span class="glyphicon glyphicon-plus-sign"></span> Tambah--}}
            {{--</button>--}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="detailTable">
                    <thead>
                    <tr >
                        <th class="text-center" style="width: 15%;">
                            Nomor Part
                        </th>
                        <th class="text-center" style="width: 5%;">
                            UOM
                        </th>
                        <th class="text-center" style="width: 10%;">
                            QTY
                        </th>
                        <th class="text-center" style="width: 10%;">
                            Harga
                        </th>
                        <th class="text-center" style="width: 10%;">
                            Diskon
                        </th>
                        <th class="text-center" style="width: 10%;">
                            Subtotal
                        </th>
                        <th class="text-center" style="width: 15%;">
                            Remark
                        </th>
                        <th class="text-center" style="width: 15%;">
                            Tindakan
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($header->purchase_order_details as $detail)
                        <tr class="item{{ $detail->id }}">
                            <td class='text-center'>
                                {{ $detail->item->code }} - {{ $detail->item->name }}
                            </td>
                            <td class="text-center">
                                {{ $detail->item->uom }}
                            </td>
                            <td class="text-center">
                                {{ $detail->quantity }}
                            </td>
                            <td class='text-right'>
                                {{ $detail->price_string }}
                            </td>
                            <td class="text-center">
                                {{ $detail->discount_string ?? '0' }}
                            </td>
                            <td class='text-right'>
                                {{ $detail->subtotal_string }}
                            </td>
                            <td>
                                {{ $detail->remark ?? '-' }}
                            </td>
                            <td class='text-center'>
                                <button class="edit-modal btn btn-info" data-id="{{ $detail->id }}" data-item-id="{{ $detail->item_id }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}" data-remark="{{ $detail->remark }}" data-price="{{ $detail->price }}" data-discount="{{ $detail->discount }}">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                                <button class="delete-modal btn btn-danger" data-id="{{ $detail->id }}" data-item-id="{{ $detail->item_id }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal form to add new detail -->
    <div id="addModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="item_add">Barang:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_add" name="item_add"></select>
                                <p class="errorItem text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_add">QTY:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_add" name="qty_add">
                                <p class="errorQty text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="price_add">Harga:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="price_add" name="price_add">
                                <p class="errorPrice text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="discount_add">Diskon:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="discount_add" name="discount_add">
                                <p class="errorDiscount text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_add">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_add" name="remark_add" cols="40" rows="5"></textarea>
                                <p class="errorRemark text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-success add" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-check'></span> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to edit a detail -->
    <div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="item_edit">Barang:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_edit" name="item_edit"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_edit">QTY:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_edit" name="qty_edit">
                                <p class="errorQty text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="price_edit">Harga:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="price_edit" name="price_edit">
                                <p class="errorPrice text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="discount_edit">Diskon:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="discount_edit" name="discount_edit">
                                <p class="errorDiscount text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_edit">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_edit" name="remark_edit" cols="40" rows="5"></textarea>
                                <p class="errorRemark text-center alert alert-danger hidden"></p>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-primary edit" data-dismiss="modal">
                            <span class='glyphicon glyphicon-check'></span> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to delete a form -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Apakah anda yakin ingin menghapus detail ini?</h3>
                    <br />
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="item_delete">Barang:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="item_delete" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_delete">QTY:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_delete" readonly>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-danger delete" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-trash'></span> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    {{ Html::style(mix('assets/admin/css/fileinput.css')) }}
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
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    {{ Html::script(mix('assets/admin/js/fileinput.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // FILEINPUT
        @if(!empty($pdfUrl1))
            $("#quotation1").fileinput({
                initialPreview: '{{ $pdfUrl1 }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#quotation1").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif

        @if(!empty($pdfUrl2))
            $("#quotation2").fileinput({
                initialPreview: '{{ $pdfUrl2 }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#quotation2").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif

        @if(!empty($pdfUrl3))
            $("#quotation3").fileinput({
                initialPreview: '{{ $pdfUrl3 }}',
                initialPreviewAsData: true,
                overwriteInitial: true,
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                showRemove: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @else
            $("#quotation3").fileinput({
                allowedFileExtensions: ["pdf"],
                showUpload: false,
                dropZoneTitle: "UNGGAH PDF DI SINI"
            });
        @endif


        var i=1;

        $('#supplier').select2({
            placeholder: {
                id: '{{ $header->supplier_id }}',
                text: '{{ $header->supplier->name }}'
            },
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route('select.suppliers') }}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        _token: $('input[name=_token]').val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $('#select0').select2({
            placeholder: {
                id: '-1',
                text: ' - Pilih Inventory - '
            },
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route('select.items') }}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        // Add autonumeric
        paymentTermFormat = new AutoNumeric('#payment_term', {
            minimumValue: '0',
            maximumValue: '999',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        qtyAddFormat = new AutoNumeric('#qty_add', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        qtyEditFormat = new AutoNumeric('#qty_edit', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0
        });

        pphFormat = new AutoNumeric('#pph', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($header->pph_amount) && $header->pph_amount > 0)
            pphFormat.clear();

            pphFormat.set('{{ $header->pph_amount }}', {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        priceAddFormat = new AutoNumeric('#price_add', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        deliveryFeeFormat = new AutoNumeric('#delivery_fee', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($header->delivery_fee))
            deliveryFeeFormat.clear();

            deliveryFeeFormat.set('{{ $header->delivery_fee }}', {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        extraDiscountFormat = new AutoNumeric('#extra_discount', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($header->extra_discount))
            extraDiscountFormat.clear();

            extraDiscountFormat.set('{{ $header->extra_discount }}', {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });
        @endif

        priceEditFormat = new AutoNumeric('#price_edit', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        discountAddFormat = new AutoNumeric('#discount_add', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        discountEditFormat = new AutoNumeric('#discount_edit', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        // Add new detail
        $(document).on('click', '.add-modal', function() {
            $('#item_add').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Inventory - '
                },
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('select.items') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term)
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('.modal-title').text('Tambah Detail');
            $('#addModal').modal('show');
        });
        $('.modal-footer').on('click', '.add', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.purchase_order_details.store') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'header_id': '{{ $header->id }}',
                    'item': $('#item_add').val(),
                    'qty': $('#qty_add').val(),
                    'price': $('#price_add').val(),
                    'discount': $('#discount_add').val(),
                    'remark': $('#remark_add').val()
                },
                success: function(data) {
                    $('.errorItem').addClass('hidden');
                    $('.errorQty').addClass('hidden');
                    $('.errorPrice').addClass('hidden');
                    $('.errorDiscount').addClass('hidden');
                    $('.errorRemark').addClass('hidden');

                    if ((data.errors)) {
                        setTimeout(function () {
                            $('#addModal').modal('show');
                            toastr.error('Gagal simpan data!', 'Peringatan', {timeOut: 5000});
                        }, 500);

                        if (data.errors.item) {
                            $('.errorItem').removeClass('hidden');
                            $('.errorItem').text(data.errors.item);
                        }
                        if (data.errors.qty) {
                            $('.errorQty').removeClass('hidden');
                            $('.errorQty').text(data.errors.qty);
                        }
                        if (data.errors.price) {
                            $('.errorQty').removeClass('hidden');
                            $('.errorQty').text(data.errors.qty);
                        }
                        if (data.errors.discount) {
                            $('.errorQty').removeClass('hidden');
                            $('.errorQty').text(data.errors.qty);
                        }
                        if (data.errors.remark) {
                            $('.errorRemark').removeClass('hidden');
                            $('.errorRemark').text(data.errors.remark);
                        }
                    } else {
                        toastr.success('Berhasil simpan detail!', 'Sukses', {timeOut: 5000});
                        var remarkAdd = '-';
                        if (data.remark !== null) {
                            remarkAdd = data.remark;
                        }
                        $('#detailTable').append("<tr class='item" + data.id + "'><td class='text-center'>" + data.item.code + " - " + data.item.name + "</td><td class='text-center'>" + data.item.uom + "</td><td class='text-center'>" + data.quantity + "</td><td class='text-right'>" + data.price_string + "</td><td class='text-center'>" + data.discount_string + "</td><td class='text-right'>" + data.subtotal_string + "</td><td>" + remarkAdd + "</td><td class='text-center'>" + "<button class='edit-modal btn btn-info' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " " + data.item.name + "' data-qty='" + data.quantity + "' data-remark='" + data.remark + "' data-price='" + data.price + "' data-discount='" + data.discount + "'><span class='glyphicon glyphicon-edit'></span></button><button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " - "  + data.item.name + "' data-qty='" + data.quantity + "' data-price='" + data.price + "' data-discount='" + data.discount + "'><span class='glyphicon glyphicon-trash'></span></button></td></tr>");

                        // Reset add form modal
                        $('#qty_add').val('');
                        $('#price_add').val('');
                        $('#discount_add').val('');
                        $('#remark_add').val('');
                        $('#item_add').val(null).trigger('change');
                    }
                },
            });
        });

        // Edit detail
        $(document).on('click', '.edit-modal', function() {
            id = $(this).data('id');

            $('.modal-title').text('Ubah Detail');

            $('#item_edit').select2({
                placeholder: {
                    id: $(this).data('item-id'),
                    text: $(this).data('item-text')
                },
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('select.items') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term)
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#remark_edit').val($(this).data('remark'));

            qtyEditFormat.clear();
            qtyEditFormat.set($(this).data('qty'),{
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0,
                modifyValueOnWheel: false
            });

            priceEditFormat.clear();
            priceEditFormat.set($(this).data('price'), {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });

            discountEditFormat.clear();
            discountEditFormat.set($(this).data('discount'), {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });

            $('#editModal').modal('show');
        });
        $('.modal-footer').on('click', '.edit', function() {
            $.ajax({
                type: 'PUT',
                url: '{{ route('admin.purchase_order_details.update') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id' : id,
                    'item': $("#item_edit").val(),
                    'qty': $('#qty_edit').val(),
                    'price': $('#price_edit').val(),
                    'discount': $('#discount_edit').val(),
                    'remark': $('#remark_edit').val()
                },
                success: function(data) {
                    $('.errorQty').addClass('hidden');
                    $('.errorRemark').addClass('hidden');
                    $('.errorPrice').addClass('hidden');
                    $('.errorDiscount').addClass('hidden');

                    if ((data.errors)) {
                        setTimeout(function () {
                            $('#editModal').modal('show');
                            toastr.error('Gagal ubah detail!', 'Peringatan', {timeOut: 5000});
                        }, 500);

                        if (data.errors.qty) {
                            $('.errorQty').removeClass('hidden');
                            $('.errorQty').text(data.errors.qty);
                        }
                        if (data.errors.price) {
                            $('.errorQty').removeClass('hidden');
                            $('.errorQty').text(data.errors.qty);
                        }
                        if (data.errors.discount) {
                            $('.errorQty').removeClass('hidden');
                            $('.errorQty').text(data.errors.qty);
                        }
                        if (data.errors.remark) {
                            $('.errorRemark').removeClass('hidden');
                            $('.errorRemark').text(data.errors.remark);
                        }
                    } else {
                        toastr.success('Berhasil ubah data!', 'Sukses', {timeOut: 5000});
                        var remarkEdit = '-';
                        if (data.remark !== null) {
                            remarkEdit = data.remark;
                        }
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'><td class='text-center'>" + data.item.code + " - " + data.item.name + "</td><td class='text-center'>" + data.item.uom + "</td><td class='text-center'>" + data.quantity + "</td><td class='text-right'>" + data.price_string + "</td><td class='text-center'>" + data.discount_string + "</td><td class='text-right'>" + data.subtotal_string + "</td><td>" + remarkEdit + "</td><td class='text-center'>" + "<button class='edit-modal btn btn-info' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " " + data.item.name + "' data-qty='" + data.quantity + "' data-remark='" + data.remark + "' data-price='" + data.price + "' data-discount='" + data.discount + "'><span class='glyphicon glyphicon-edit'></span></button><button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " - "  + data.item.name + "' data-qty='" + data.quantity + "' data-price='" + data.price + "' data-discount='" + data.discount + "'><span class='glyphicon glyphicon-trash'></span></button></td></tr>");
                    }
                }
            });
        });

        // Delete detail
        $(document).on('click', '.delete-modal', function() {
            $('.modal-title').text('Hapus Detail');
            $('#item_delete').val($(this).data('item-text'));
            $('#qty_delete').val($(this).data('qty'));
            $('#remark_delete').val($(this).data('remark'));
            $('#deleteModal').modal('show');
            deletedId = $(this).data('id')
        });
        $('.modal-footer').on('click', '.delete', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.purchase_order_details.delete') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': deletedId,
                    'header_id': '{{ $header->id }}'
                },
                success: function(data) {
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal hapus detail!', 'Peringatan', {timeOut: 5000});
                        }, 500);
                    }
                    else{
                        toastr.success('Berhasil menghapus detail!', 'Sukses', {timeOut: 5000});
                        $('.item' + data['id']).remove();
                    }
                }
            });
        });
    </script>
@endsection