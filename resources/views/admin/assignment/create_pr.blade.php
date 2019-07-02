@extends('admin.layouts.admin')

@section('title', 'Assign PR ke Staff Purchasing')

@section('content')

    <hr/>
    <div class="row">
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%" id="assignment_table">
            <thead>
            <tr>
                <th class="text-center">Assign</th>
                <th class="text-center">Nomor PR</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Departemen</th>
                <th class="text-center">Prioritas</th>
                <th class="text-center">Dibuat Oleh</th>
                <th class="text-center">Tanggal Dibuat</th>
            </tr>
            </thead>
            <tbody>
            @foreach($prHeaders as $header)
                <tr>
                    <td id="row_{{ $header->id }}" class="text-center"><button class="btn btn-primary assign" data-id="{{ $header->id }}" data-mr-code="{{ $header->code }}" >Assign</button></td>
                    <td><a href="{{ route('admin.purchase_requests.show', ['purchase_request' => $header]) }}" target="_blank" style="font-weight: bold; text-decoration: underline;">{{ $header->code }}</a></td>
                    <td class="text-center">{{ $header->date_string }}</td>
                    <td class="text-center">{{ $header->department->name }}</td>
                    <td class="text-center">{{ $header->priority }}</td>
                    <td class="text-center">{{ $header->createdBy->name }}</td>
                    <td class="text-center">{{ $header->created_at_string }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Assign Modal -->
    <div id="modal_assign" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Assign ke Staff Purchasing</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="mr_code">Nomor MR:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="mr_code" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="assigned_user">Assign ke:</label>
                            <div class="col-sm-10">
                                <select type="text" class="form-control" id="assigned_user"></select>
                            </div>
                        </div>
                        <input type="hidden" id="mr_id" name="mr_id"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button id="btn_assign_store" type="button" class="btn btn-success" onclick="storeAssignment();" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>">Assign</button>
                    <button id="btn_assign_loading" type="button" class="btn btn-success" style="display: none;"><i class='fa fa-circle-o-notch fa-spin'></i></button>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/select2.css')) }}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    {{ Html::script(mix('assets/admin/js/select2.js')) }}
    <script>
        $(function() {
            $('#assignment_table').DataTable({
                pageLength: 25
            });
        });

        // Add new detail
        $(document).on('click', '.assign', function(e) {
            let id = $(this).data('id');
            let code = $(this).data('mr-code');

            $('#mr_code').val(code);
            $('#mr_id').val(id);

            $('#assigned_user').val(null).trigger('change');
            $('#assigned_user').select2({
                placeholder: {
                    id: '-1',
                    text: ' - Pilih Staff Purchasing - '
                },
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('select.assignment.users') }}',
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

            $('#btn_assign_store').show();
            $('#btn_assign_loading').hide();

            $('#modal_assign').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        function storeAssignment(){

            if(!$('#assigned_user').val()){
                alert('Pilih staff purchasing!');
                return false;
            }

            $('#btn_assign_store').hide();
            $('#btn_assign_loading').show();

            $.ajax({
                type: 'POST',
                url: '{{ route('admin.assignment.mr.assign.store') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'assigned_user': $('#assigned_user').val(),
                    'mr_id': $('#mr_id').val()
                },
                success: function(data) {
                    //alert(data.mr_id);
                    if ((data.errors)){
                        setTimeout(function () {
                            toastr.error('Gagal assign!', 'Peringatan', {timeOut: 6000, positionClass: "toast-top-center"});
                        }, 500);
                    }
                    else{
                        $('#modal_assign').modal('hide');
                        toastr.success('Berhasil assign!', 'Sukses', {timeOut: 5000});
                        $('#row_' + data['mr_id']).html(data['assigned_name']);
                    }
                }
            });
        }

        function filterStatus(e){
            // Get status filter value
            var status = e.value;

            var url = '{{ route('admin.purchase_requests') }}';

            // alert(status);

            window.location = url + '?status=' +status;
        }
    </script>
@endsection
