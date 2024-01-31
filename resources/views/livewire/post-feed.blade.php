<div>
    @foreach ($posts as $post)
        <div class="post-feed-item">
            <h2 class="post-title">
                {{ $post->title }}
            </h2>

            <div class="post-content">
                {!! nl2br(e($post->content)) !!}
            </div>

            <div class="post-meta">
                <span class="post-created-at">
                  {{ $post->created_at->diffForHumans()}}
                </span>
            </div>
        </div>
    @endforeach
</div>
