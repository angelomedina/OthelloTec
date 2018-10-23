@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 profile-left-content" >
            <div class="row profile-top-container">
                <div class="profile-picture-container">

                    @if(Auth::user()->avatar)  <!--Verifica que posea una imagen de perfil registrada-->
                    <img class="img-responsive profile-picture" src="{{Auth::user()->avatar}}" alt="" >
                    @else  <!--Sino le coloca una por defecto-->
                    <img class="img-responsive profile-picture" src="https://vignette.wikia.nocookie.net/k-project/images/6/66/Anonimo.png/revision/latest/scale-to-width-down/522?cb=20170918014043&path-prefix=es" alt="" >
                    @endif 
                    
                    <img class="picture-badge" src="https://cdn0.iconfinder.com/data/icons/gamification-flat-awards-and-badges/500/cup1-512.png" alt="">
                </div>
                <div class="profile-top-info-container">
                    
                    <h2><i class="fas fa-user-astronaut"></i> {{$user->username}}</h2>
                    <h3><i class="fas fa-headset"></i> {{$user->score}}</h3>
                    

                </div>
            </div>
            <hr> 
            <div class="profile-user-feed">
            @foreach($sessions as $session)
                <div class="row feed-element">
                        
                        <div>
                            <img class="img-responsive feed-image" src="https://www.shareicon.net/data/512x512/2017/03/29/881750_sport_512x512.png" alt="" >
                        </div>
                        
                        <div class="feed-element-info">
                         

                         {{$session->users->first()->username}} ha jugado una partida con {{$session->users->last()->username}}.
                          

                        </div>
                        

                </div>
            

             @endforeach
            </div>

        </div>

        <div class="col-md-4 profile-right-content">
            
            <h2>Estadísticas</h2>
            <hr>
            <ul>
                <li>Partidas juguadas: {{$sessions->count()}}</li>
                <li>Ganes: {{$ganes}}</li>
                <li>Perdidas: {{$perdidas}}</li>
            </ul>
        </div>
    </div>
</div>
<div class="footer" style="height:300px">

</div>
@endsection


