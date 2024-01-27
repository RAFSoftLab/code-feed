<?php

namespace Tests\Feature\app\Services;

use Tests\TestCase;

class GithubServiceTest extends TestCase
{
    public function test_get_avatar_from_good_email(): void
    {
        $githubService = new \App\Services\GithubService();
        $avatar = $githubService->loadAvatar('slavko.fodor@gmail.com');
        self::assertEquals('https://avatars.githubusercontent.com/slavkof', $avatar);
    }

    public function test_get_avatar_from_bad_email(): void
    {
        $githubService = new \App\Services\GithubService();
        $avatar = $githubService->loadAvatar('bad_email@example.com');
        self::assertEquals('https://www.shutterstock.com/image-vector/default-avatar-profile-icon-vector-600nw-1745180411.jpg', $avatar);
    }
}
