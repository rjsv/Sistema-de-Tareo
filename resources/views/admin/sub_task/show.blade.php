@foreach($subTasks as $subtask)
    <li class="list-group-item row">
        <div class="col-xs-12">
            <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                       @if($subtask->status == 'complete') checked @endif>
                <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                <a href="#" class="text-muted edit-sub-task" data-name="title"  data-url="{{ route('admin.sub-task.update', $subtask->id) }}" data-pk="{{ $subtask->id }}" data-type="text" data-value="{{ ucfirst($subtask->title) }}"></a>
            </div>
        </div>
        <div class="col-xs-11 text-right m-t-10">
            <a href="#"  data-type="combodate" data-name="due_date" data-url="{{ route('admin.sub-task.update', $subtask->id) }}"  data-emptytext="@lang('app.dueDate')" class="m-r-10 edit-sub-task-date"  data-format="YYYY-MM-DD" data-viewformat="DD/MM/YYYY" data-template="D / MMM / YYYY" data-value="@if($subtask->due_date){{ $subtask->due_date->format('Y-m-d') }}@endif" data-pk="{{ $subtask->id }}" data-title="@lang('app.dueDate')">@if($subtask->due_date){{ $subtask->due_date->format('d M, Y') }}@endif</a>
        </div>
        <div class="col-xs-1 m-t-10">
            <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" class="btn btn-danger btn-xs delete-sub-task"><i class="fa fa-times"></i></a>
        </div>
    </li>
@endforeach