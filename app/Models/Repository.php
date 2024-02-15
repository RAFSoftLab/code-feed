<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization',
        'url',
        'description',
        'user_id'
    ];

    public function commits(): HasMany
    {
        return $this->hasMany(Commit::class);
    }
}
