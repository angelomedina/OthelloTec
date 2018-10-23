<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSession extends Pivot
{
    protected $table = 'session_user';

     protected $fillable = [
        'user_id', 'session_id','score','piece_color'
    ];

}
