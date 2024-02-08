<div>
    @foreach ($posts as $post)
        <div class="post-feed-item">
            <h2 class="post-title">
                <a href="/{{$post->commit->organization}}/{{$post->commit->repository}}/commits/{{$post->commit->hash}}" class="post-link">
                    {{ $post->title }}
                </a>
            </h2>

            <div class="post-content">
                {!! nl2br(e($post->content)) !!}
            </div>

            <div class="post-meta">
                <span class="post-created-at">
                  {{ $post->commit->organization }}/{{ $post->commit->repository }}
                </span>
                <span class="post-created-at">
                  {{ $post->commit->created_at->diffForHumans()}}
                </span>
            </div>
        </div>
    @endforeach
</div>
