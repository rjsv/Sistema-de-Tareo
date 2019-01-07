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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/lobipanel/dist/css/lobipanel.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="white-box">
            <a href="{{ route('member.all-tasks.index') }}" class="btn btn-info btn-outline"><i class="fa fa-arrow-left"></i> @lang('modules.tasks.tasksTable')</a>

            @if($user->can('add_tasks'))
                <a href="javascript:;" id="add-column" class="btn btn-success btn-outline"><i class="fa fa-plus"></i> @lang('modules.tasks.addBoardColumn')</a>
            @endif

            {!! Form::open(['id'=>'addColumn','class'=>'ajax-form','method'=>'POST']) !!}


            <div class="row" id="add-column-form" style="display: none;">
                <div class="col-md-12">
                    <hr>
                    <div class="form-group">
                        <label class="control-label">@lang("modules.tasks.columnName")</label>
                        <input type="text" name="column_name" class="form-control">
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-4">
                    <div class="form-group">
                        <label>@lang("modules.tasks.labelColor")</label><br>
                        <input type="text" class="colorpicker form-control"  name="label_color" value="#ff0000" />
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <button class="btn btn-success" id="save-form" type="submit"><i class="fa fa-check"></i> @lang('app.save')</button>
                    </div>
                </div>
                <!--/span-->

            </div>
            {!! Form::close() !!}


            {!! Form::open(['id'=>'updateColumn','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="row" id="edit-column-form" style="display: none;">



            </div>
            <!--/row-->
            {!! Form::close() !!}
        </div>

    </div>

    <div class="container-scroll">

    <div class="row container-row">
        @foreach($boardColumns as $key=>$column)
            <div class="panel col-md-3 board-column p-0" data-column-id="{{ $column->id }}" >
                    <div class="panel-heading p-t-5 p-b-5" style="background-color: {{ $column->label_color }}" >
                        <div class="panel-title">
                            <h4 class="text-white">{{ ucwords($column->column_name) }}

                                @if($user->can('add_tasks') || $user->can('edit_tasks') || $user->can('delete_tasks'))
                               <div style="position: relative;" class=" pull-right">
                                   <a href="javascript:;"  data-toggle="dropdown"  class="dropdown-toggle "><i class="ti-settings text-white font-normal"></i></a>
                                   <ul role="menu" class="dropdown-menu">
                                       @if($user->can('add_tasks'))
                                           <li><a href="javascript:;" data-column-id="{{ $column->id }}" class="add-task">@lang('modules.tasks.newTask')</a></li>
                                       @endif

                                       @if($user->can('edit_tasks'))
                                           <li><a href="javascript:;" data-column-id="{{ $column->id }}" class=" edit-column" >@lang('app.edit')</a>
                                       </li>
                                       @endif

                                       @if($column->id != 1)
                                           @if($user->can('delete_tasks'))
                                               <li><a href="javascript:;" data-column-id="{{ $column->id }}" class=" delete-column"  >@lang('app.delete')</a></li>
                                           @endif
                                       @endif
                                   </ul>

                               </div>
                                    @endif
                            </h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                @if($user->can('view_tasks'))
                                    @foreach($column->tasks as $task)
                                        <div class="panel panel-default lobipanel" data-task-id="{{ $task->id }}" data-sortable="true">
                                            <div class="panel-body">
                                                {{ ucfirst($task->heading) }}
                                                <div class="b-t p-t-10 p-b-10 m-t-5">
                                                    {!!  ($task->user->image) ? '<img src="'.asset('user-uploads/avatar/'.$task->user->image).'"
                                                                            alt="user" class="img-circle" width="30">' : '<img src="'.asset('default-profile-2.png').'"
                                                                            alt="user" class="img-circle" width="30">' !!}

                                                    <label class="label label-inverse">{{ $task->due_date->format('d M, Y') }}</label>
                                                    <a href="javascript:;" class="btn btn-default btn-rounded btn-xs view-task" data-task-id="{{ $task->id }}"><i class="fa fa-eye"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($column->membertasks as $task)
                                        <div class="panel panel-default lobipanel" data-task-id="{{ $task->id }}" data-sortable="true">
                                            <div class="panel-body">
                                                {{ ucfirst($task->heading) }}
                                                <div class="b-t p-t-10 p-b-10 m-t-5">
                                                    {!!  ($task->user->image) ? '<img src="'.asset('user-uploads/avatar/'.$task->user->image).'"
                                                                            alt="user" class="img-circle" width="30">' : '<img src="'.asset('default-profile-2.png').'"
                                                                            alt="user" class="img-circle" width="30">' !!}

                                                    <label class="label label-inverse">{{ $task->due_date->format('d M, Y') }}</label>
                                                    <a href="javascript:;" class="btn btn-default btn-rounded btn-xs view-task" data-task-id="{{ $task->id }}"><i class="fa fa-eye"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <div class="panel panel-default lobipanel"  data-sortable="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
        @endforeach

    </div>
    <!-- .row -->
    </div>

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="eventDetailModal" role="dialog" aria-labelledby="myModalLabel"
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
<script src="{{ asset('plugins/bower_components/lobipanel/dist/js/lobipanel.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

<script>
    // Colorpicker

    $(".colorpicker").asColorPicker();


    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('member.taskboard.store')}}',
            container: '#addColumn',
            data: $('#addColumn').serialize(),
            type: "POST"
        })
    });

    $('.edit-column').click(function () {
        var id = $(this).data('column-id');
        var url = '{{ route("member.taskboard.edit", ':id') }}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            success: function (response) {
                $('#edit-column-form').html(response.view);
                $(".colorpicker").asColorPicker();
                $('#edit-column-form').show();
            }
        })
    })

    $('#edit-column-form').on('click', '#update-form', function () {
        var id = $(this).data('column-id');
        var url = '{{route('member.taskboard.update', ':id')}}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            container: '#updateColumn',
            data: $('#updateColumn').serialize(),
            type: "PUT"
        })
    });
</script>

<script>
    $(function () {
        $('.lobipanel').on('dragged.lobiPanel', function () {
            var $parent = $(this).parent(),
            $children = $parent.children();

            var boardColumnIds = [];
            var taskIds = [];
            var prioritys = [];

            $children.each(function (ind, el) {
//                console.log(el, $(el).index());
                boardColumnIds.push($(el).closest('.board-column').data('column-id'));
                taskIds.push($(el).data('task-id'));
                prioritys.push($(el).index());
            });

            // update values for all tasks
            $.easyAjax({
                url: '{{ route("member.taskboard.updateIndex") }}',
                type: 'POST',
                data:{boardColumnIds: boardColumnIds, taskIds: taskIds, prioritys: prioritys,'_token':'{{ csrf_token() }}'},
                success: function (response) {
                }
            });

        }).lobiPanel({
            sortable: true,
            reload: false,
            editTitle: false,
            close: false,
            minimize: false,
            unpin: false,
            expand: false

        });


        $('.view-task').click(function () {
            var id = $(this).data('task-id');
            var url = '{{ route('member.task-calendar.show', ':id')}}';
            url = url.replace(':id', id);

            $('#modelHeading').html('Task Detail');
            $.ajaxModal('#eventDetailModal', url);
        })

        $('.add-task').click(function () {
            var id = $(this).data('column-id');
            var url = '{{ route('member.all-tasks.ajaxCreate', ':id')}}';
            url = url.replace(':id', id);

            $('#modelHeading').html('Add Task');
            $.ajaxModal('#eventDetailModal', url);
        })

        $('#add-column').click(function () {
            $('#add-column-form').toggle();
        })

        $('.delete-column').click(function () {
            var id = $(this).data('column-id');
            var url = '{{ route('member.taskboard.destroy', ':id')}}';
            url = url.replace(':id', id);

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted column!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.easyAjax({
                        url: url,
                        type: 'POST',
                        data: { '_token': '{{ csrf_token() }}', '_method': 'DELETE'},
                        success: function (response) {
                            if(response.status == 'success'){
                                window.location.reload();
                            }
                        }
                    });

                }
            });

        })

    });
</script>

@endpush