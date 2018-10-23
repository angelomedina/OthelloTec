<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $fillable = ['session_id'];

    public function messages(){
        return $this->hasMany('\App\Message');
    }
    public function session(){
        return $this->belongsTo('\App\Session');
    }
}
