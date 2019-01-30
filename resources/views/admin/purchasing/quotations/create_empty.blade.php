@extends('admin.layouts.admin')

@section('title','Buat RFQ Vendor Kosong')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.quotations.print_empty'],'method' => 'post','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="quot_code">--}}
                    {{--Nomor RFQ--}}
                    {{--<span class="required">*</span>--}}
                {{--</label>--}}
                {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
                    {{--<input id="quot_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('quot_code')) parsley-error @endif"--}}
                           {{--name="quot_code" value="{{ $autoNumber}}" readonly>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_number"></label>--}}
                {{--<div class="col-md-4 col-sm-4 col-xs-12">--}}
                    {{--<div class="checkbox">--}}
                        {{--<label>--}}
                            {{--<input type="checkbox" class="flat" id="auto_number" name="auto_number" checked="checked"> Auto Number--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pr_code" >
                    Nomor PR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pr_code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('pph')) parsley-error @endif"
                           name="pr_code" value="{{ $purchaseRequest->code }}" readonly>
                    <input type="hidden" id="pr_id" name="pr_id" value="{{ $purchaseRequest->id }}">
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

            <hr/>

            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-xs-12 box-section">
                    <h3 class="text-center">Detil Inventory</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail_table">
                            <thead>
                            <tr >
                                <th class="text-center" style="width: 10%">
                                    No
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Part Number
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Part Name
                                </th>
                                <th class="text-center" colspan="2" style="width: 20%">
                                    QTY
                                </th>
                                <th class="text-center" style="width: 25%">
                                    Remarks
                                </th>
                                <th class="text-center" style="width: 15%">
                                    Tindakan
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $idx = 0; ?>
                            @if(!empty($purchaseRequest))
                                @foreach($purchaseRequest->purchase_request_details as $detail)
                                    <?php $idx++; ?>
                                    <tr class='item{{ $idx }}'>
                                        <td class='text-center'>
                                            {{ $idx }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->item->code }}
                                            <input type='hidden' name='item[]' value='{{ $detail->item_id }}'/>
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->item->name }}
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->quantity }}
                                            <input type='hidden' name='qty[]' value='{{ $detail->quantity }}'/>
                                        </td>
                                        <td class='text-center'>
                                            {{ $detail->item->uom }}
                                        </td>
                                        <td>
                                            {{ $detail->remark }}
                                            <input type='hidden' name='remark[]' value='{{ $detail->remark }}'/>
                                        </td>
                                        <td class='text-center'>
                                            <?php $itemId = $detail->item_id. "#". $detail->item->code. "#". $detail->item->name. "#". $detail->item->uom ?>
                                            <a class="edit-modal btn btn-info" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}" data-remark="{{ $detail->remark }}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <a class="delete-modal btn btn-danger" data-id="{{ $idx }}" data-item-id="{{ $itemId }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}">
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

            <hr/>

            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a class="btn btn-danger" href="{{ route('admin.quotations') }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Print</button>
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
        {{--$('#auto_number').change(function(){--}}
            {{--if(this.checked){--}}
                {{--$('#quot_code').val('{{ $autoNumber }}');--}}
                {{--$('#quot_code').prop('readonly', true);--}}
            {{--}--}}
            {{--else{--}}
                {{--$('#quot_code').val('');--}}
                {{--$('#quot_code').prop('readonly', false);--}}
            {{--}--}}
        {{--});--}}

        $('#supplier').select2({
            placeholder: {
                id: '-1',
                text: ' - Pilih Vendor -'
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

        // Add autonumeric
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

            if(!itemAdd || itemAdd === ""){
                alert('Mohon pilih barang...');
                return false;
            }

            if(!qtyAdd || qtyAdd === "" || qtyAdd === "0"){
                alert('Mohon isi kuantitas...');
                return false;
            }

            var remarkAdd = $('#remark_add').val();

            // Split item value
            var splitted = itemAdd.split('#');

            // Increase idx
            var idx = $('#index_counter').val();
            idx++;
            $('#index_counter').val(idx);

            var sbAdd = new stringbuilder();

            sbAdd.append("<tr class='item" + idx + "'>");
            sbAdd.append("<td class='text-center'>" + idx + "</td>");

            sbAdd.append("<td class='text-center'>" + splitted[1]);
            sbAdd.append("<input type='hidden' name='item[]' value='" + splitted[0] + "'/></td>");

            sbAdd.append("<td class='text-center'>" + splitted[2] + "</td>");

            sbAdd.append("<td class='text-center'>" + qtyAdd);
            sbAdd.append("<input type='hidden' name='qty[]' value='" + qtyAdd + "'/></td>");

            sbAdd.append("<td class='text-center'>" + splitted[3] + "</td>");

            sbAdd.append("<td>" + remarkAdd);
            sbAdd.append("<input type='hidden' name='remark[]' value='" + remarkAdd + "'/></td>");

            sbAdd.append("<td class='text-center'>");
            sbAdd.append("<a class='edit-modal btn btn-info' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbAdd.append("<a class='delete-modal btn btn-danger' data-id='" + idx + "' data-item-id='" + itemAdd + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyAdd + "' data-remark='" + remarkAdd + "'><span class='glyphicon glyphicon-trash'></span></a>");
            sbAdd.append("</td>");
            sbAdd.append("</tr>");

            $('#detail_table').append(sbAdd.toString());

            // Reset add form modal
            $('#qty_add').val('');
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

            qtyEditFormat.clear();
            qtyEditFormat.set($(this).data('qty'),{
                minimumValue: '0',
                digitGroupSeparator: '',
                decimalPlaces: 0
            });

            $('#remark_edit').val($(this).data('remark'));
            $('#editModal').modal('show');
        });
        $('.modal-footer').on('click', '.edit', function() {
            var itemEdit = $('#item_edit').val();
            var qtyEdit = $('#qty_edit').val();
            var remarkEdit = $('#remark_edit').val();

            // Validate qty
            if(!qtyEdit || qtyEdit === "" || qtyEdit === "0"){
                alert('Mohon isi kuantitas!')
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

            var sbEdit = new stringbuilder();

            sbEdit.append("<tr class='item" + id + "'>");
            sbEdit.append("<td class='text-center'>" + id + "</td>");

            sbEdit.append("<td class='text-center'>" + splitted[1]);
            sbEdit.append("<input type='hidden' name='item[]' value='" + splitted[0] + "'/></td>");

            sbEdit.append("<td class='text-center'>" + splitted[2] + "</td>");

            sbEdit.append("<td class='text-center'>" + qtyEdit);
            sbEdit.append("<input type='hidden' name='qty[]' value='" + qtyEdit + "'/></td>");

            sbEdit.append("<td class='text-center'>" + splitted[3] + "</td>");

            sbEdit.append("<td>" + remarkEdit);
            sbEdit.append("<input type='hidden' name='remark[]' value='" + remarkEdit + "'/></td>");

            sbEdit.append("<td class='text-center'>");
            sbEdit.append("<a class='edit-modal btn btn-info' data-id='" + id + "' data-item-id='" + itemEdit + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "'><span class='glyphicon glyphicon-edit'></span></a>");
            sbEdit.append("<a class='delete-modal btn btn-danger' data-id='" + id + "' data-item-id='" + itemEdit + "' data-item-text='" + splitted[1] + " " + splitted[2] + "' data-qty='" + qtyEdit + "' data-remark='" + remarkEdit + "'><span class='glyphicon glyphicon-trash'></span></a>");
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

            // Validate table rows count
            var rows = document.getElementById('detail_table').getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;
            if(rows === 1){
                alert('Detail PR harus minimal satu!');
                return false;
            }

            // Decrease idx counter
            var idx = $('#index_counter').val();
            idx--;
            $('#index_counter').val(idx);

            $('.item' + deletedId).remove();
        });
    </script>
@endsection