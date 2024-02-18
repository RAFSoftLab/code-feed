<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAllRepositories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-all-repositories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates all the repositories';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info("Updating all the repositories");
        $users = User::with('repositories')->get();
        foreach ($users as $user) {
            foreach ($user->repositories as $repository) {
                $feedService = new FeedService(new GoogleAIService(), $user, $repository->url);
                $feedService->updateFeed();
            }
        }
    }
}
