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
        $firstLine = strtok($this->message, "\n");
        if (strlen($firstLine) > 120) {
            return substr($firstLine, 0, 120) . '...';
        }
        return $firstLine;
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
