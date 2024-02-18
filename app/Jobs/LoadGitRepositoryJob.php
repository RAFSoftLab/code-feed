<?php

namespace App\Jobs;

use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LoadGitRepositoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $user =  $this->data['user'];
        $selectedRepository = $this->data['repository'];
        $githubRepositoryUrl = $selectedRepository;
        $feedService = new FeedService(new GoogleAIService(), $user, $githubRepositoryUrl);
        $feedService->loadFreshFeed();
    }
}
