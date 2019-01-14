<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <!-- .User Profile -->
        <ul class="nav" id="side-menu">
            {{--<li class="sidebar-search hidden-sm hidden-md hidden-lg">--}}
                {{--<!-- / Search input-group this is only view in mobile-->--}}
                {{--<div class="input-group custom-search-form">--}}
                    {{--<input type="text" class="form-control" placeholder="Search...">--}}
                        {{--<span class="input-group-btn">--}}
                        {{--<button class="btn btn-default" type="button"> <i class="fa fa-search"></i> </button>--}}
                        {{--</span>--}}
                {{--</div>--}}
                {{--<!-- /input-group -->--}}
            {{--</li>--}}

            <li class="user-pro">
                @if(is_null($user->image))
                    <a href="#" class="waves-effect"><img src="{{ asset('default-profile-3.png') }}" alt="user-img" class="img-circle"> <span class="hide-menu">{{ (strlen($user->name) > 24) ? substr(ucwords($user->name), 0, 20).'..' : ucwords($user->name) }}
                            <span class="fa arrow"></span></span>
                    </a>
                @else
                    <a href="#" class="waves-effect"><img src="{{ asset('user-uploads/avatar/'.$user->image) }}" alt="user-img" class="img-circle"> <span class="hide-menu">{{ ucwords($user->name) }}
                            <span class="fa arrow"></span></span>
                    </a>
                @endif
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('member.profile.index') }}"><i class="ti-user"></i> @lang("app.menu.profileSettings")</a></li>
                    @if($user->hasRole('admin'))
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-sign-in"></i>  @lang("app.loginAsAdmin")
                            </a>
                        </li>
                    @endif
                        <li role="separator" class="divider"></li>
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                        ><i class="fa fa-power-off"></i> @lang('app.logout')</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </li>

            <li><a href="{{ route('member.dashboard') }}" class="waves-effect"><i class="icon-speedometer"></i> <span class="hide-menu">@lang("app.menu.dashboard") </span></a> </li>

            @if(\App\ModuleSetting::checkModule('clients'))
            @if($user->can('view_clients'))
            <li><a href="{{ route('member.clients.index') }}" class="waves-effect"><i class="icon-people"></i> <span class="hide-menu">@lang('app.menu.clients') </span></a> </li>
            @endif
            @endif

            @if(\App\ModuleSetting::checkModule('employees'))
            @if($user->can('view_employees'))
                <li><a href="{{ route('member.employees.index') }}" class="waves-effect"><i class="icon-user"></i> <span class="hide-menu">@lang('app.menu.employees') </span></a> </li>
            @endif
            @endif

            {{--@if(\App\ModuleSetting::checkModule('projects'))--}}
            {{--<li><a href="{{ route('member.projects.index') }}" class="waves-effect"><i class="icon-layers"></i> <span class="hide-menu">@lang("app.menu.projects") </span> @if($unreadProjectCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif</a> </li>--}}
            {{--@endif--}}

            @if(\App\ModuleSetting::checkModule('tasks'))
            <li><a href="{{ route('member.task.index') }}" class="waves-effect"><i class="ti-layout-list-thumb"></i> <span class="hide-menu"> @lang('app.menu.tasks') <span class="fa arrow"></span> </span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('member.all-tasks.index') }}">@lang('app.menu.tasks')</a></li>
                    <li><a href="{{ route('member.taskboard.index') }}">@lang('modules.tasks.taskBoard')</a></li>
                    <li><a href="{{ route('member.task-calendar.index') }}">@lang('app.menu.taskCalendar')</a></li>
                </ul>
            </li>
            @endif

            @if(\App\ModuleSetting::checkModule('leads') && $user->can('view_lead'))
                <li><a href="{{ route('member.leads.index') }}" class="waves-effect"><i class="icon-doc"></i> <span class="hide-menu">@lang('app.menu.lead') </span></a> </li>
            @endif

            @if(\App\ModuleSetting::checkModule('timelogs'))
                <li><a href="{{ route('member.all-time-logs.index') }}" class="waves-effect"><i class="icon-clock"></i> <span class="hide-menu">@lang('app.menu.timeLogs') </span></a> </li>
            @endif

            @if(\App\ModuleSetting::checkModule('attendance'))
            <li><a href="{{ route('member.attendances.index') }}" class="waves-effect"><i class="icon-clock"></i> <span class="hide-menu">@lang("app.menu.attendance") </span></a> </li>
            @endif

            @if(\App\ModuleSetting::checkModule('holidays'))
            <li><a href="{{ route('member.holidays.index') }}" class="waves-effect"><i class="icon-calender"></i> <span class="hide-menu">@lang("app.menu.holiday") </span></a> </li>
            @endif

            {{--@if(\App\ModuleSetting::checkModule('tickets'))--}}
            {{--<li><a href="{{ route('member.tickets.index') }}" class="waves-effect"><i class="ti-ticket"></i> <span class="hide-menu">@lang("app.menu.tickets") </span></a> </li>--}}
            {{--@endif--}}

            {{--@if(\App\ModuleSetting::checkModule('messages'))--}}
            {{--<li><a href="{{ route('member.user-chat.index') }}" class="waves-effect"><i class="icon-envelope"></i> <span class="hide-menu">@lang("app.menu.messages") @if($unreadMessageCount > 0)<span class="label label-rouded label-custom pull-right">{{ $unreadMessageCount }}</span> @endif--}}
                    {{--</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            {{--@endif--}}

            {{--@if(\App\ModuleSetting::checkModule('events'))--}}
            {{--<li><a href="{{ route('member.events.index') }}" class="waves-effect"><i class="icon-calender"></i> <span class="hide-menu">@lang('app.menu.Events')</span></a> </li>--}}
            {{--@endif--}}

            @if(\App\ModuleSetting::checkModule('leaves'))
            <li><a href="{{ route('member.leaves.index') }}" class="waves-effect"><i class="icon-logout"></i> <span class="hide-menu">@lang('app.menu.leaves')</span></a> </li>
            @endif

            @if(\App\ModuleSetting::checkModule('notices'))
            @if($user->can('view_notice'))
                <li><a href="{{ route('member.notices.index') }}" class="waves-effect"><i class="ti-layout-media-overlay"></i> <span class="hide-menu">@lang("app.menu.noticeBoard") </span></a> </li>
            @endif
            @endif
        </ul>
    </div>
</div>