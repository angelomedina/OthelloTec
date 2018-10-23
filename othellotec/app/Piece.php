<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    protected $table = 'pieces';

    protected $fillable= ['color','x','y'];


   

    public function board(){
        return $this->belongsTo('App\Board');
    }

   
}
