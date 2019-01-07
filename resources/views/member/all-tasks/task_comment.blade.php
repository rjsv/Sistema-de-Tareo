@foreach($comments as $comment)
    <div class="row b-b m-b-5 font-12">
        <div class="col-xs-8">
            {!! ucfirst($comment->comment) !!} <br>
            @if($comment->user_id == $user->id)
                <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="text-danger delete-task-comment">@lang('app.delete')</a>
            @endif
        </div>
        <div class="col-xs-4 text-right">
            {{ ucfirst($comment->created_at->diffForHumans()) }}
        </div>
        <div class="col-xs-12 text-right m-t-5 m-b-5">
            &mdash; <i>{{ ucwords($comment->user->name) }}</i>
        </div>
    </div>
@endforeach