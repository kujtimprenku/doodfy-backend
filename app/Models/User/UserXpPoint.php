<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserXpPoint extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'activity_id',
    ];
}
