<?php

namespace App\Models;

use App\Services\GithubService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'author_name',
        'author_email',
        'message',
        'repository',
        'hash',
        'committer',
        'created_at',
        'committed_at',
        'organization'
    ];

    /**
     * GetTitle should return the first line in message and if the line is longer than 120 characters,
     * it should be truncated
     */
    public function getTitle(): bool|string
    {
        return $this->parseTitleAndSummary($this->message)['title'];
    }

    public function getSummary(): string
    {
        return $this->parseTitleAndSummary($this->message)['summary'];
    }

    function parseTitleAndSummary(string $commitMessage): array
    {
        $pattern = '/^(?P<title>.+?)\n{2}(?P<summary>.+)/s';
        if (preg_match($pattern, $commitMessage, $matches)) {
            return [
                'title' => $matches['title'],
                'summary' => $matches['summary']
            ];
        }
        return [
            'title' => $commitMessage,
            'summary' => ''
        ];
    }

    /**
     * Get the GitHub avatar URL from the author.
     *
     * @return string|null The GitHub avatar URL or null if not found.
     */
    public function getGithubAvatarUrl(GithubService $service): ?string
    {
        return $service->loadAvatar($this->author_email);
    }
}
