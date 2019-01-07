<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-success"><i class="ti-layout-list-thumb"></i></span>{{ ucfirst($notification->data['heading']) }} - @lang('email.taskComplete.subject')</h5>
        {{ ucfirst($notification->data['description']) }} </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->data['completed_on'])->diffForHumans() }} </i></h6>
</div>