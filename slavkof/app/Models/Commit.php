<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
