<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table = 'boards';
    protected $fillable = ['session_id','size','color'];

    public function pieces(){

        $this->hasMany('\App\Piece');
    }
    public function session(){
        return $this->belongsTo('\App\Session');
    }
}
