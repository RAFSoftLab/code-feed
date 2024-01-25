<div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @foreach ($posts as $post)
                        <div class="newsfeed-item">
                            <div class="avatar">
                                <img src="https://avatars.githubusercontent.com/u/23264032?v=4" alt="User Avatar">
                            </div>
                            <div class="content">
                                <p>
                                    <a href={{'https://github.com/'.$post->organization.'/'. $post->repository.'/commit/'.$post->tree}}>{{$post->getAuthor()}}</a>
                                    {{ $post->getTitle() }}
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
