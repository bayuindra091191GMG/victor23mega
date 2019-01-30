@extends('admin.layouts.admin')

@section('title','Ubah Purchase Request '. $header->code)

@section('content')
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12">

            {{ Form::open(['route'=>['admin.purchase_requests.update', $header->id],'method' => 'put','id' => 'general-form','class'=>'form-horizontal form-label-left']) }}

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pr_code">
                    Nomor PR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="pr_code" type="text" class="form-control col-md-7 col-xs-12"
                           name="pr_code" value="{{ $header->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-12" for="date">
                    Tanggal
                    <span class="required">*</span>
                </label>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <input id="date" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('date')) parsley-error @endif"
                           name="date" value="{{ $date }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mr_code">
                    Nomor MR
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="mr_code" type="text" class="form-control col-md-7 col-xs-12"
                           name="mr_code" value="{{ $header->material_request_header->code }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department" >
                    Departemen
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {{--<select id="department" name="department" class="form-control col-md-7 col-xs-12 @if($errors->has('priority')) parsley-error @endif">--}}
                        {{--@foreach($departments as $department)--}}
                            {{--<option value="{{ $department->id }}" {{ $header->department_id == $department->id ? "selected":"" }}>{{ $department->name }}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                    <input type="text" name="department" class="form-control col-md-7 col-xs-12" value="{{ $header->material_request_header->department->name }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="machinery" >
                    Unit Alat Berat
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="machinery" type="text" class="form-control col-md-7 col-xs-12"
                           name="machinery" value="{{ $header->machinery->code ?? '' }}" readonly>
                    <input type="hidden" id="machinery_id" name="machinery_id" value="{{ $header->machinery_id ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="priority">
                    Prioritas
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="priority" type="text" class="form-control col-md-7 col-xs-12"
                           name="priority" value="{{ $header->priority }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="km">
                    KM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="km" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('km')) parsley-error @endif"
                           name="km" value="{{ $header->km }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="hm">
                    HM
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="hm" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('hm')) parsley-error @endif"
                           name="hm" value="{{ $header->hm  }}" readonly>
                </div>
            </div>

            @if($header->material_request_header->type === 4)
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note" >
                        Keterangan Servis
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <textarea id="note" name="note" rows="10" style="resize: vertical;" class="form-control col-md-7 col-xs-12" readonly>{{ $header->material_request_header->material_request_details->first()->remark }}</textarea>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-warning" href="{{ route('admin.purchase_requests.show',['purchase_request' => $header->id]) }}"> Batal</a>
                    <button type="submit" class="btn btn-success"> Simpan</button>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>

    <hr/>
    @if($header->material_request_header->type !== 4)
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 box-section">
                <h3 class="text-center">Detil Inventory</h3>
                {{--<button class="add-modal btn btn-info" data-header-id="{{ $header->id }}">--}}
                {{--<span class="glyphicon glyphicon-plus-sign"></span> Tambah--}}
                {{--</button>--}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="detail_table">
                        <thead>
                        <tr >
                            <th class="text-center">
                                Nomor Part
                            </th>
                            <th class="text-center">
                                UOM
                            </th>
                            <th class="text-center">
                                QTY
                            </th>
                            <th class="text-center" style="width: 30%;">
                                Remark
                            </th>
                            <th class="text-center" style="width: 20%;">
                                Tindakan
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($header->purchase_request_details as $detail)
                            <tr class="item{{ $detail->id }}">
                                <td class='text-center'>
                                    {{ $detail->item->code }} - {{ $detail->item->name }}
                                </td>
                                <td class='text-center'>
                                    {{ $detail->item->uom }}
                                </td>
                                <td class='text-center'>
                                    {{ $detail->quantity }}
                                </td>
                                <td>
                                    {{ $detail->remark ?? '-' }}
                                </td>
                                <td class='text-center'>
                                    <button class="edit-modal btn btn-info" data-id="{{ $detail->id }}" data-item-id="{{ $detail->item_id }}" data-item-text="{{ $detail->item->code. ' - '. $detail->item->name }}" data-qty="{{ $detail->quantity }}" data-remark="{{ $detail->remark }}">
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
                                <label class="control-label col-sm-2" for="remark_add">Remark:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="remark_add" name="remark_add" cols="40" rows="5"></textarea>
                                    <p class="errorRemark text-center alert alert-danger hidden"></p>
                                </div>
                            </div>
                        </form>
                        <div class="modal-footer">\
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
                                    <select class="form-control" id="item_edit" name="item"></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="qty_edit">QTY:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="qty_edit" name="qty">
                                    <p class="errorQty text-center alert alert-danger hidden"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="remark_edit">Remark:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="remark_edit" name="remark" cols="40" rows="5"></textarea>
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
    @endif

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
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
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    {{ Html::script(mix('assets/admin/js/autonumeric.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">
        // Date Picker
        $('#date').datetimepicker({
            format: "DD MMM Y"
        });

        @if($header->material_request_header->type !== 4)
            // AutoNumeric
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

            var i=1;

            $('#select0').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Inventory - '
                },
                width: '100%',
                minimumInputLength: 2,
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

            $('#date0').datetimepicker({
                format: "DD MMM Y"
            });

            $('#date_edit').datetimepicker({
                format: "DD MMM Y"
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
                    url: '{{ route('admin.purchase_request_details.store') }}',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'header_id': '{{ $header->id }}',
                        'item': $('#item_add').val(),
                        'qty': $('#qty_add').val(),
                        'remark': $('#remark_add').val()
                    },
                    success: function(data) {
                        $('.errorItem').addClass('hidden');
                        $('.errorQty').addClass('hidden');
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
                            if (data.errors.remark_add) {
                                $('.errorRemark').removeClass('hidden');
                                $('.errorRemark').text(data.errors.remark);
                            }
                        } else {
                            toastr.success('Berhasil simpan detail!', 'Sukses', {timeOut: 5000});
                            var remarkAdd = '-';
                            if (data.remark !== null) {
                                remarkAdd = data.remark;
                            }
                            $('#detail_table').append("<tr class='item" + data.id + "'><td class='field-item'>" + data.item.code + " - " + data.item.name + "</td><td>" + data.item.uom + "</td><td>" + data.quantity + "</td><td>" + remarkAdd + "</td><td>" + "<button class='edit-modal btn btn-info' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " " + data.item.name + "' data-qty='" + data.quantity + "' data-remark='" + data.remark + "'><span class='glyphicon glyphicon-edit'></span></button><button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " - "  + data.item.name + "' data-qty='" + data.quantity + "'><span class='glyphicon glyphicon-trash'></span></button></td></tr>");

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

                qtyEditFormat.clear();
                qtyEditFormat.set($(this).data('qty'),{
                    minimumValue: '0',
                    digitGroupSeparator: '',
                    decimalPlaces: 0
                });

                $('#remark_edit').val($(this).data('remark'));
                $('#date_edit').val($(this).data('date'));
                $('#editModal').modal('show');
            });
            $('.modal-footer').on('click', '.edit', function() {
                var qtyEdit = $('#qty_edit').val();
                if(!qtyEdit || qtyEdit === "" || qtyEdit === "0"){
                    alert('Mohon isi kuantitas!');
                    return false;
                }

                $.ajax({
                    type: 'PUT',
                    url: '{{ route('admin.purchase_request_details.update') }}',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'id' : id,
                        'item': $("#item_edit").val(),
                        'qty': qtyEdit,
                        'remark': $('#remark_edit').val()
                    },
                    success: function(data) {
                        $('.errorQty').addClass('hidden');
                        $('.errorRemark').addClass('hidden');

                        if ((data.errors)) {
                            setTimeout(function () {
                                $('#editModal').modal('show');
                                toastr.error('Gagal ubah data!', 'Peringatan', {timeOut: 5000});
                            }, 500);

                            if (data.errors.qty) {
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
                            $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'><td class='field-item'>" + data.item.code + " - " + data.item.name + "</td><td>" + data.item.uom + "</td><td>" + data.quantity + "</td><td>" + remarkEdit + "</td><td>" + "<button class='edit-modal btn btn-info' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " " + data.item.name + "' data-qty='" + data.quantity + "' data-remark=" + data.remark + "><span class='glyphicon glyphicon-edit'></span></button><button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-item-id='" + data.item_id + "' data-item-text='" + data.item.code + " - "  + data.item.name + "' data-qty='" + data.quantity + "'><span class='glyphicon glyphicon-trash'></span></button></td></tr>");

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
                // Validate table rows count
                var rows = document.getElementById('detail_table').getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;
                if(rows === 1){
                    alert('Detail PR harus minimal satu!');
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.purchase_request_details.delete') }}',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'id': deletedId
                    },
                    success: function(data) {
                        if ((data.errors)){
                            setTimeout(function () {
                                toastr.error('Gagal menghapus detil!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                            }, 500);
                        }
                        else{
                            toastr.success('Berhasil menghapus detail!', 'Sukses', {timeOut: 5000});
                            $('.item' + data['id']).remove();
                        }
                    }
                });
            });
        @endif
    </script>
@endsection