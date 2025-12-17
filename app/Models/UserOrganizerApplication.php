<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganizerApplication extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'event_description',
        'is_individual',
        'website_url',
    ];
}
