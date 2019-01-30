@extends('admin.layouts.admin')

@section('title','Buat Purchase Invoice Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.purchase_invoices.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">
                    Nomor Invoice
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" value="{{ $autoNumber }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_number"></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="flat" id="auto_number" name="auto_number" checked="checked"> Auto Number
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="po_code" >
                    Nomor PO
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="po_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="po_code" value="{{ $purchaseOrder->code }}" readonly>
                    <input type="hidden" id="po_id" name="po_id" value="{{ $purchaseOrder->id }}">
                </div>
                {{--<div class="col-md-2 col-sm-2 col-xs-12">--}}
                    {{--<a class="get-po-data btn btn-info">--}}
                        {{--Ambil Data--}}
                    {{--</a>--}}
                    {{--@if(!empty($purchaseOrder))--}}
                        {{--<a class="clear-po-data btn btn-info">--}}
                            {{--Reset Data--}}
                        {{--</a>--}}
                    {{--@endif--}}
                {{--</div>--}}
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ old('date') }}" required>
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
                            <input type="checkbox" class="flat" id="ppn" name="ppn" @if(!empty($purchaseOrder) && !empty($purchaseOrder->ppn_amount)) checked @endif> PPN sekarang: 10%
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
                           name="pph" value="{{ old('pph') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment_term">
                    Payment Term (Hari)
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="payment_term" type="text" placeholder="Hari Dalam Angka" class="form-control col-md-2 col-xs-12 @if($errors->has('payment_term')) parsley-error @endif"
                           name="payment_term" value="{{ $purchaseOrder->payment_term ?? '' }}">
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-xs-12 box-section">
                    <h3 class="text-center">Detil Inventory</h3>

                    @if(empty($purchaseOrder))
                        <a class="add-modal btn btn-info" style="margin-bottom: 10px;">
                            <span class="glyphicon glyphicon-plus-sign"></span> Tambah
                        </a>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail_table">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 20%">
                                    Nomor Part
                                </th>
                                <th class="text-center" style="width: 10%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Harga
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Diskon
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Subtotal
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Remark
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Tindakan
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php( $idx = 0 )
                            @if(!empty($purchaseOrder))
                                @foreach($purchaseOrder->purchase_order_details as $detail)
                                    @if($detail->quantity_invoiced < $detail->quantity)
                                        @php( $idx++ )
                                        <tr class='item{{ $idx }}'>
                                            <td>
                                                {{ $detail->item->code. ' - '. $detail->item->name }}
                                                <input type='hidden' name='item_text[]' value='{{ $detail->item->code. ' - '. $detail->item->name }}'/>
                                                <input type='hidden' name='item_value[]' value='{{ $detail->item_id }}'/>
                                            </td>
                                            <td>
                                                @php( $qty = $detail->quantity - $detail->quantity_invoiced )
                                                <input type='text' name='qty[]' class='form-control text-center' value='{{ $qty }}' readonly/>
                                            </td>
                                            <td>
                                                <input type='text' name='price[]' class='form-control text-right' value='{{ $detail->price_string }}' readonly/>
                                            </td>
                                            <td>
                                                <input type='text' name='discount[]' class='form-control text-right' value='{{ $detail->discount_string }}' readonly/>
                                            </td>
                                            <td>
                                                @php( $discount = $detail->discount ?? 0 )
                                                @php( $subtotal = $qty * $detail->price - $discount )
                                                @php( $subtotalString = number_format($subtotal, 2, ",", ".") )
                                                <input type='text' name='subtotal[]' class='form-control text-right' value='{{ $subtotalString }}' readonly/>
                                            </td>
                                            <td>
                                                <input type='text' name='remark[]' class='form-control' value='{{ $detail->remark }}' readonly/>
                                            </td>
                                            <td class='text-center'>
                                                @php( $itemId = $detail->item_id. "#". $detail->item->code. "#". $detail->item->name )
                                                <a class="edit-modal btn btn-info" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $qty }}" data-remark="{{ $detail->remark }}" data-price="{{ $detail->price }}" data-discount="{{ $detail->discount }}">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                                <a class="delete-modal btn btn-danger" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $qty }}" data-price="{{ $detail->price }}" data-discount="{{ $detail->discount }}">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <input id="index_counter" type="hidden" value="{{ $idx }}"/>

            <hr/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.purchase_invoices') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>
            {{ Form::close() }}
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
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_add">QTY:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_add" name="qty_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="price_add">Harga:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="price_add" name="price_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="discount_add">Diskon:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="discount_add" name="discount_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_add">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_add" name="remark_add" cols="40" rows="5"></textarea>
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
                                <input type="hidden" id="item_old_value"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_edit">QTY:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_edit" name="qty_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="price_edit">Harga:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="price_edit" name="price_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="discount_edit">Diskon:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="discount_edit" name="discount_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="remark_edit">Remark:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark_edit" name="remark_edit" cols="40" rows="5"></textarea>
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
                                <input type="text" class="form-control" id="item_delete" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_delete">Jumlah:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_delete" disabled>
                            </div>
                        </div>
                        <input type="hidden" name="deleted_id"/>
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
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    {{ Html::script(mix('assets/admin/js/stringbuilder.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        // Auto Numbering
        $('#auto_number').change(function(){
            if(this.checked){
                $('#code').val('{{ $autoNumber }}');
                $('#code').prop('readonly', true);
            }
            else{
                $('#code').val('');
                $('#code').prop('readonly', false);
            }
        });

        {{--@if(!empty($purchaseOrder))--}}
        {{--$('#po_code').select2({--}}
            {{--placeholder: {--}}
                {{--id: '{{ $purchaseOrder->id }}',--}}
                {{--text: '{{ $purchaseOrder->code }}'--}}
            {{--},--}}
            {{--width: '100%',--}}
            {{--minimumInputLength: 1,--}}
            {{--ajax: {--}}
                {{--url: '{{ route('select.purchase_orders') }}',--}}
                {{--dataType: 'json',--}}
                {{--data: function (params) {--}}
                    {{--return {--}}
                        {{--q: $.trim(params.term)--}}
                    {{--};--}}
                {{--},--}}
                {{--processResults: function (data) {--}}
                    {{--return {--}}
                        {{--results: data--}}
                    {{--};--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}
        {{--@else--}}
        {{--$('#po_code').select2({--}}
            {{--placeholder: {--}}
                {{--id: '-1',--}}
                {{--text: 'Pilih Nomor PO...'--}}
            {{--},--}}
            {{--width: '100%',--}}
            {{--minimumInputLength: 1,--}}
            {{--ajax: {--}}
                {{--url: '{{ route('select.purchase_orders') }}',--}}
                {{--dataType: 'json',--}}
                {{--data: function (params) {--}}
                    {{--return {--}}
                        {{--q: $.trim(params.term)--}}
                    {{--};--}}
                {{--},--}}
                {{--processResults: function (data) {--}}
                    {{--return {--}}
                        {{--results: data--}}
                    {{--};--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}
        {{--@endif--}}


        {{--$('#supplier').select2({--}}
            {{--placeholder: {--}}
                {{--id: '-1',--}}
                {{--text: 'Pilih Vendor...'--}}
            {{--},--}}
            {{--width: '100%',--}}
            {{--minimumInputLength: 1,--}}
            {{--ajax: {--}}
                {{--url: '{{ route('select.suppliers') }}',--}}
                {{--dataType: 'json',--}}
                {{--data: function (params) {--}}
                    {{--return {--}}
                        {{--q: $.trim(params.term),--}}
                        {{--_token: $('input[name=_token]').val()--}}
                    {{--};--}}
                {{--},--}}
                {{--processResults: function (data) {--}}
                    {{--return {--}}
                        {{--results: data--}}
                    {{--};--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}

        // Add autonumeric
        qtyAddFormat = new AutoNumeric('#qty_add', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            modifyValueOnWheel: false
        });

        qtyEditFormat = new AutoNumeric('#qty_edit', {
            minimumValue: '0',
            digitGroupSeparator: '',
            decimalPlaces: 0,
            modifyValueOnWheel: false
        });

        pphFormat = new AutoNumeric('#pph', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            minimumValue: '0',
            decimalPlaces: 2,
            allowDecimalPadding: false,
            modifyValueOnWheel: false
        });

        @if(!empty($purchaseOrder) && !empty($purchaseOrder->pph_amount) && $purchaseOrder->pph_amount > 0)
            pphFormat.clear();

            pphFormat.set('{{ $purchaseOrder->pph_amount }}', {
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

        @if(!empty($purchaseOrder) && !empty($purchaseOrder->delivery_fee) && $purchaseOrder->delivery_fee > 0)
            deliveryFeeFormat.clear();

            deliveryFeeFormat.set('{{ $purchaseOrder->delivery_fee }}', {
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

        @if(!empty($purchaseOrder) && !empty($purchaseOrder->extra_discount) && $purchaseOrder->extra_discount > 0)
            extraDiscountFormat.clear();

            extraDiscountFormat.set('{{ $purchaseOrder->extra_discount }}', {
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

        {{--// Get selected PO data--}}
        {{--$(document).on('click', '.get-po-data', function(){--}}
            {{--var url = '{{ route('admin.purchase_invoices.create') }}';--}}
            {{--if($('#po_code').val() && $('#po_code').val() !== ""){--}}
                {{--url += "?po=" + $('#po_code').val();--}}
                {{--window.location = url;--}}
            {{--}--}}
            {{--else{--}}
                {{--if($('#po_id').val() && $('#po_id').val() !== ""){--}}
                    {{--url += "?po=" + $('#po_id').val();--}}
                    {{--window.location = url;--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}

        {{--// Clear selected PO data--}}
        {{--$(document).on('click', '.clear-po-data', function(){--}}
            {{--var url = '{{ route('admin.purchase_invoices.create') }}';--}}
            {{--window.location = url;--}}
        {{--});--}}

        // Add new detail
        $(document).on('click', '.add-modal', function() {
            $('#item_add').select2({
                placeholder: {
                    id: '-1',
                    text: 'Pilih barang...'
                },
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('select.extended_items') }}',
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
            $('#addModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
        $('.modal-footer').on('click', '.add', function() {
            var qtyAdd = $('#qty_add').val();
            var itemAdd = $('#item_add').val();

            if(!itemAdd || itemAdd === ""){
                alert('Mohon pilih inventory!');
                return false;
            }

            if(!qtyAdd || qtyAdd === "" || qtyAdd === "0"){
                alert('Mohon isi kuantitas!')
                return false;
            }

            var priceAdd = $('#price_add').val();

            if(!priceAdd || priceAdd === "" || priceAdd === "0"){
                alert('Mohon isi harga!')
                return false;
            }

            var discountAdd = $('#discount_add').val();
            var remarkAdd = $('#remark_add').val();

            // Split item value
            var splitted = itemAdd.split('#');

            // Filter variables
            var qty = parseFloat(qtyAdd);
            var price = 0;
            if(priceAdd && priceAdd !== "" && priceAdd !== "0"){
                var priceClean = priceAdd.replace(/\./g,'');
                var priceClean2 = priceClean.replace(',', '.');
                price = parseFloat(priceClean2);
            }
            var discount = 0;
            if(discountAdd && discountAdd !== "" && discountAdd !== "0"){
                var discountClean = discountAdd.replace(/\./g,'');
                var discountClean2 = discountClean.replace(',', '.');
                discount = parseFloat(discountClean2);

                // Validate discount
                if(discount > (price * qty)){
                    alert('Diskon tidak boleh melebihi harga!')
                    return false;
                }
            }

            // Increase idx
            var idx = $('#index_counter').val();
            idx++;
            $('#index_counter').val(idx);

            var sbAdd = new stringbuilder();

            sbAdd.append("<tr class='item" + idx + "'>");
            sbAdd.append("<td>" + splitted[1] + " - " + splitted[2] + "<input type='hidden' name='item_text[]' value='" + splitted[1] + " - " + splitted[2] + "'/>")
            sbAdd.append("<input type='hidden' name='item_value[]' value='" + splitted[0] + "'/></td>");
            if(qtyAdd && qtyAdd !== ""){
                sbAdd.append("<td><input type='text' name='qty[]' class='form-control text-center' value='" + qtyAdd + "' readonly/></td>");
            }
            else{
                sbAdd.append("<td><input type='text' name='qty[]' class='form-control text-center' readonly/></td>");
            }

            if(priceAdd && priceAdd !== "" && priceAdd !== "0"){
                sbAdd.append("<td><input type='text' name='price[]' class='form-control text-right' value='" + priceAdd + "' readonly/></td>");
            }
            else{
                sbAdd.append("<td><input type='text' name='price[]' class='form-control text-right' value='0' readonly/></td>");
            }

            if(discount > 0){
                sbAdd.append("<td><input type='text' name='discount[]' class='form-control text-right' value='" + discountAdd + "' readonly/></td>");
            }
            else{
                sbAdd.append("<td><input type='text' name='discount[]' class='form-control text-right' value='0' readonly/></td>");
            }

            var subtotal = 0;
            var totalPrice = price * qty;
            if(discount > 0){
                subtotal = totalPrice - discount;
            }
            else{
                subtotal = totalPrice;
            }
            var subtotalString = rupiahFormat(subtotal);

            sbAdd.append("<td><input type='text' class='form-control' value='" + subtotalString + "' readonly/></td>");
            sbAdd.append("<td><input type='text' name='remark[]' class='form-control' value='" + remarkAdd + "' readonly/></td>");

            sbAdd.append("<td class='text-center'>");
            sbAdd.append("<a class='edit-modal btn btn-info' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "' data-price='" + price + "' data-discount='" + discount + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbAdd.append("<a class='delete-modal btn btn-danger' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "' data-price='" + price + "' data-discount='" + discount + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbAdd.append("</td>");
            sbAdd.append("</tr>");

            $('#detail_table').append(sbAdd.toString());

            // Reset add form modal
            $('#qty_add').val('');
            priceAddFormat.clear();
            $('#discount_add').val('');
            $('#remark_add').val('');
            $('#item_add').val(null).trigger('change');
        });

        // Edit detail
        $(document).on('click', '.edit-modal', function() {
            id = $(this).data('id');

            $('.modal-title').text('Ubah Detail');

            $('#item_old_value').val($(this).data('item-id'));
            $('#item_edit').select2({
                placeholder: {
                    id: $(this).data('item-id'),
                    text: $(this).data('item-text')
                },
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('select.extended_items') }}',
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

            $('#qty_edit').val($(this).data('qty'));
            $('#remark_edit').val($(this).data('remark'));
            // $('#discount_edit').val($(this).data('discount'));
            $('#editModal').modal('show');

            priceEditFormat.clear();

            priceEditFormat.set($(this).data('price'), {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });

            discountEditFormat.set($(this).data('discount'), {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 2,
                allowDecimalPadding: false,
                modifyValueOnWheel: false
            });

        });
        $('.modal-footer').on('click', '.edit', function() {
            var itemEdit = $('#item_edit').val();
            var qtyEdit = $('#qty_edit').val();
            var priceEdit = $('#price_edit').val();
            var discountEdit = $('#discount_edit').val();
            var remarkEdit = $('#remark_edit').val();

            if(!qtyEdit || qtyEdit === "" || qtyEdit === "0"){
                alert('Mohon isi jumlah...')
                return false;
            }

            if(!priceEdit || priceEdit === "" || priceEdit === "0"){
                alert('Mohon isi harga...')
                return false;
            }

            // Split item value
            var data = "default";
            if(itemEdit && itemEdit !== ''){
                alert(itemEdit);
                data = itemEdit;
            }
            else {
                data = $('#item_old_value').val();
            }

            var splitted = data.split('#');

            // Filter variables
            var qty = parseFloat(qtyEdit);
            var price = 0;
            if(priceEdit && priceEdit !== "" && priceEdit !== "0"){
                var priceClean = priceEdit.replace(/\./g,'');
                var priceClean2 = priceClean.replace(',', '.');
                price = parseFloat(priceClean2);
            }
            var discount = 0;
            if(discountEdit && discountEdit !== "" && discountEdit !== "0"){
                var discountClean = discountEdit.replace(/\./g,'');
                var discountClean2 = discountClean.replace(',', '.');
                discount = parseFloat(discountClean2);

                // Validate discount
                if(discount > (price * qty)){
                    alert('Diskon tidak boleh melebihi harga!')
                    return false;
                }
            }

            var sbEdit = new stringbuilder();

            sbEdit.append("<tr class='item" + id + "'>");
            sbEdit.append("<td>" + splitted[1] + ' - ' + splitted[2] + "<input type='hidden' name='item_text[]' value='" + splitted[1] + ' - ' + splitted[2] + "'/>")
            sbEdit.append("<input type='hidden' name='item_value[]' value='" + splitted[0] + "'/></td>");
            if(qtyEdit && qtyEdit !== ""){
                sbEdit.append("<td><input type='text' name='qty[]' class='form-control text-center' value='" + qtyEdit + "' readonly/></td>");
            }
            else{
                sbEdit.append("<td><input type='text' name='qty[]' class='form-control text-center' readonly/></td>");
            }

            if(priceEdit && priceEdit !== "" && priceEdit !== "0"){
                var priceEditStr = rupiahFormat(price);
                sbEdit.append("<td><input type='text' name='price[]' class='form-control text-right' value='" + priceEditStr + "' readonly/></td>");
            }
            else{
                sbEdit.append("<td><input type='text' name='price[]' class='form-control text-right' value='0,00' readonly/></td>");
            }

            if(discount > 0){
                var discountEditStr = rupiahFormat(discount);
                sbEdit.append("<td><input type='text' name='discount[]' class='form-control text-right' value='" + discountEditStr + "' readonly/></td>");
            }
            else{
                sbEdit.append("<td><input type='text' name='discount[]' class='form-control text-right' value='0,00' readonly/></td>");
            }

            var subtotal = 0;
            var totalPrice = price * qty;
            if(discount > 0){
                subtotal = totalPrice - discount;
            }
            else{
                subtotal = totalPrice;
            }
            var subtotalString = rupiahFormat(subtotal);

            sbEdit.append("<td><input type='text' class='form-control text-right' value='" + subtotalString + "' readonly/></td>");
            sbEdit.append("<td><input type='text' name='remark[]' class='form-control' value='" + remarkEdit + "' readonly/></td>");

            sbEdit.append("<td class='text-center'>");
            sbEdit.append("<a class='edit-modal btn btn-info' data-id='" + id + "' data-item-id='" + data + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "' data-price='" + price + "' data-discount='" + discount + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbEdit.append("<a class='delete-modal btn btn-danger' data-id='" + id + "' data-item-id='" + data + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "' data-price='" + price + "' data-discount='" + discount + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbEdit.append("</td>");
            sbEdit.append("</tr>");

            $('.item' + id).replaceWith(sbEdit.toString());
        });

        // Delete detail
        var deletedId = "0";
        $(document).on('click', '.delete-modal', function() {
            $('.modal-title').text('Hapus Detail');
            deletedId = $(this).data('id');
            $('#item_delete').val($(this).data('item-text'));
            $('#qty_delete').val($(this).data('qty'));
            $('#remark_delete').val($(this).data('remark'));
            $('#deleteModal').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function() {
            $('.item' + deletedId).remove();
        });

        function rupiahFormat(nStr) {
            // nStr += '';
            // x = nStr.split(',');
            // x1 = x[0];
            // x2 = x.length > 1 ? '.' + x[1] : '';
            // var rgx = /(\d+)(\d{3})/;
            // while (rgx.test(x1)) {
            //     x1 = x1.replace(rgx, '$1' + '.' + '$2');
            // }
            // return x1 + x2;
            return nStr.toLocaleString(
                "de-DE",
                {minimumFractionDigits: 2}
            );
        }
    </script>
@endsection