<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'sessions';
    protected $fillable = [
          'state', 'current_player_id'
    ];

    public function users()
    {
        return $this->belongsToMany('\App\User')->using('\App\UserSession')->withPivot('score');
    }
    public function board(){
        return $this->hasOne('\App\Board');
    }
    public function chat(){
        return $this->hasOne('\App\Chat');
    }

}
