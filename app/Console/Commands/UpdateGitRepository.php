<?php

namespace App\Console\Commands;

use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use Illuminate\Console\Command;

class UpdateGitRepository  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-git-repository {githubRepository=https://github.com/RAFSoftLab/code-Feed-test-repo.git}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads a git repository from web';

    /**
     * Execute the console command.
     */
    public function handle(GoogleAIService $aiService): void
    {
        $githubRepositoryUrl = $this->argument('githubRepository');

        $feedService = new FeedService(new GoogleAIService(), $githubRepositoryUrl);
        $feedService->updateFeed();
    }
}