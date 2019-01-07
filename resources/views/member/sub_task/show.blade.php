@foreach($subTasks as $subtask)
    <li class="list-group-item row">
        <div class="col-xs-6">
            <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                       @if($subtask->status == 'complete') checked @endif>
                <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                <a href="#" class="text-muted @if($user->can('edit_tasks')) edit-sub-task @endif" data-name="title"  data-url="{{ route('member.sub-task.update', $subtask->id) }}" data-pk="{{ $subtask->id }}" data-type="text" data-value="{{ ucfirst($subtask->title) }}">{{ ucfirst($subtask->title) }}</a>
            </div>
        </div>
        <div class="col-xs-5 text-right">
            <a href="#"  data-type="combodate" data-name="due_date" data-url="{{ route('member.sub-task.update', $subtask->id) }}"  data-emptytext="@lang('app.dueDate')" class="m-r-10 @if($user->can('edit_tasks')) edit-sub-task-date @endif"  data-format="YYYY-MM-DD" data-viewformat="DD/MM/YYYY" data-template="D / MMM / YYYY" data-value="@if($subtask->due_date){{ $subtask->due_date->format('Y-m-d') }}@endif" data-pk="{{ $subtask->id }}" data-title="@lang('app.dueDate')">@if($subtask->due_date){{ $subtask->due_date->format('d M, Y') }}@endif</a>
        </div>
        @if($user->can('delete_tasks'))
            <div class="col-xs-1">
                <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" class="btn btn-danger btn-xs delete-sub-task"><i class="fa fa-times"></i></a>
            </div>
        @endif
    </li>
@endforeach