<?php

namespace App\Models;

use App\Services\GithubService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function comments(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
