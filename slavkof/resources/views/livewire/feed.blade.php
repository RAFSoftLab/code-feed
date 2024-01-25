<div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @foreach ($posts as $post)
                        <div class="newsfeed-item">
                            <div class="avatar">
                                <img src="{{$post->getGithubAvatarUrl($githubService)}}" alt="User Avatar">
                            </div>
                            <div class="content">
                                <p>
                                    {{$post->getAuthor()}}<a  class="post-link" href={{'https://github.com/'.$post->organization.'/'. $post->repository.'/commit/'.$post->tree}}>{{$post->getTitle() }}</a>
                                </p>
                                <span class="time">{{date('d-m-Y-H-i-s', $post->createdAt)}}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
</div>
