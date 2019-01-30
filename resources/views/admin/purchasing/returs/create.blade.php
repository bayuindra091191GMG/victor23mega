@extends('admin.layouts.admin')

@section('title','Buat Retur Baru')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.returs.store'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="retur_code">
                    Nomor Retur
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="retur_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('po_code')) parsley-error @endif"
                           name="retur_code" value="{{ $autoNumber }}" readonly>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pi_code" >
                    Nomor Invoice
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="pi_code" class="form-control col-md-7 col-xs-12 @if($errors->has('pi_code')) parsley-error @endif"
                           name="pi_code" value="{{ $purchaseInvoice->code }}" readonly>
                    <input type="hidden" id="pi_id" name="pi_id" value="{{ $purchaseInvoice->id }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="supplier" >
                    Vendor
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="supplier" name="supplier" class="form-control col-md-7 col-xs-12" value="{{ $purchaseInvoice->purchase_order_header->supplier->name }}" readonly>
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
                            <input type="checkbox" class="flat" id="ppn" name="ppn" @if(!empty($purchaseInvoice->ppn_amount) && $purchaseInvoice->ppn_amount > 0) checked @endif> PPN sekarang: 10%
                        </label>
                    </div>
                </div>
            </div>

            <hr/>

            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-xs-12 box-section">
                    <h3 class="text-center">Detil Inventory</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail_table">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 15%">
                                    Nomor Part
                                </th>
                                <th colspan="2" class="text-center" style="width: 10%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Gudang
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Harga
                                </th>
                                <th class="text-center" style="width: 10%">
                                    Diskon
                                </th>
                                <th class="text-center" style="width: 10%">
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
                            <?php $idx = 0; ?>
                            @if(!empty($purchaseInvoice))
                                @foreach($purchaseInvoice->purchase_invoice_details as $detail)
                                    <?php $idx++; ?>
                                    <tr class='item{{ $idx }}'>
                                        <td class='text-center'>
                                            {{ $detail->item->code. ' - '. $detail->item->name }}
                                            <input type='hidden' name='item_text[]' value='{{ $detail->item->code. ' - '. $detail->item->name }}'/>
                                            <input type='hidden' name='item_value[]' value='{{ $detail->item_id }}'/>
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->quantity }}
                                            <input type='hidden' name='qty[]' value='{{ $detail->quantity }}'/>
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->item->uom }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->warehouse->name ?? '-' }}
                                            <input type='hidden' name='warehouse[]'/>
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->price_string }}
                                            <input type='hidden' name='price[]' value='{{ $detail->price_string }}'/>
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->discount_string }}
                                            <input type='hidden' name='discount[]' value='{{ $detail->discount_string }}'/>
                                        </td>
                                        <td class='text-right'>
                                            {{ $detail->subtotal_string }}
                                            <input type='hidden' name='subtotal[]' value='{{ $detail->subtotal_string }}'/>
                                        </td>
                                        <td>
                                            {{ $detail->remark ?? '-' }}
                                            <input type='hidden' name='remark[]' value='{{ $detail->remark }}'/>
                                        </td>
                                        <td class='text-center'>
                                            <?php $itemId = $detail->item_id. "#". $detail->item->code. "#". $detail->item->name. "#". $detail->item->uom ?>
                                            <a class="edit-modal btn btn-info" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}" data-warehouse-id="-1" data-warehouse-text="- Pilih Gudang -" data-remark="{{ $detail->remark }}" data-price="{{ $detail->price }}" data-discount="{{ $detail->discount }}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <a class="delete-modal btn btn-danger" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}" data-warehouse-id="-1" data-warehouse-text="- Pilih Gudang -" data-price="{{ $detail->price }}" data-discount="{{ $detail->discount }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
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
                    <a class="btn btn-danger" href="{{ route('admin.returs') }}"> Batal</a>
                    <a class="btn btn-success confirm-modal">Simpan</a>
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
                            <label class="control-label col-sm-2" for="warehouse_edit">Gudang Pengambilan:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="warehouse_edit" name="warehouse_edit"></select>
                                <input type="hidden" id="warehouse_old_value"/>
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
                                <input type="text" class="form-control" id="item_delete" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qty_delete">QTY:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="qty_delete" readonly>
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

    <!-- Modal form to confirm submit -->
    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body text-center">
                    <h3>Apakah anda yakin ingin menyimpan Retur ini?</h3>
                    <span style="color: red;">Akan terjadi pengurangan stok setelah menyimpan Retur</span>
                    <br/>
                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Batal
                        </button>
                        <button type="button" class="btn btn-danger confirm" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-check'></span> Simpan
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
                $('#po_code').val('{{ $autoNumber }}');
                $('#po_code').prop('readonly', true);
            }
            else{
                $('#po_code').val('');
                $('#po_code').prop('readonly', false);
            }
        });

        // Confirm before submit
        $(document).on('click', '.confirm-modal', function() {
            $('#confirmModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('.modal-footer').on('click', '.confirm', function(){
            $('#general-form').submit();
        });

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

        @if(!empty($purchaseInvoice->delivery_fee) && $purchaseInvoice->delivery_fee > 0)
            deliveryFeeFormat.clear();

            var deliveryFee = '{{ $purchaseInvoice->delivery_fee }}';
            var deliveryFeeClean = deliveryFee.replace(/\./g,'');

            deliveryFeeFormat.set(deliveryFeeClean, {
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

        @if(!empty($purchaseInvoice->extra_discount) && $purchaseInvoice->extra_discount > 0)
            extraDiscountFormat.clear();

            var extraDiscount = '{{ $purchaseInvoice->extra_discount }}';
            var extraDiscountClean = extraDiscount.replace(/\./g,'');

            extraDiscountFormat.set(extraDiscountClean, {
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
            var priceAdd = $('#price_add').val();

            if(!itemAdd || itemAdd === ""){
                alert('Mohon pilih inventory!');
                return false;
            }

            if(!qtyAdd || qtyAdd === "" || qtyAdd === "0"){
                alert('Mohon isi kuantitas!')
                return false;
            }

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
            sbAdd.append("<td class='field-item'><input type='text' name='item_text[]' class='form-control' value='" + splitted[1] + " - " + splitted[2] + "' readonly/>")
            sbAdd.append("<input type='hidden' name='item_value[]' value='" + splitted[0] + "'/></td>");
            if(qtyAdd && qtyAdd !== ""){
                sbAdd.append("<td><input type='text' name='qty[]' class='form-control' value='" + qtyAdd + "' readonly/></td>");
            }
            else{
                sbAdd.append("<td><input type='text' name='qty[]' class='form-control' readonly/></td>");
            }

            if(priceAdd && priceAdd !== "" && priceAdd !== "0"){
                sbAdd.append("<td><input type='text' name='price[]' class='form-control' value='" + priceAdd + "' readonly/></td>");
            }
            else{
                sbAdd.append("<td><input type='text' name='price[]' class='form-control' value='0' readonly/></td>");
            }

            if(discount > 0){
                sbAdd.append("<td><input type='text' name='discount[]' class='form-control' value='" + discountAdd + "' readonly/></td>");
            }
            else{
                sbAdd.append("<td><input type='text' name='discount[]' class='form-control' value='0' readonly/></td>");
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
            $('#price_add').val('');
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

            $('#warehouse_old_value').val($(this).data('warehouse-id'));
            $('#warehouse_edit').select2({
                placeholder: {
                    id: $(this).data('warehouse-id'),
                    text: $(this).data('warehouse-text')
                },
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('select.extended_warehouses') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term),
                            mode: 'create_retur'
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
                decimalPlaces: 0
            });

            priceEditFormat.clear();
            priceEditFormat.set($(this).data('price'), {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 0
            });

            discountEditFormat.clear();
            discountEditFormat.set($(this).data('discount'), {
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                minimumValue: '0',
                decimalPlaces: 0
            });

            $('#editModal').modal('show');
        });
        $('.modal-footer').on('click', '.edit', function() {
            var itemEdit = $('#item_edit').val();
            var qtyEdit = $('#qty_edit').val();
            var warehouseEdit = $('#warehouse_edit').val();
            var priceEdit = $('#price_edit').val();
            var discountEdit = $('#discount_edit').val();
            var remarkEdit = $('#remark_edit').val();

            if(!qtyEdit || qtyEdit === "" || qtyEdit === "0"){
                alert('Mohon isi kuantitas!')
                return false;
            }

            if(!priceEdit || priceEdit === "" || priceEdit === "0"){
                alert('Mohon isi harga!')
                return false;
            }

            var warehouseOldValue = $('#warehouse_old_value').val();;
            if(!warehouseEdit && warehouseOldValue === '-1'){
                alert('Mohon isi gudang pengambilan!')
                return false;
            }

            // Split item value
            var data = "default";
            if(itemEdit && itemEdit !== ''){
                data = itemEdit;
            }
            else {
                data = $('#item_old_value').val();
            }

            var splitted = data.split('#');

            // Split warehouse valie
            var warehouseData = "default";
            if(warehouseEdit && warehouseEdit !== ''){
                warehouseData = warehouseEdit;
            }
            else{
                warehouseData = warehouseOldValue;
            }
            var warehouseSplitted = warehouseData.split('#');

            // Filter variables
            var qty = parseFloat(qtyEdit);
            var price = 0;
            if(priceEdit && priceEdit !== "" && priceEdit !== "0"){
                var priceClean = priceEdit.replace(/\./g,'');
                price = parseFloat(priceClean);
            }
            var discount = 0;
            if(discountEdit && discountEdit !== "" && discountEdit !== "0"){
                var discountClean = discountEdit.replace(/\./g,'');
                discount = parseFloat(discountClean);

                // Validate discount
                if(discount > (price * qty)){
                    alert('Diskon tidak boleh melebihi harga!')
                    return false;
                }
            }

            var sbEdit = new stringbuilder();

            sbEdit.append("<tr class='item" + id + "'>");
            sbEdit.append("<td class='text-center'>" + splitted[1] + ' - ' + splitted[2] + "<input type='hidden' name='item_text[]' value='" + splitted[1] + ' - ' + splitted[2] + "'/>")
            sbEdit.append("<input type='hidden' name='item_value[]' value='" + splitted[0] + "'/></td>");
            sbEdit.append("<td class='text-center'>" + qtyEdit + "<input type='hidden' name='qty[]' value='" + qtyEdit + "'/></td>");
            sbEdit.append("<td class='text-center'>" + splitted[3] + "</td>");
            sbEdit.append("<td class='text-center'>" + warehouseSplitted[1] + "<input type='hidden' name='warehouse[]' value='" + warehouseSplitted[0] + "'/></td>");

            if(priceEdit && priceEdit !== "" && priceEdit !== "0"){
                sbEdit.append("<td class='text-right'>" + priceEdit + "<input type='hidden' name='price[]' value='" + priceEdit + "'/></td>");
            }
            else{
                sbEdit.append("<td class='text-right'>0<input type='hidden' name='price[]' value='0'/></td>");
            }

            if(discount > 0){
                sbEdit.append("<td class='text-right'>" + discountEdit + "<input type='hidden' name='discount[]' value='" + discountEdit + "'/></td>");
            }
            else{
                sbEdit.append("<td class='text-right'>0<input type='hidden' name='discount[]' value='0'/></td>");
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

            sbEdit.append("<td class='text-right'>" + subtotalString + "<input type='hidden' value='" + subtotalString + "'/></td>");

            if(remarkEdit && remarkEdit !== ""){
                sbEdit.append("<td>" + remarkEdit +"<input type='hidden' name='remark[]' class='form-control' value='" + remarkEdit + "'/></td>");
            }
            else{
                sbEdit.append("<td>-<input type='hidden' name='remark[]'/></td>");
            }

            sbEdit.append("<td class='text-center'>");
            sbEdit.append("<a class='edit-modal btn btn-info' data-id='" + id + "' data-item-id='" + data + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-warehouse-id='" + warehouseData + "' data-warehouse-text='" + warehouseSplitted[1] + "' data-remark='" + remarkEdit + "' data-price='" + price + "' data-discount='" + discount + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbEdit.append("<a class='delete-modal btn btn-danger' data-id='" + id + "' data-item-id='" + data + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-warehouse-id='" + warehouseData + "' data-warehouse-text='" + warehouseSplitted[1] + "' data-remark='" + remarkEdit + "' data-price='" + price + "' data-discount='" + discount + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbEdit.append("</td>");
            sbEdit.append("</tr>");

            $('.item' + id).replaceWith(sbEdit.toString());

            // Reset edit form modal
            $('#qty_edit').val('');
            $('#price_edit').val('');
            $('#discount_edit').val('');
            $('#remark_edit').val('');
            $('#item_edit').val(null).trigger('change');
            $('#warehouse_edit').val(null).trigger('change');
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

            // Validate table rows count
            var rows = document.getElementById('detail_table').getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;
            if(rows === 1){
                alert('Detail PO harus minimal satu!');
                return false;
            }

            // Decrease idx
            var idx = $('#index_counter').val();
            idx--;
            $('#index_counter').val(idx);

            $('.item' + deletedId).remove();
        });

        function rupiahFormat(nStr) {
            nStr += '';
            x = nStr.split(',');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            return x1 + x2;
        }
    </script>
@endsection