@extends('admin.layouts.admin')

@section('title', 'Histori Assignment Dokumen MR')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('partials._success')
        </div>
        <div class="col-md-12">
            <hr/>
            <form class="form-inline" style="margin-bottom: 10px;">
                <div class="form-group">
                    <label for="date_start">
                        Tanggal Mulai
                    </label>
                    <input id="date_start" type="text" class="form-control" value="{{ $filterDateStart }}">
                </div>
                <div class="form-group">
                    <label for="date_end">
                        Tanggal Akhir
                    </label>
                    <input id="date_end" type="text" class="form-control" value="{{ $filterDateEnd }}">
                </div>
                <div class="form-group">
                    <a id="btn_filter" class="btn btn-primary" style="margin: 0 !important;">FILTER</a>
                </div>
                <div class="form-group">
                    <a id="btn_reset" class="btn btn-primary" style="margin: 0 !important;">RESET</a>
                </div>
            </form>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                   width="100%" id="assignment_table">
                <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Tanggal Assignment</th>
                    <th class="text-center">Status Assignment</th>
                    <th class="text-center">No MR</th>
                    <th class="text-center">Tanggal buat Dokumen</th>
                    <th class="text-center">Di-assign ke</th>
                    <th class="text-center">Di-assign oleh</th>
                    <th class="text-center">Diproses sesuai assign?</th>
                    <th class="text-center">Diproses oleh</th>
                    <th class="text-center">Tanggal diproses</th>
                    {{--                <th class="text-center">Tindakan</th>--}}
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

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

            $('#assignment_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.history_assigment_mr') !!}',
                    data: {
                        'date_start': '{{ $filterDateStart }}',
                        'date_end': '{{ $filterDateEnd }}'
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', class: 'text-center', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'mr_code', name: 'mr_code', class: 'text-center' },
                    { data: 'doc_created_at', name: 'doc_created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'assigned_user', name: 'assigned_user', class: 'text-center' },
                    { data: 'assigner_user', name: 'assigner_user', class: 'text-center' },
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
                    // { data: 'action', name:'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });

        $(document).on("click", "#btn_filter", function(){
            let dateStart = $('#date_start').val();
            let dateEnd = $('#date_end').val();

            let url = '{{ route('admin.assignment.mr.history') }}';
            window.location = url + '?date_start=' + dateStart + "&date_end=" + dateEnd;
        });

        $(document).on("click", "#btn_reset", function(){
            let url = '{{ route('admin.assignment.mr.history') }}';
            window.location = url;
        });
    </script>
@endsection
