<?php

namespace App\Models;

use App\Services\GithubService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commit extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_name',
        'author_email',
        'title',
        'summary',
        'repository',
        'lineCount',
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
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
