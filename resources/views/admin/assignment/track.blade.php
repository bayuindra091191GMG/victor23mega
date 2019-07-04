@extends('admin.layouts.admin')

@section('title', 'Tracking Assignment')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('partials._success')
        </div>
        <div class="col-md-12">
            <form id="form_filter_assignment" class="form-horizontal form-label-left">
                <hr/>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="user">
                        Staff Purchasing
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <select id="user" name="user" class="form-control col-md-7 col-xs-12">
                            <option value="-1" @if($selectedUserId === -1) selected @endif> - Pilih Staff Purchasing - </option>
                            <option value="0" @if($selectedUserId === 0) selected @endif>Semua</option>
                            @foreach($purchasingUsers as $user)
                                <option value="{{ $user->id }}" @if(!empty($selectedUser) && $user->id === $selectedUser->id) selected @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-3 col-sm-3 col-xs-12">--}}
{{--                        Dokumen--}}
{{--                    </label>--}}
{{--                    <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                        <div class="radio">--}}
{{--                            <label><input type="radio" name="doc_type" value="mr" @if($docType === 'mr') checked @endif>Material Request</label>--}}
{{--                        </div>--}}
{{--                        <div class="radio">--}}
{{--                            <label><input type="radio" name="doc_type" value="pr" @if($docType === 'pr') checked @endif>Purchase Request</label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date_start">
                        Tanggal Mulai
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input id="date_start" type="text" class="form-control" value="{{ $filterDateStart }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="date_end">
                        Tanggal Akhir
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input id="date_end" type="text" class="form-control" value="{{ $filterDateEnd }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3 col-sm-3 col-xs-12"></div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <a class="btn btn-warning" id="btn_reset" style="cursor: pointer;"> Reset Filter</a>
                        <a class="btn btn-success" id="btn_filter" style="cursor: pointer;"> Track Assignment</a>
                    </div>
                </div>
                <hr/>
            </form>
        </div>
    </div>

    @if(!empty($selectedUserId > -1 && $docType === 'mr'))
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                       width="100%" id="assignment_table">
                    <thead>
                    <tr>
                        {{--                    <th class="text-center">No</th>--}}
                        {{--                    <th class="text-center">Di-assign ke</th>--}}
                        {{--                    <th class="text-center">Di-assign oleh</th>--}}
                        {{--                    <th class="text-center">Tanggal Assignment</th>--}}
                        {{--                    <th class="text-center">No MR</th>--}}
                        {{--                    <th class="text-center">Tanggal buat Dokumen</th>--}}
                        {{--                    <th class="text-center">Diproses sesuai assign?</th>--}}
                        {{--                    <th class="text-center">Diproses oleh</th>--}}
                        {{--                    <th class="text-center">Tanggal diproses</th>--}}
                        {{--                    <th class="text-center">Status</th>--}}
                        <th class="text-center">No</th>
                        <th class="text-center">Di-assign ke</th>
                        <th class="text-center">Di-assign oleh</th>
                        <th class="text-center">Tanggal Assignment</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">No MR</th>
                        <th class="text-center">MR Diproses sesuai assign?</th>
                        <th class="text-center">Tanggal Proses MR</th>
                        <th class="text-center">No PR</th>
                        <th class="text-center">PR Diproses sesuai assign?</th>
                        <th class="text-center">Tanggal Proses PR</th>
                        <th class="text-center">No PO</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(!empty($selectedUserId > -1 && $docType === 'pr'))
        <div class="row">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                   width="100%" id="assignment_table">
                <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Di-assign ke</th>
                    <th class="text-center">Di-assign oleh</th>
                    <th class="text-center">Tanggal Assignment</th>
                    <th class="text-center">No PR</th>
                    <th class="text-center">Tanggal buat Dokumen</th>
                    <th class="text-center">Diproses sesuai assign?</th>
                    <th class="text-center">Diproses oleh</th>
                    <th class="text-center">Tanggal diproses</th>
                    <th class="text-center">Status</th>
                    {{--                <th class="text-center">Tindakan</th>--}}
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    @endif

    @include('partials._delete')
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-datetimepicker.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-datetimepicker.js')) }}
    <script>
        $(function() {
            // Date Picker
            $('#date_start').datetimepicker({
                format: "DD MMM Y"
            });

            $('#date_end').datetimepicker({
                format: "DD MMM Y"
            });

            @if($selectedUserId > -1 && $docType === 'mr')
                {{--$('#assignment_table').DataTable({--}}
                {{--    processing: true,--}}
                {{--    serverSide: true,--}}
                {{--    pageLength: 25,--}}
                {{--    ajax: {--}}
                {{--        url: '{!! route('datatables.assignment.track') !!}',--}}
                {{--        data: {--}}
                {{--            'user_id': '{{ $selectedUserId }}',--}}
                {{--            'doc_type': '{{ $docType }}',--}}
                {{--            'date_start': '{{ $filterDateStart }}',--}}
                {{--            'date_end': '{{ $filterDateEnd }}'--}}
                {{--        }--}}
                {{--    },--}}
                {{--    columns: [--}}
                {{--        { data: 'DT_Row_Index', class: 'text-center', orderable: false, searchable: false },--}}
                {{--        { data: 'assigned_user', name: 'assigned_user', class: 'text-center' },--}}
                {{--        { data: 'assigner_user', name: 'assigner_user', class: 'text-center' },--}}
                {{--        { data: 'created_at', name: 'created_at', class: 'text-center',--}}
                {{--            render: function ( data, type, row ){--}}
                {{--                if ( type === 'display' || type === 'filter' ){--}}
                {{--                    return moment(data).format('DD MMM YYYY');--}}
                {{--                }--}}
                {{--                return data;--}}
                {{--            }--}}
                {{--        },--}}
                {{--        { data: 'mr_code', name: 'mr_code', class: 'text-center' },--}}
                {{--        { data: 'doc_created_at', name: 'doc_created_at', class: 'text-center',--}}
                {{--            render: function ( data, type, row ){--}}
                {{--                if ( type === 'display' || type === 'filter' ){--}}
                {{--                    return moment(data).format('DD MMM YYYY');--}}
                {{--                }--}}
                {{--                return data;--}}
                {{--            }--}}
                {{--        },--}}
                {{--        { data: 'different_processor', name: 'different_processor', class: 'text-center' },--}}
                {{--        { data: 'processed_by', name: 'processed_by', class: 'text-center' },--}}
                {{--        { data: 'processed_date', name: 'processed_date', class: 'text-center',--}}
                {{--            render: function ( data, type, row ){--}}
                {{--                if ( (type === 'display' || type === 'filter') && data !== '-' ){--}}
                {{--                    return moment(data).format('DD MMM YYYY');--}}
                {{--                }--}}
                {{--                return data;--}}
                {{--            }--}}
                {{--        },--}}
                {{--        { data: 'status', name: 'status', class: 'text-center' }--}}
                {{--        // { data: 'action', name:'action', orderable: false, searchable: false, class: 'text-center' }--}}
                {{--    ],--}}
                {{--    language: {--}}
                {{--        url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"--}}
                {{--    }--}}
                {{--});--}}

            $('#assignment_table').DataTable({
                responsive: false,
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: '{!! route('datatables.assignment.track') !!}',
                    data: {
                        'user_id': '{{ $selectedUserId }}',
                        'doc_type': '{{ $docType }}',
                        'date_start': '{{ $filterDateStart }}',
                        'date_end': '{{ $filterDateEnd }}'
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', class: 'text-center', orderable: false, searchable: false },
                    { data: 'assigned_user', name: 'assigned_user', class: 'text-center' },
                    { data: 'assigner_user', name: 'assigner_user', class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'track_status', name: 'track_status', class: 'text-center' },
                    { data: 'mr_code', name: 'mr_code', class: 'text-center' },
                    { data: 'different_mr_processor', name: 'different_mr_processor', class: 'text-center' },
                    { data: 'processed_mr_date', name: 'processed_mr_date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( (type === 'display' || type === 'filter') && data !== '-'){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'pr_code', name: 'mr_code', class: 'text-center' },
                    { data: 'different_pr_processor', name: 'different_pr_processor', class: 'text-center' },
                    { data: 'processed_pr_date', name: 'processed_pr_date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( (type === 'display' || type === 'filter') && data !== '-' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'po_code', name: 'po_code', class: 'text-center' },
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        @endif

        @if($selectedUserId > -1 && $docType === 'pr')
            $('#assignment_table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: '{!! route('datatables.assignment.track') !!}',
                    data: {
                        'user_id': '{{ $selectedUserId }}',
                        'doc_type': '{{ $docType }}',
                        'date_start': '{{ $filterDateStart }}',
                        'date_end': '{{ $filterDateEnd }}'
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', class: 'text-center', orderable: false, searchable: false },
                    { data: 'assigned_user', name: 'assigned_user', class: 'text-center' },
                    { data: 'assigner_user', name: 'assigner_user', class: 'text-center' },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'pr_code', name: 'mr_code', class: 'text-center' },
                    { data: 'doc_created_at', name: 'doc_created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'different_processor', name: 'different_processor', class: 'text-center' },
                    { data: 'processed_by', name: 'processed_by', class: 'text-center' },
                    { data: 'processed_date', name: 'processed_date', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( (type === 'display' || type === 'filter') && data !== '-' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'status', name: 'status', class: 'text-center' }
                    // { data: 'action', name:'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        @endif

        });

        $(document).on("click", "#btn_filter", function(){
            let dateStart = $('#date_start').val();
            let dateEnd = $('#date_end').val();

            let docType = $('input[name="doc_type"]:checked', '#form_filter_assignment').val();
            let userId = $('#user').val();

            let url = '{{ route('admin.assignment.track') }}';
            window.location = url + '?user_id=' + userId + '&doc_type=' + docType + '&date_start=' + dateStart + "&date_end=" + dateEnd;
        });

        $(document).on("click", "#btn_reset", function(){
            let url = '{{ route('admin.assignment.track') }}';
            window.location = url;
        });
    </script>
@endsection
