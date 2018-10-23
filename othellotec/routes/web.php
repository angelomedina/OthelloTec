<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| 
*/
 
Auth::routes();
 
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');


Route::get('/profile','UserController@profile');


// Juego  maatriz general
Route::get('/othelloboard', function(){return view('othelloboard');});
Route::get('/othelloboard/crearMatriz', 'OthelloController@crearMatriz');
Route::get('/othelloboard/selectCell', 'OthelloController@selectCell');
Route::get('/othelloboard/ganador', 'OthelloController@ganador');

// Jugador automatico
Route::get('/automatic/posiblesJugadas', 'AutomaticPlayerController@posblesJugada');
Route::get('/automatic/evaluarJugada', 'AutomaticPlayerController@evaluarJugada');


//sesiones
Route::get('/session',      function(){return view('session');});
Route::get('/sessionboard', function(){return view('sessionboard');});
Route::get('/session/crearSession', 'SessionController@crearSession');
Route::get('/session/obtenerColorPiece', 'SessionController@obtenerColorPiece');
Route::get('/session/unirmeSession', 'SessionController@unirmeSession');
Route::get('/session/arrayPieces', 'SessionController@arrayPieces');
Route::get('/session/selectCell', 'SessionController@selectCell');
Route::get('/session/crearPieces', 'SessionController@crearPieces');
Route::get('/session/userID', 'SessionController@userID');
Route::get('/session/scoreSession', 'SessionController@scoreSession');
Route::get('/session/ganador', 'SessionController@ganador');
Route::get('/session/finalizar', 'SessionController@finalizar');
Route::get('/session/score', 'SessionController@score');
Route::get('/session/background', 'SessionController@background');

//chat
Route::get('/chat/mensaje', 'ChatController@mensaje');
Route::get('/chat/arraySMS', 'ChatController@arraySMS');


Route::get('/join',      function(){return view('join');});

// autenticacion redes sociales
Route::get('/redirect/{provider}', 'socialController@redirect');  // request de peticion de login
Route::get('/callback/{provider}', 'socialController@callback'); // respuesta de peticion

Route::get('/logout', 'socialController@logout');  // Cerrar sesión 