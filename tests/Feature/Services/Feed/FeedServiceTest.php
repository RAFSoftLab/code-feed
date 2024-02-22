<?php

namespace Tests\Feature\Services\Feed;


use App\Models\User;
use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use Tests\TestCase;

class FeedServiceTest extends TestCase
{

    public function testLoadFeed()
    {
        $user = User::factory()->create();
        $feedService = new FeedService(new GoogleAIService(), $user, 'https://github.com/RAFSoftLab/code-Feed-test-repo.git');
        $feedService->loadFreshFeed();

        self::assertEquals(6, $feedService->getFeed()->count());

        $feedService->updateFeed();
        // TODO add testing for new commits.
    }
}
