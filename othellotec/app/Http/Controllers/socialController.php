<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\User;
use AuthenticatesUsers;
use Auth;



class socialController extends Controller 
{
    public function redirect ($provider){
        return Socialite:: driver($provider) ->redirect();
        // redigue al servicio de autentificacion del provvedor (Facebook o Google)
    }
 
    public function callback($provider){
       
        $social_user = Socialite::driver($provider)->user();
        // guarda la info obtenida del login

        /////////// Si ya se logueo anteriormente
        if ($user = User::where('email', $social_user->email)->first()) { 
            return $this->authAndRedirect($user); // Login y redirección
        }
         else {  
            // En caso de que no exista creamos un nuevo usuario con sus datos.
            $user = User::create([
                'username' => $social_user->name,
                'email' => $social_user->email,
                'avatar' => $social_user->avatar,
                'provider' =>$provider,
                'provider_id'=>$social_user ->id

            ]);
     // com
            return $this->authAndRedirect($user); // Login y redirección  
    
    }

  } 

   // Login y redirección
   public function authAndRedirect($user)
   {
       Auth::login($user);
      // echo $user;
       return redirect()->route('home');
       //eturn view('home') ->with('name', $user-> username )->with('img', $user -> avatar ); 
  }


  // realizar el logout y redireccion  a pantalla de login
  public function logout(Request $request) {
    Auth::logout();
    return redirect('/home');
  }



} 