<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = ['author', 'message', 'repository', 'tree',  'committer', 'created_at', 'committed_at', 'organization'];
}
