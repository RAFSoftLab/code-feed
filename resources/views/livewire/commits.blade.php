<div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @foreach ($commits as $commit)
                            <div class="newsfeed-item">
                                <div class="avatar">
                                    <img src="{{$commit->getGithubAvatarUrl($githubService)}}" alt="User Avatar">
                                </div>
                                <div class="content">
                                    <p>
                                        <a class="post-link"
                                           href="{{'https://github.com/'.$commit->organization.'/'.$commit->repository.'/commit/'.$commit->hash}}">{{$commit->title}}</a>
                                    </p>
                                    <p class="summary">{{ \Illuminate\Support\Str::limit($commit->summary, 300) }}</p>
                                    <div class="details">
                                        @if($commit->hasBugs)
                                            <a href="/{{$commit->organization}}/{{$commit->repository}}/commits/{{$commit->hash}}" class="label red">BUGS</a>
                                        @endif
                                        @if($commit->hasSecurityIssues)
                                                <a href="/{{$commit->organization}}/{{$commit->repository}}/commits/{{$commit->hash}}" class="label red">SECURITY</a>
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
