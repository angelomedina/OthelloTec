<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function mensaje(Request $request){

        $session            = \App\Session::find($request->session_id);
        $chat               = $session->chat()->where('session_id',$request->session_id)->first();

        $user = Auth::user();

        $message = new \App\Message;
        $message->chat_id = $chat->id;
        $message->message = $request->message;
        $message->user_id = $user->id; 
        $message->save();

    }

    public function arraySMS(Request $request){
        
        $chat  = \App\Chat::where('session_id', $request->session_id)->first();
        $message = \App\Message::where('chat_id', $chat->id)->get();
        return $message;
    }
    
}



