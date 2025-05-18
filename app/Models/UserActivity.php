<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'source',
        'ip',
        'location',
    ];

    /**
     * Používateľ, ktorý vykonal danú akciu
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
