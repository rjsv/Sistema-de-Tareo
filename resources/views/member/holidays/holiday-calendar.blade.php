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
                <li><a href="{{ route('member.holidays.index') }}">@lang('app.menu.holiday')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

@endpush

@section('content')

    <div class="row">
        <div class="col-md-8">
            <div class="white-box">
                <div class="row">
                    <h3 class="box-title col-md-3">@lang('app.menu.holiday')</h3>

                </div>


                <div id="calendar"></div>
            </div>
        </div>
        <div class="col-md-4 show" id="new-follow-panel" style="">
            <div class="panel panel-default">
                <div class="panel-heading "><i class="ti-calendar"></i> <span id="currentMonthName">New</span> Holidays  <div class="panel-action">
                    </div>
                </div>
                <div class="panel-wrapper collapse in">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Occassion</th>
                        </tr>
                        </thead>
                        <tbody id="monthDetailData">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')

<script>

    var taskEvents = [
        @foreach($holidays as $holiday)
        {
            id: '{{ ucfirst($holiday->id) }}',
            title: function () {
                var reson = '{{ ucfirst($holiday->occassion) }}';
                if(reson){
                    return reson;
                }
                else{
                    return 'Not Define';
                }
            },
            start: '{{ $holiday->date }}',
            end:  '{{ $holiday->date }}',
            className:function(){
                var occassion = '{{ $holiday->occassion }}';
                if(occassion == 'Sunday' || occassion == 'Saturday'){
                    return 'bg-info';
                }else{
                    return 'bg-danger';
                }
            }
        },
        @endforeach
];

    var calendarLocale = '{{ $global->locale }}';


</script>

<script src="{{ asset('plugins/bower_components/calendar/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/locale-all.js') }}"></script>
<script src="{{ asset('js/holiday-calendar.js') }}"></script>

<script>

    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    var currentMonth = new Date();
    $('#currentMonthName').html(monthNames[currentMonth.getMonth()]);
    var currentMonthData = '';

    setMonthData(currentMonth);
    $('.fc-button-group .fc-prev-button').click(function(){
        var bs = $('#calendar').fullCalendar('getDate');
        var d = new Date(bs);
        setMonthData(d);
    });


    $('.fc-button-group .fc-next-button').click(function(){
        var bs = $('#calendar').fullCalendar('getDate');
        var d = new Date(bs);
        setMonthData(d);
    });

    function setMonthData(d){
        var month_int = d.getMonth();
        var year_int = d.getFullYear();
        var firstDay = new Date(year_int, month_int, 1);
        var lastDay = new Date(year_int, month_int + 1, 0);

        firstDay = moment(firstDay).format("YYYY-MM-DD");
        lastDay = moment(lastDay).format("YYYY-MM-DD");

        var eventData = $('#calendar').fullCalendar('clientEvents', function(evt) {
            return evt.start.format("YYYY-MM-DD") >= firstDay && evt.start.format("YYYY-MM-DD") <= lastDay;
        });
        $('#currentMonthName').html(monthNames[d.getMonth()]);
        currentMonthData = '';
        $.each( eventData, function( key, value ) {
            currentMonthData += '<tr> <td align="center">'+(key+1)+'</td> <td>'+value.start.format("DD-MM-YYYY")+'</td> <td>'+value.title()+'</td> </tr>';
        });

        $('#monthDetailData').html(currentMonthData);
    }
</script>

@endpush
