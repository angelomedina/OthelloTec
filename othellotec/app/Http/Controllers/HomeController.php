<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $sessions    = \App\Session::where('state','disponible')->get();
        $games       = \App\Session::where('state','reservada')->get();
        $boards       = \App\Board::where('size', '>=', 4)->get();
        $data = [
            'sessions'    => $sessions,
            'boards'      => $boards,
            'games'       => $games
        ];        
        return view('home')->with($data);
    }
}
