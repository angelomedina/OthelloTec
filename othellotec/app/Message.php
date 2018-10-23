<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = ['chat_id','message','user_id'];

    public function chat(){
       return $this->belongsTo('\App\Chat');
    }
}
