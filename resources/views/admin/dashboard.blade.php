@extends('admin.layouts.admin')

@section('content')
    <!-- page content -->
    <!-- top tiles -->
    <div id="testNotif" class="row tile_count">
        <h2>DASHBOARD</h2>
    </div>
    <div class="row tile_count">
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"> Total PR Aktif</span>
            {{--<div class="count green">{{ $counts['users'] }}</div>--}}
            <div class="count green">{{ $prActiveCount }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"> Total Surat Jalan Aktif</span>
            <div>
                <span class="count green">{{ $deliveryHeaderActiveCount }}</span>
                {{--<span class="count green">{{  $counts['users'] - $counts['users_unconfirmed'] }}</span>--}}
                {{--<span class="count">/</span>--}}
                {{--<span class="count red">{{ $counts['users_unconfirmed'] }}</span>--}}
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            {{--<span class="count_top"><i class="fa fa-user-times "></i> {{ __('views.admin.dashboard.count_2') }}</span>--}}
            {{--<div>--}}
                {{--<span class="count green">{{  $counts['users'] - $counts['users_inactive'] }}</span>--}}
                {{--<span class="count">/</span>--}}
                {{--<span class="count red">{{ $counts['users_inactive'] }}</span>--}}
            {{--</div>--}}
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            {{--<span class="count_top"><i class="fa fa-lock"></i> {{ __('views.admin.dashboard.count_3') }}</span>--}}
            {{--<div>--}}
                {{--<span class="count green">{{  $counts['protected_pages'] }}</span>--}}
            {{--</div>--}}
        </div>
    </div>
    <!-- /top tiles -->

    {{--<div class="row">--}}
        {{--<div class="col-md-12 col-sm-12 col-xs-12">--}}
            {{--<div id="log_activity" class="dashboard_graph">--}}

                {{--<div class="row x_title">--}}
                    {{--<div class="col-md-6">--}}
                        {{--<h3>{{ __('views.admin.dashboard.sub_title_0') }}</h3>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-6">--}}
                        {{--<div class="date_piker pull-right"--}}
                             {{--style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">--}}
                            {{--<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>--}}
                            {{--<span class="date_piker_label">--}}
                                {{--{{ \Carbon\Carbon::now()->addDays(-6)->format('F j, Y') }} - {{ \Carbon\Carbon::now()->format('F j, Y') }}--}}
                            {{--</span>--}}
                            {{--<b class="caret"></b>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="col-md-9 col-sm-9 col-xs-12">--}}
                    {{--<div class="chart demo-placeholder" style="width: 100%; height:460px;"></div>--}}
                {{--</div>--}}


                {{--<div class="col-md-3 col-sm-3 col-xs-12 bg-white">--}}
                    {{--<div class="x_title">--}}
                        {{--<h2>{{ __('views.admin.dashboard.sub_title_1') }}</h2>--}}
                        {{--<div class="clearfix"></div>--}}
                    {{--</div>--}}

                    {{--<div class="col-md-12 col-sm-12 col-xs-6">--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_0') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-emergency" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_1') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-alert" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_2') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-critical" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_3') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="asdasdasd"></div>--}}
                                    {{--<div class="progress-bar log-error" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_4') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-warning" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_5') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-notice" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_6') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-info" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div>--}}
                            {{--<p>{{ __('views.admin.dashboard.log_level_7') }}</p>--}}
                            {{--<div class="">--}}
                                {{--<div class="progress progress_sm" style="width: 76%;">--}}
                                    {{--<div class="progress-bar log-debug" role="progressbar" data-transitiongoal="0"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="clearfix"></div>--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}
    {{--<br />--}}

    @if($isAssignedRole)
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Assignment MR ({{ $assignmentMrCount }})</h3>
                        <div class="nav navbar-right">
                            <a href="{{ route('admin.assignment.mr') }}" class="btn btn-default">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="warning-notice">
                            @if($assignmentMrList->count() > 0)
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Tanggal Assignment</th>
                                        <th>No MR</th>
                                        <th>Tanggal MR</th>
                                        <th>Di-assign oleh</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($assignmentMrList as $assignment)
                                        <tr>
                                            <td>{{ $assignment->created_at_string }}</td>
                                            @if($assignment->material_request_header->type === 1)
                                                <td><a href="{{ route('admin.material_requests.other.show', ['material_request' => $assignment->material_request_header]) }}" target="_blank" style="font-weight: bold; text-decoration: underline;">{{ $assignment->material_request_header->code }}</a></td>
                                            @elseif($assignment->material_request_header->type === 2)
                                                <td><a href="{{ route('admin.material_requests.fuel.show', ['material_request' => $assignment->material_request_header]) }}" target="_blank" style="font-weight: bold; text-decoration: underline;">{{ $assignment->material_request_header->code }}</a></td>
                                            @elseif($assignment->material_request_header->type === 3)
                                                <td><a href="{{ route('admin.material_requests.oil.show', ['material_request' => $assignment->material_request_header]) }}" target="_blank" style="font-weight: bold; text-decoration: underline;">{{ $assignment->material_request_header->code }}</a></td>
                                            @else
                                                <td><a href="{{ route('admin.material_requests.service.show', ['material_request' => $assignment->material_request_header]) }}" target="_blank" style="font-weight: bold; text-decoration: underline;">{{ $assignment->material_request_header->code }}</a></td>
                                            @endif
                                            <td>{{ $assignment->material_request_header->date_string }}</td>
                                            <td>{{ $assignment->assignerUser->name }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak assignment MR</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Assignment PR ({{ $assignmentPrCount }})</h3>
                        <div class="nav navbar-right">
                            <a href="{{ route('admin.assignment.pr') }}" class="btn btn-default">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="warning-notice">
                            @if($assignmentPrList->count() > 0)
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Tanggal Assignment</th>
                                        <th>No PR</th>
                                        <th>Tanggal PR</th>
                                        <th>Di-assign oleh</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($assignmentPrList as $assignment)
                                        <tr>
                                            <td>{{ $assignment->created_at_string }}</td>
                                            <td><a href="{{ route('admin.purchase_requests.show', ['purchase_request' => $assignment->purchase_request_header]) }}" target="_blank" style="font-weight: bold; text-decoration: underline;">{{ $assignment->purchase_request_header->code }}</a></td>
                                            <td>{{ $assignment->purchase_request_header->date_string }}</td>
                                            <td>{{ $assignment->assignerUser->name }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak assignment PR</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel tile overflow_hidden">
                <div class="x_title" >
                    <h3>STATUS MATERIAL REQUEST</h3>
                    <span style="color: #000000;"><b>Data 6 bulan terakhir dan hanya untuk MR jenis "STOCK"</b></span>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="panel with-nav-tabs panel-default" id="mr-chart-tabs">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab1default" data-toggle="tab" id="tab-mr-part">Part</a></li>
                                <li><a href="#tab2default" data-toggle="tab" id="tab-mr-non-part">Non-Part</a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab1default">

                                </div>
                                <div class="tab-pane fade" id="tab2default"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php echo Lava::render('ColumnChart', 'MRPartDatas', 'tab1default'); ?>
    <?php echo Lava::render('ColumnChart', 'MRNonPartDatas', 'tab2default'); ?>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel tile overflow_hidden">
                <div class="x_title" >
                    <h3>STATUS PURCHASE REQUEST</h3>
                    <span style="color: #000000;"><b>Data 6 bulan terakhir</b></span>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" id="pr-chart-container">
                </div>
                <?php echo Lava::render('ColumnChart', 'PRDatas', 'pr-chart-container'); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel tile overflow_hidden">
                <div class="x_title" >
                    <h3>STATUS PURCHASE ORDER</h3>
                    <span style="color: #000000;"><b>Data 6 bulan terakhir</b></span>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" id="po-chart-container">
                </div>
                <?php echo Lava::render('ColumnChart', 'PODatas', 'po-chart-container'); ?>
            </div>
        </div>
    </div>

    {{--<div class="row">--}}
        {{--<div class="col-md-12 col-sm-12 col-xs-12">--}}
            {{--<div class="x_panel tile overflow_hidden">--}}
                {{--<div class="x_title" >--}}
                    {{--<h3>APPROVAL PURCHASE ORDER</h3>--}}
                    {{--<span style="color: #000000;"><b>Data 30 hari terakhir</b></span>--}}
                    {{--<div class="clearfix"></div>--}}
                {{--</div>--}}
                {{--<div class="x_content" id="po-chart-container">--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    @if($prPartWarnings->count() > 0)
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Warning PR Prioritas Part ({{ $prPartWarningCounter }})</h3>
                        <div class="nav navbar-right">
                            <a href="{{ route('admin.purchase_requests.warning') }}" class="btn btn-default">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="error-notice">
                            @if($prPartWarnings->count() > 0)
                                @foreach($prPartWarnings as $pr)
                                    @if($pr->priority_expired)
                                        <div class="oaerror danger">
                                            <span>Prioritas {{ $pr->priority }} - Nomor PR</span>
                                            <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $pr->id]) }}" target="_blank">{{ $pr->code }}</a>
                                            <span>sudah jatuh tempo melewati {{ $pr->day_left }} hari</span>
                                        </div>
                                    @else
                                        @if($pr->day_left > 0)
                                            <div class="oaerror warning">
                                                <span>Prioritas {{ $pr->priority }} - Nomor PR</span>
                                                <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $pr->id]) }}" target="_blank">{{ $pr->code }}</a>
                                                <span>akan jatuh tempo {{ $pr->day_left }} hari lagi</span>
                                            </div>
                                        @elseif($pr->day_left == 0)
                                            <div class="oaerror warning">
                                                <span>Prioritas {{ $pr->priority }} - Nomor PR</span>
                                                <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $pr->id]) }}" target="_blank">{{ $pr->code }}</a>
                                                <span>akan jatuh tempo hari ini</span>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak ada peringatan PR</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($prNonPartWarnings->count() > 0)
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Warning PR Prioritas Non-Part ({{ $prNonPartWarningCounter }})</h3>
                        <div class="nav navbar-right">
                            <a href="{{ route('admin.purchase_requests.warning') }}" class="btn btn-default">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="error-notice">
                            @if($prNonPartWarnings->count() > 0)
                                @foreach($prNonPartWarnings as $pr)
                                    @if($pr->priority_expired)
                                        <div class="oaerror danger">
                                            <span>Prioritas {{ $pr->priority }} - Nomor PR</span>
                                            <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $pr->id]) }}" target="_blank">{{ $pr->code }}</a>
                                            <span>sudah jatuh tempo melewati {{ $pr->day_left }} hari</span>
                                        </div>
                                    @else
                                        @if($pr->day_left > 0)
                                            <div class="oaerror warning">
                                                <span>Prioritas {{ $pr->priority }} - Nomor PR</span>
                                                <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $pr->id]) }}" target="_blank">{{ $pr->code }}</a>
                                                <span>akan jatuh tempo {{ $pr->day_left }} hari lagi</span>
                                            </div>
                                        @elseif($pr->day_left == 0)
                                            <div class="oaerror warning">
                                                <span>Prioritas {{ $pr->priority }} - Nomor PR</span>
                                                <a style="text-decoration: underline;" href="{{ route('admin.purchase_requests.show', ['purchase_request' => $pr->id]) }}" target="_blank">{{ $pr->code }}</a>
                                                <span>akan jatuh tempo hari ini</span>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak ada peringatan PR</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($approvalFeatured === 1 && ($isMrApprover || $isPoApprover))
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Approval PR ({{ $approvalMrCount }})</h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="error-notice">
                            @if($approvalMaterialRequests->count() > 0)
                                @foreach($approvalMaterialRequests as $mr)
                                    <div class="oaerror warning">
                                        <span>Nomor MR </span>
                                        @if($mr->type === 1)
                                            : <a style="text-decoration: underline;" href="{{ route('admin.material_requests.other.show', ['material_request' => $mr->id]) }}" target="_blank">{{ $mr->code }}</a>
                                        @elseif($mr->type === 2)
                                            : <a style="text-decoration: underline;" href="{{ route('admin.material_requests.fuel.show', ['material_request' => $mr->id]) }}" target="_blank">{{ $mr->code }}</a>
                                        @elseif($mr->type === 3)
                                            : <a style="text-decoration: underline;" href="{{ route('admin.material_requests.oil.show', ['material_request' => $mr->id]) }}" target="_blank">{{ $mr->code }}</a>
                                        @else
                                            : <a style="text-decoration: underline;" href="{{ route('admin.material_requests.service.show', ['material_request' => $mr->id]) }}" target="_blank">{{ $mr->code }}</a>
                                        @endif
                                        <span >membutuhkan approval anda</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak ada MR yang membutuhkan approval anda</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Approval PO ({{ $approvalMrCount }})</h3>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="error-notice">
                            @if($approvalPurchaseOrders->count() > 0)
                                @foreach($approvalPurchaseOrders as $po)
                                    <div class="oaerror warning">
                                        <span>Nomor PO </span>
                                        <a style="text-decoration: underline;" href="{{ route('admin.purchase_orders.show', ['purchase_request' => $po->id]) }}" target="_blank">{{ $po->code }}</a>
                                        <span >membutuhkan approval anda</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak ada PO yang membutuhkan approval anda</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($stockWarnings))
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile overflow_hidden">
                    <div class="x_title">
                        <h3>Warning Stok {{ $warningCount > 0 ? '('. $warningCount. ')' : '' }}</h3>
                        <div class="nav navbar-right">
                            <a href="{{ route('admin.items.stock_notifications') }}" class="btn btn-default">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="error-notice">
                            @if($stockWarnings->count() > 0)
                                @foreach($stockWarnings as $stockWarning)
                                    <div class="oaerror warning">
                                        <span>Inventory </span>
                                        <a style="text-decoration: underline;" href="{{ route('admin.items.show', ['item' => $stockWarning->item->id]) }}" target="_blank">{{ $stockWarning->item->code }}</a>
                                        <span> ({{ $stockWarning->item->name }}) sisa kuantitas sebanyak {{ $stockWarning->item_stock->stock }} {{ $stockWarning->item->uom }} di {{ $stockWarning->item_stock->warehouse->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="oaerror success">
                                    <strong>Tidak ada peringatan stok</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    {{--{!! $chart->script() !!}--}}
    <script>
        function getMrPartDetails (event, chart) {
            var str = "";

            var selectedItem = chart.getSelection()[0];
            console.log(selectedItem);
            //console.log(selectedItem);
            //console.log("Row: " + selectedItem.row + " Col: " + selectedItem.column);
            lava.getChart('MRNonPartDatas', function (googleChart, lavaChart) {
                var test = lavaChart.data.Nf;
                var test1 = test[selectedItem.row].c;
                //console.log(test1[0].v);
                var url = '{{ route('admin.dashboard_mr_chart') }}';
                var statusTmp = selectedItem.column;
                var status = 0;

                //Check Index
                if(statusTmp === 1 || statusTmp === 2){
                    status = 0;
                }
                else if(statusTmp === 3 || statusTmp === 4){
                    status = 1;
                }
                else if(statusTmp === 5 || statusTmp === 6){
                    status = 2;
                }
                else if(statusTmp === 7 || statusTmp === 8){
                    status = 3;
                }
                else if(statusTmp === 9 || statusTmp === 10){
                    status = 4;
                }

                str = url + "?type=1&month=" + test1[0].v + "&status=" + status;
            });

            var a = document.createElement('a');
            a.href = str;
            a.setAttribute('target', '_blank');
            a.click();
        }

        function getMrNonPartDetails (event, chart) {
            var str = "";

            var selectedItem = chart.getSelection()[0];
            console.log(selectedItem);
            //console.log(selectedItem);
            //console.log("Row: " + selectedItem.row + " Col: " + selectedItem.column);
            lava.getChart('MRPartDatas', function (googleChart, lavaChart) {
                var test = lavaChart.data.Nf;
                var test1 = test[selectedItem.row].c;
                //console.log(test1[0].v);
                var url = '{{ route('admin.dashboard_mr_chart') }}';
                var statusTmp = selectedItem.column;
                var status = 0;

                //Check Index
                if(statusTmp === 1 || statusTmp === 2){
                    status = 0;
                }
                else if(statusTmp === 3 || statusTmp === 4){
                    status = 1;
                }
                else if(statusTmp === 5 || statusTmp === 6){
                    status = 2;
                }
                else if(statusTmp === 7 || statusTmp === 8){
                    status = 3;
                }
                else if(statusTmp === 9 || statusTmp === 10){
                    status = 4;
                }

                str = url + "?type=0&month=" + test1[0].v + "&status=" + status;
            });

            var a = document.createElement('a');
            a.href = str;
            a.setAttribute('target', '_blank');
            a.click();
        }

        function getPrDetails (event, chart) {
            var str = "";

            var selectedItem = chart.getSelection()[0];

            lava.getChart('PRDatas', function (googleChart, lavaChart) {
                var test = lavaChart.data.Nf;
                var test1 = test[selectedItem.row].c;
                var url = '{{ route('admin.dashboard_pr_chart') }}';
                var statusTmp = selectedItem.column;
                var status = 0;

                //Check Index
                if(statusTmp === 1 || statusTmp === 2){
                    status = 0;
                }
                else if(statusTmp === 3 || statusTmp === 4){
                    status = 1;
                }
                else if(statusTmp === 5 || statusTmp === 6){
                    status = 2;
                }
                else if(statusTmp === 7 || statusTmp === 8){
                    status = 3;
                }
                else if(statusTmp === 9 || statusTmp === 10){
                    status = 4;
                }

                str = url + "?month=" + test1[0].v + "&status=" + status;
            });

            var a = document.createElement('a');
            a.href = str;
            a.setAttribute('target', '_blank');
            a.click();
        }

        function getPoDetails (event, chart) {
            var str = "";

            var selectedItem = chart.getSelection()[0];

            lava.getChart('PODatas', function (googleChart, lavaChart) {
                var test = lavaChart.data.Nf;
                var test1 = test[selectedItem.row].c;
                var url = '{{ route('admin.dashboard_po_chart') }}';
                var statusTmp = selectedItem.column;
                var status = 0;

                //Check Index
                if(statusTmp === 1 || statusTmp === 2){
                    status = 0;
                }
                else if(statusTmp === 3 || statusTmp === 4){
                    status = 1;
                }
                else if(statusTmp === 5 || statusTmp === 6){
                    status = 2;
                }
                else if(statusTmp === 7 || statusTmp === 8){
                    status = 3;
                }

                str = url + "?month=" + test1[0].v + "&status=" + status;
            });

            var a = document.createElement('a');
            a.href = str;
            a.setAttribute('target', '_blank');
            a.click();
        }

        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            if (e.target.id === "tab-mr-part") lava.getChart('MRPartDatas', function (googleChart, lavaChart) { lavaChart.redraw(); });
            if (e.target.id === "tab-mr-non-part") lava.getChart('MRNonPartDatas', function (googleChart, lavaChart) { lavaChart.redraw(); });
        });
    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
    <style>
        .error-notice {
            margin: 5px 5px; /* Making sure to keep some distance from all side */
        }

        .oaerror {
            width: 90%; /* Configure it fit in your design  */
            margin: 0 auto; /* Centering Stuff */
            background-color: #FFFFFF; /* Default background */
            padding: 20px;
            border: 1px solid #eee;
            border-left-width: 5px;
            border-radius: 3px;
            margin: 0 auto;
            font-family: 'Open Sans', sans-serif;
            font-size: 16px;
        }

        .danger {
            border-left-color: #d9534f; /* Left side border color */
            background-color: rgba(217, 83, 79, 0.1); /* Same color as the left border with reduced alpha to 0.1 */
        }

        .danger strong {
            color:  #d9534f;
        }

        .warning {
            border-left-color: #f0ad4e;
            background-color: rgba(240, 173, 78, 0.1);
        }

        .warning strong {
            color: #f0ad4e;
        }

        .info {
            border-left-color: #5bc0de;
            background-color: rgba(91, 192, 222, 0.1);
        }

        .info strong {
            color: #5bc0de;
        }

        .success {
            border-left-color: #2b542c;
            background-color: rgba(43, 84, 44, 0.1);
        }

        .success strong {
            color: #2b542c;
        }

        .oaerror{
            padding: 10px !important;
        }
    </style>
@endsection
