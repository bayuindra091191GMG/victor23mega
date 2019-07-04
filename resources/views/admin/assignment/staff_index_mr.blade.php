@extends('admin.layouts.admin')

@section('title', 'Daftar Tugas Material Request')

@section('content')

    <div class="row">
        @include('partials._success')
        {{--<div class="nav navbar-right">--}}
            {{--<a href="{{ route('admin.departments.create') }}" class="btn btn-app">--}}
                {{--<i class="fa fa-plus"></i> Tambah--}}
            {{--</a>--}}
        {{--</div>--}}
        <div class="clearfix"></div>
    </div>

    <div class="ln_solid"></div>
    <div class="panel-heading">
        <ul class="nav nav-pills nav-justified thumbnail custom-color" style="height:auto!important;">
            <li class="active"><a>
                    <h4 class="list-group-item-heading"><b>Tugas Material Request</b></h4>
                </a>
            </li>
            <li><a href="{{route('admin.assignment.pr')}}">
                    <h4 class="list-group-item-heading"><b>Tugas Purchase Request</b></h4>
                </a>
            </li>
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            <div class="tab-pane active">
                <div class="row">
                    <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                           width="100%" id="assigments-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal assignment</th>
                            <th>Nomor Dokumen</th>
                            <th>Tanggal Dibuat Dokumen</th>
                            <th>Assign Oleh</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @include('partials._delete')
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/datatables.css')) }}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/datatables.js')) }}
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(function() {
            $('#assigments-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('datatables.staff_assigment_mr') !!}'
                },
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable: false, class: 'text-center'},
                    { data: 'created_at', name: 'created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'mr_code', name: 'mr_code', class: 'text-center'},
                    { data: 'doc_created_at', name: 'doc_created_at', class: 'text-center',
                        render: function ( data, type, row ){
                            if ( type === 'display' || type === 'filter' ){
                                return moment(data).format('DD MMM YYYY');
                            }
                            return data;
                        }
                    },
                    { data: 'assigner_user', name: 'assigner_user' },
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
                }
            });
        });
    </script>
@endsection