<?php

namespace App\Services;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Http;

class GithubService
{
    private array $emailsToAvatars = array();

    public function loadAvatar(string $email)
    {
        if (isset($this->emailsToAvatars[$email]))
        {
            return $this->emailsToAvatars[$email];
        }

        $response = Http::get('https://api.github.com/search/users', [
            'q' => "in:email " . $email,
        ]);
        $data = $response->json();
        if ($response->ok() && count($data['items']) > 0) {
            $username = $data['items'][0]['login'];
            $this->emailsToAvatars[$email] = "https://avatars.githubusercontent.com/{$username}";
            return $this->emailsToAvatars[$email];
        }

        return 'https://www.shutterstock.com/image-vector/default-avatar-profile-icon-vector-600nw-1745180411.jpg';
    }

}
