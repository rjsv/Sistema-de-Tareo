@extends('layouts.app')

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">{{ $pageTitle }}</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-6">

                                    <h3 class="box-title m-b-0">@lang("modules.emailSettings.notificationTitle")</h3>

                                    <p class="text-muted m-b-10 font-13">
                                        @lang("modules.emailSettings.notificationSubtitle")
                                    </p>

                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12 b-t p-t-20">
                                            {!! Form::open(['id'=>'editSettings','class'=>'ajax-form form-horizontal','method'=>'PUT']) !!}

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.userRegistration")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[4]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[4]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.employeeAssign")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[5]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[5]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.newNotice")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[6]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[6]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.taskAssign")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[7]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[7]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.expenseAdded")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[0]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[0]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.expenseMember")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[1]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[1]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.expenseStatus")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[2]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[2]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.ticketRequest")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[3]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[3]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.leaveRequest")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[8]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[8]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-sm-8">@lang("modules.emailSettings.taskComplete")</label>

                                                    <div class="col-sm-4">
                                                        <div class="switchery-demo">
                                                            <input type="checkbox"
                                                                   @if($emailSettings[9]->send_email == 'yes') checked
                                                                   @endif class="js-switch change-email-setting"
                                                                   data-color="#99d683"
                                                                   data-setting-id="{{ $emailSettings[9]->id }}"/>
                                                        </div>
                                                    </div>
                                                </div>

                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">


                                    <h3 class="box-title m-b-0">@lang("modules.emailSettings.configTitle")</h3>

                                    <p class="text-muted m-b-10 font-13">
                                        &nbsp;
                                    </p>

                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12 b-t p-t-20">
                                            {!! Form::open(['id'=>'updateSettings','class'=>'ajax-form','method'=>'POST']) !!}
                                            {!! Form::hidden('_token', csrf_token()) !!}
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-12 ">
                                                        <div class="form-group">
                                                            <label>@lang("modules.emailSettings.mailDriver")</label>
                                                            <select class="form-control" name="mail_driver"
                                                                    id="mail_driver">
                                                                <option @if($smtpSetting->mail_driver == 'smtp') selected @endif>
                                                                    smtp
                                                                </option>
                                                                <option @if($smtpSetting->mail_driver == 'mail') selected @endif>
                                                                    mail
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>@lang("modules.emailSettings.mailHost")</label>
                                                            <input type="text" name="mail_host" id="mail_host"
                                                                   class="form-control"
                                                                   value="{{ $smtpSetting->mail_host }}">
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                </div>
                                                <div class="row">

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>@lang("modules.emailSettings.mailPort")</label>
                                                            <input type="text" name="mail_port" id="mail_port"
                                                                   class="form-control"
                                                                   value="{{ $smtpSetting->mail_port }}">
                                                        </div>
                                                    </div>
                                                    <!--/span-->

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>@lang("modules.emailSettings.mailUsername")</label>
                                                            <input type="text" name="mail_username" id="mail_username"
                                                                   class="form-control"
                                                                   value="{{ $smtpSetting->mail_username }}">
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                </div>
                                                <!--/row-->

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">@lang("modules.emailSettings.mailPassword")</label>
                                                            <input type="password" name="mail_password"
                                                                   id="mail_password"
                                                                   class="form-control"
                                                                   value="{{ $smtpSetting->mail_password }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">@lang("modules.emailSettings.mailFrom")</label>
                                                            <input type="text" name="mail_from_name" id="mail_from_name"
                                                                   class="form-control"
                                                                   value="{{ $smtpSetting->mail_from_name }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">@lang("modules.emailSettings.mailFromEmail")</label>
                                                            <input type="text" name="mail_from_email" id="mail_from_email"
                                                                   class="form-control"
                                                                   value="{{ $smtpSetting->mail_from_email }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">@lang("modules.emailSettings.mailEncryption")</label>
                                                            <select class="form-control" name="mail_encryption"
                                                                    id="mail_encryption">
                                                                <option @if($smtpSetting->mail_encryption == 'tls') selected @endif>
                                                                    tls
                                                                </option>
                                                                <option @if($smtpSetting->mail_encryption == 'ssl') selected @endif>
                                                                    ssl
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->


                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" id="save-form" class="btn btn-success"><i
                                                            class="fa fa-check"></i>
                                                    @lang('app.update')
                                                </button>
                                                <button type="button" id="send-test-email" class="btn btn-primary">@lang('modules.emailSettings.sendTestEmail')</button>
                                                <button type="reset" class="btn btn-default">@lang('app.reset')</button>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                </div>
                                <!-- .row -->

                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
        <!-- .row -->


        @endsection

        @push('footer-script')
        <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
        <script>

            // Switchery
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            $('.js-switch').each(function () {
                new Switchery($(this)[0], $(this).data());

            });

            $('.change-email-setting').change(function () {
                var id = $(this).data('setting-id');

                if ($(this).is(':checked'))
                    var sendEmail = 'yes';
                else
                    var sendEmail = 'no';

                var url = '{{route('admin.email-settings.update', ':id')}}';
                url = url.replace(':id', id);
                $.easyAjax({
                    url: url,
                    type: "POST",
                    data: {'id': id, 'send_email': sendEmail, '_method': 'PUT', '_token': '{{ csrf_token() }}'}
                })
            });

            $('#save-form').click(function () {

                var url = '{{route('admin.email-settings.updateMailConfig')}}';

                $.easyAjax({
                    url: url,
                    type: "POST",
                    container: '#updateSettings',
                    data: $('#updateSettings').serialize()
                })
            });

            $('#send-test-email').click(function () {

                var url = '{{route('admin.email-settings.sendTestEmail')}}';

                $.easyAjax({
                    url: url,
                    type: "GET",
                    success: function (response) {

                    }
                })
            });
        </script>
    @endpush