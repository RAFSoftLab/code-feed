<?php

namespace Tests\Feature\app\Services\Feed;


use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use Tests\TestCase;

class FeedServiceTest extends TestCase
{

    public function testLoadFeed()
    {
        $feedService = new FeedService(new GoogleAIService(), null, 'https://github.com/RAFSoftLab/code-Feed-test-repo.git');
        $feedService->loadFreshFeed();

        self::assertEquals(6, $feedService->getFeed()->count());

        $feedService->updateFeed();
        // TODO add testing for new commits.
    }
}
