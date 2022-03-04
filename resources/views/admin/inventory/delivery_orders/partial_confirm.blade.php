@extends('admin.layouts.admin')

@section('title','Konfirmasi Parsial Surat Jalan '. $deliveryOrder->code)

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="navbar-left">
                <a class="btn btn-default" href="{{ route('admin.delivery_orders.show', ['delivery_order' => $deliveryOrder->id ]) }}"><i class="fa fa-arrow-circle-o-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="navbar-right">
{{--                <a class="confirm-modal btn btn-success" data-id="{{ $header->id }}">KONFIRMASI</a>--}}
{{--                <a class="cancel-modal btn btn-danger" data-id="{{ $header->id }}">BATAL</a>--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form class="form-horizontal form-label-left box-section">

                <div class="form-group">
                    <label class="col-md-3 col-sm-3 col-xs-12">
                        Nomor Surat Jalan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        : {{ $deliveryOrder->code }}
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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code" >
                        Nomor Konfirmasi Surat Jalan
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="code" type="text" class="form-control col-md-7 col-xs-12"
                               name="code" value="{{ $documentCode }}" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="remark" >
                        Remark
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <textarea id="remark" name="remark" rows="5" class="form-control col-md-7 col-xs-12" style="resize: vertical" placeholder="Isi keterangan tambahan di sini">{{ old('remark') }}</textarea>
                    </div>
                </div>

                <hr/>

                <div id="vue_app" class="form-group">
                    <div class="col-lg-12 col-md-12 col-xs-12 column">
                        <h4 class="text-center">Konfirmasi Parsial</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 20%">
                                        Kode Barang
                                    </th>
                                    <th class="text-center" style="width: 50%">
                                        Nama Barang
                                    </th>
                                    <th class="text-center" style="width: 20%">
                                        QTY Penerimaan
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-for="(item, index) in delivery_order_array">
                                    <td>
                                        <input type="hidden" :name="'item_detail[' + index  +  '][item_id]'" v-model="item.item_id">
                                        <span>@{{ item.code }}</span>
                                    </td>
                                    <td>
                                        <span>@{{ item.name }}</span>
                                    </td>
                                    <td>
                                        <vue-autonumeric class="form-control text-right" :name="'item_detail[' + index  +  '][qty]'"
                                                         :id="item.qty_input_id"
                                                         :query_selector="item.qty_input_id"
                                                         :value="item.qty_value"
                                                         v-model="item.qty_value"></vue-autonumeric>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger" @click="removeRow(item)"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <button type="button" class="btn btn-success" onclick="formSubmit();">Simpan</button>
                    </div>
                </div>
            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script src="https://unpkg.com/vue@3"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.cancel-modal', function(){
            $('#cancel_modal').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#canceled-id').val($(this).data('id'));
        });

        $('.modal-footer').on('click', '.cancel', function() {
            $('.cancel').prop("disabled", true);
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.delivery_orders.cancel') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': $('#canceled-id').val(),
                },
                success: function(data) {
                    if ((data.errors)){
                        $('.cancel').prop("disabled", false);
                        setTimeout(function () {
                            toastr.error('Gagal membatalkan', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        window.location = '{{ route('admin.delivery_orders') }}';
                    }
                }
            });
        });


    </script>
    <script>
        const app = Vue.createApp({
            data(){
                return {...@json($vueData),...{
                    }
                }},
            methods: {
                // Remove selected row
                removeFirstVariantRow(item){
                    if(this.delivery_order_array.length > 0){
                        this.delivery_order_array = this.delivery_order_array.filter(function (x) { return x !== item; });
                    }
                },
            }
        })

        app.component('vue-autonumeric', {
            props: ['query_selector', 'value'],
            template: '<input type="text">',
            mounted: function (){
                let vm = this;
                new AutoNumeric('#' + this.query_selector, this.value, {
                    minimumValue: '0',
                    maximumValue: '9999999',
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    decimalPlaces: 0,
                    modifyValueOnWheel: false,
                    emptyInputBehavior: 'zero'
                });
            }
        })

        app.mount('#vue_app');
    </script>
@endsection