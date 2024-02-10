<?php

namespace App\Console\Commands;

use App\Models\Commit;
use App\Models\Post;
use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use App\Services\Git\GitRepositoryService;
use Carbon\Carbon;
use Gitonomy\Git\Admin;
use Gitonomy\Git\Tree;
use Illuminate\Console\Command;

class LoadGitRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-git-repository {githubRepository=https://github.com/RAFSoftLab/code-Feed-test-repo.git}';

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

        $feedService = new FeedService();
        $feedService->loadFreshFeed($githubRepositoryUrl, new GoogleAIService());
    }
}
