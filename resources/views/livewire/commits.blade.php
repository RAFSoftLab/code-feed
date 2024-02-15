<div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @foreach ($commits as $commit)
                            <div class="post-feed-item"
                                 style="display: flex;
                                 align-items: center; /* Align items vertically */
                                 margin-bottom: 20px;">
                                <div class="avatar">
                                    <img src="{{$commit->getGithubAvatarUrl($githubService)}}" alt="User Avatar">
                                </div>
                                <div class="content">
                                    <p>
                                        <a class="post-link"
                                           href="{{'https://github.com/'.$commit->repository->organization.'/'.$commit->repository->name.'/commit/'.$commit->hash}}">{{$commit->title}}</a>
                                    </p>
                                    <div class="post-content">
                                        {!! nl2br(e($commit->summary)) !!}
                                    </div>
                                    <div class="details">
                                        @if($commit->hasBugs)
                                            <a href="/{{$commit->repository->organization}}/{{$commit->repository->name}}/commits/{{$commit->hash}}" class="label red">BUGS</a>
                                        @endif
                                        @if($commit->hasSecurityIssues)
                                            <a href="/{{$commit->repository->organization}}/{{$commit->repository->name}}/commits/{{$commit->hash}}" class="label red">SECURITY</a>
                                        @endif
                                        <span class="time">{{$commit->created_at->diffForHumans()}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
