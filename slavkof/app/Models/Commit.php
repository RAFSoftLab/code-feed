<?php

namespace App\Models;

use App\Services\GithubService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Commit extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'author',
        'message',
        'repository',
        'tree',
        'committer',
        'created_at',
        'committed_at',
        'organization'
    ];

    /*
     * GetTitle should return the first line in message and if the line is longer than 20 characters,
     * it should be truncated
     */
    public function getTitle(): bool|string
    {
        $firstLine = strtok($this->message, "\n");
        if (strlen($firstLine) > 120) {
            return substr($firstLine, 0, 120) . '...';
        }
        return $firstLine;
    }

    /**
     * If author is formatted as name <email>, return name
     */
    public function getAuthor(): string
    {
        return explode('<', $this->author)[0];
    }

    /**
     * Get the GitHub avatar URL from the author.
     *
     * @return string|null The GitHub avatar URL or null if not found.
     */
    public function getGithubAvatarUrl(GithubService $service): ?string
    {
        // Extract the email from the author
        $email = explode('<', $this->author)[1] ?? null;
        if (!$email) {
            return 'https://www.shutterstock.com/image-vector/default-avatar-profile-icon-vector-600nw-1745180411.jpg';
        }

        return $service->loadAvatar($email);
    }
}
