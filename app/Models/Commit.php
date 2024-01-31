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
        'title',
        'summary',
        'repository',
        'hasSecurityIssues',
        'hasBugs',
        'hash',
        'committer',
        'created_at',
        'committed_at',
        'organization',
        'change'
    ];

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
