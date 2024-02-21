<div>
    @foreach ($posts as $post)
        <div class="post-feed-item">
            <h2 class="post-title">
                <a href="/{{$post->commit->repository->organization}}/{{$post->commit->repository->name}}/commits/{{$post->commit->hash}}" class="post-link">
                    {{ $post->commit->title }}
                </a>
            </h2>

            <div class="post-content">
                {!! nl2br(e(empty($post->commit->summary)? $post->content : $post->commit->summary)) !!}
            </div>

            <div class="post-meta">
                <a href="{{'https://github.com/'.$post->commit->repository->organization.'/'.$post->commit->repository->name.'/commit/'.$post->commit->hash}}">
                    <span class="post-created-at">
                      {{ $post->commit->repository->organization }}/{{ $post->commit->repository->name }}
                    </span>
                    <span class="post-created-at">
                      {{ $post->commit->created_at->diffForHumans()}}
                    </span>
                </a>
            </div>
            <div class="details">
                @if($post->commit->hasBugs)
                    <span class="label red">BUGS</span>
                @endif
                @if($post->commit->hasSecurityIssues)
                        <span class="label red">SECURITY</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
