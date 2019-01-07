@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')


    <h2>@lang('app.filterResults')</h2>

    <div class="white-box">
        <div class="row m-b-10">
            {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="col-md-5">
                <div class="example">
                    <h5 class="box-title m-t-30">@lang('app.selectDateRange')</h5>

                    <div class="input-daterange input-group" id="date-range">
                        <input type="text" class="form-control" id="start-date" placeholder="Show Results From"
                               value="{{ \Carbon\Carbon::today()->subDays(15)->format('Y-m-d') }}"/>
                        <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                        <input type="text" class="form-control" id="end-date" placeholder="Show Results To"
                               value="{{ \Carbon\Carbon::today()->addDays(15)->format('Y-m-d') }}"/>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <h5 class="box-title m-t-30">@lang('app.selectProject')</h5>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <select class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id">
                                <option value=""></option>
                                @foreach($projects as $project)
                                    <option
                                            value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <h5 class="box-title m-t-30">&nbsp;</h5>

                <div class="checkbox checkbox-info">
                    <input type="checkbox" id="hide-completed-tasks">
                    <label for="hide-completed-tasks">@lang('app.hideCompletedTasks')</label>
                </div>
            </div>

            <div class="col-md-12">
                <button type="button" class="btn btn-success" id="filter-results"><i class="fa fa-check"></i> @lang('app.apply')
                </button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                <h2>@lang('app.menu.tasks')</h2>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            @if($user->can('add_tasks'))
                                <a href="{{ route('member.all-tasks.create') }}" class="btn btn-outline btn-success btn-sm">@lang('modules.tasks.newTask') <i class="fa fa-plus" aria-hidden="true"></i></a>
                            @endif
                            <a href="{{ route('member.taskboard.index') }}" class="btn btn-inverse btn-sm"><i class="ti-layout-column3" aria-hidden="true"></i> @lang('modules.tasks.taskBoard')</a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                           id="tasks-table">
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@lang('app.project')</th>
                            <th>@lang('app.title')</th>
                            <th>@lang('modules.tasks.assignTo')</th>
                            <th>@lang('app.dueDate')</th>
                            <th>@lang('app.status')</th>
                            <th>@lang('app.action')</th>
                        </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>

    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="editTimeLogModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script>

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd',
        language: '{{ $global->locale }}',
        autoclose: true
    });

    var table;

    function showTable() {

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var projectID = $('#project_id').val();
        if (!projectID) {
            projectID = 0;
        }

        if ($('#hide-completed-tasks').is(':checked')) {
            var hideCompleted = '1';
        } else {
            var hideCompleted = '0';
        }

        var url = '{!!  route('member.all-tasks.data', [':startDate', ':endDate', ':hideCompleted', ':projectId']) !!}';

        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':hideCompleted', hideCompleted);
        url = url.replace(':projectId', projectID);

        table = $('#tasks-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: url,
            deferRender: true,
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            "order": [[0, "desc"]],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'project_name', name: 'projects.project_name', width: '20%'},
                {data: 'heading', name: 'heading', width: '20%'},
                {data: 'name', name: 'users.name', width: '25%'},
                {data: 'due_date', name: 'due_date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', "searchable": false}
            ]
        });
    }

    $('#filter-results').click(function () {
        showTable();
    });


    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('task-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted task!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('member.all-tasks.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

    $('#tasks-table').on('click', '.show-task-detail', function () {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");

        var id = $(this).data('task-id');
        var url = "{{ route('member.all-tasks.show',':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                }
            }
        });
    })

    showTable();


</script>
@endpush