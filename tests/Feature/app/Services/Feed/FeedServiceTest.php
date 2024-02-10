<?php

namespace Tests\Feature\app\Services\Feed;


use App\Services\AI\GoogleAIService;
use App\Services\Feed\FeedService;
use Tests\TestCase;

class FeedServiceTest extends TestCase
{

    public function testLoadFreshFeed()
    {
        $feedService = new FeedService();
        $feedService->loadFreshFeed('https://github.com/RAFSoftLab/code-Feed-test-repo.git', new GoogleAIService());
        self::assertEquals(3, $feedService->getFeed()->count());
    }
}
