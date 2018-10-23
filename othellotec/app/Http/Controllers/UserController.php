<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
class UserController extends Controller
{
    public function profile(){
        $user = Auth::user();
        $sessions = $user->sessions;
        $perdidas = 0;
        $ganes = 0;

        foreach($sessions as $session){
            $player1 = $session->users()->where('id',$user->id)->first();
            $player2 = $session->users()->where('id','!=',$user->id)->first();
            if($player1->pivot->score > $player2->pivot->score){
                $ganes +=1;
            }else{
                $perdidas +=1;
            }
        }
      
        return view('profile')->with('user',$user)->with('sessions',$sessions)->with('ganes',$ganes)->with('perdidas',$perdidas);

    }
}
