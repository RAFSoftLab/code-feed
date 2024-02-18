<?php

namespace App\Console\Commands;

use App\Models\Commit;
use App\Models\Post;
use App\Models\User;
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
    protected $signature = 'app:load-git-repository {githubRepository=https://github.com/RAFSoftLab/code-feed-test-repo.git} {user_email=slavko.fodor@gmail.com}';

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
        $userEmail = $this->argument('user_email');
        $user = User::where('email', $userEmail)->first();

        $feedService = new FeedService(new GoogleAIService(), $user, $githubRepositoryUrl);
        $feedService->loadFreshFeed();
    }
}
