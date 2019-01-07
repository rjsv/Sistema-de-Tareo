@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }} @lang('modules.holiday.listOf') {{ \Carbon\Carbon::now()->format('Y') }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }} @lang('modules.holiday.listOf') {{ \Carbon\Carbon::now()->format('Y') }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

@endpush

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group pull-left">
                            @if($user->can('add_holiday'))
                                <a onclick="showAdd()" class="btn btn-outline btn-success btn-sm ">@lang('modules.holiday.addNewHoliday') <i class="fa fa-plus" aria-hidden="true"></i></a>
                            @endif
                        </div>
                        <div class="form-group pull-right">
                            <a href="{{ route('member.holidays.calendar') }}" class="btn btn-outline btn-info btn-sm ">@lang('modules.holiday.viewOnCalendar') <i class="fa fa-calendar" aria-hidden="true"></i></a>
                        </div>
                        @if($user->can('add_holiday'))
                            <div class="pull-right" style="margin-right: 10px">
                                @if($number_of_sundays>$holidays_in_db)
                                    <a class="btn btn-outline btn-sm btn-primary" onclick="showMarkHoliday()">
                                        @lang('modules.holiday.markSunday')
                                        <i class="fa fa-check"></i> </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row" id="holidaySection">
                    <div class="col-lg-12 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="vtabs">
                                <ul class="nav tabs-vertical">
                                    @foreach($months as $month)
                                        <li class="tab nav-item @if($month == $currentMonth) active @endif">
                                            <a data-toggle="tab" href="#{{ $month }}" class="nav-link " aria-expanded="@if($month == $currentMonth) true @else false @endif ">
                                                <i class="fa fa-calendar"></i> {{ $month }} </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" style="padding-top: 0;">
                                    @foreach($months as $month)
                                        <div id="{{$month}}" class="tab-pane @if($month == $currentMonth) active @endif">
                                            <div class="panel panel-info block4">
                                                <div class="panel-heading">
                                                    <div class="caption">
                                                        <i class="fa fa-calendar"> </i> {{$month}}
                                                    </div>

                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th> # </th>
                                                                <th> @lang('modules.holiday.date') </th>
                                                                <th> @lang('modules.holiday.occasion') </th>
                                                                <th> @lang('modules.holiday.day') </th>
                                                                <th> @lang('modules.holiday.action') </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @if(isset($holidaysArray[$month]))

                                                                @for($i=0;$i<count($holidaysArray[$month]['date']);$i++)

                                                                    <tr id="row{{ $holidaysArray[$month]['id'][$i] }}">
                                                                        <td> {{($i+1)}} </td>
                                                                        <td> {{ $holidaysArray[$month]['date'][$i] }} </td>
                                                                        <td> {{ $holidaysArray[$month]['ocassion'][$i] }} </td>
                                                                        <td> {{ $holidaysArray[$month]['day'][$i] }} </td>
                                                                        <td>
                                                                            @if($user->can('delete_holiday'))
                                                                            <button type="button" onclick="del('{{ $holidaysArray[$month]['id'][$i] }}',' {{ $holidaysArray[$month]['date'][$i] }}')" href="#" class="btn btn-xs btn-danger">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endfor
                                                            @endif

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="edit-column-form" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
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
    <script>
       // Delete Holiday
        function del(id, date) {

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted holiday!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('member.holidays.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                    });
                }
            });
        }
        // Show Create Holiday Modal
        function showAdd() {
            var url = "{{ route('member.holidays.create') }}";
            $.ajaxModal('#edit-column-form', url);
        }
        // Show Create Holiday Modal
        function showMarkHoliday() {
            var url = "{{ route('member.holidays.mark-holiday') }}";
            $.ajaxModal('#edit-column-form', url);
        }

    </script>
@endpush