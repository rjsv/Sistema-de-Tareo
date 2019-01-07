@extends('layouts.client-app')

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
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <style>
        .col-in {
            padding: 0 20px !important;

        }

        .fc-event{
            font-size: 10px !important;
        }

    </style>
@endpush

@section('content')

    <div class="row">
        @if(\App\ModuleSetting::checkModule('projects'))
        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="col-in row">
                    <h3 class="box-title">@lang('modules.dashboard.totalProjects')</h3>
                    <ul class="list-inline two-part">
                        <li><i class="icon-layers text-info"></i></li>
                        <li class="text-right"><span class="counter">{{ $counts->totalProjects }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @if(\App\ModuleSetting::checkModule('tickets'))
        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="col-in row">
                    <h3 class="box-title">@lang('modules.tickets.totalUnresolvedTickets')</h3>
                    <ul class="list-inline two-part">
                        <li><i class="ti-ticket text-warning"></i></li>
                        <li class="text-right"><span class="counter">{{ $counts->totalUnResolvedTickets }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @if(\App\ModuleSetting::checkModule('invoices'))
        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="col-in row">
                    <h3 class="box-title">@lang("modules.dashboard.totalPaidAmount")</h3>
                    <ul class="list-inline two-part">
                        <li><i class="fa fa-money text-success"></i></li>
                        <li class="text-right"><span class="counter">{{ floor($counts->totalPaidAmount) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="col-in row">
                    <h3 class="box-title">@lang("modules.dashboard.totalOutstandingAmount")</h3>
                    <ul class="list-inline two-part">
                        <li><i class="fa fa-money text-danger"></i></li>
                        <li class="text-right"><span class="counter">{{ floor($counts->totalUnpaidAmount) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        @endif

    </div>
    <!-- .row -->

    <div class="row" >

        @if(\App\ModuleSetting::checkModule('projects'))
        <div class="col-md-6" id="project-timeline">
            <div class="panel panel-default">
                <div class="panel-heading">@lang("modules.dashboard.projectActivityTimeline")</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="steamline">
                            @foreach($projectActivities as $activity)
                                <div class="sl-item">
                                    <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                    </div>
                                    <div class="sl-right">
                                        <div><h6><a href="{{ route('client.projects.show', $activity->project_id) }}" class="text-danger">{{ ucwords($activity->project_name) }}:</a> {{ $activity->activity }}</h6> <span class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

@endsection