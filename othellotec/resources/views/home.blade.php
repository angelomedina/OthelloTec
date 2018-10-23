@extends('layouts.app')
 

@section('content')

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
    
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>  
      <div class="modal-body">
       <button type="button" class="btn btn-dangerous"  data-dismiss="modal">Cancelar</button>&nbsp&nbsp
        <button type="button" class="btn btn-success" onclick="setTipoJuego(0)" >Multiplayer</button> &nbsp&nbsp
        
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">Jugador Automatico
        </button>
            <div class="dropdown-menu">
                <button class="dropdown-item" onclick="setTipoJuego(11)">Fácil</button>
                <button class="dropdown-item" onclick="setTipoJuego(12)">Medio</button>
                <button class="dropdown-item" onclick="setTipoJuego(13)">Dificil</button>
            </div>
      </div> 

    </div> 
  </div>
</div>
<!-- Modal -->




<div class="container">
    <div class="row">
        <nav class="navbar navbar-light bg-dark" style="width:100%">
            <span style="color:white"  class="h2">Bienvenido, {{Auth::user()->username}}</span>
        </nav>
        <div  style="text-align:center; width:100%; background-color:#ffffff;">
                
                <button type="button" style="margin-top:10%;width:150px;height:100px;font-size:20px"data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-success">Crear Partida</button><br><br><br>    
            
        </div>
    </div>
    <div class="row" style="text-align:center; width:100%; background-color:#f4f4f4;">

            <div class="col-md-4"><br><br>
                <span style="color:black"  class="h2">Sessiones disponibles</span><br><br>
                <div class="row" style="margin:5px">
                    @foreach($boards as $board)
                        @foreach($sessions as $session)
                            @foreach($session->users as $user)
                                @if($session->id == $board->session_id)
                                    @if( $user->username != Auth::user()->username)
                                                
                                                <div class="col-md-4">
                                                    {{$user->username}}
                                                </div>
                                                <div class="col-md-4">
                                                    {{$board->size}} x {{$board->size}}
                                                </div>
                                                <div class="col-md-4">  
                                                    <div class="btn btn-info" onclick="redireccionar({{$board->id}},{{$session->id}},{{$board->size}})">Unirse</div> 
                                                </div><br><br>
                                    @endif
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                </div>
            </div>  
            
            <div class="col-md-8"><br><br>
                <span style="color:black"  class="h2">Jugando</span><br><br>
                <div class="row" style="margin:5px">
                @foreach($boards as $board)
                    @foreach($games as $game)
                        @foreach($game->users as $user)
                            @if($game->id == $board->session_id)
                                    @if($user->username == Auth::user()->username)

                                        @foreach($game->users as $retadores)
                                            @if($retadores->username != Auth::user()->username)
                                                <div class="col-md-4">
                                                    {{Auth::user()->username}} vs {{$retadores->username}}
                                                </div>
                                                <div class="col-md-4"> 
                                                    {{$board->size}} x {{$board->size}}
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="btn btn-warning" onclick="play({{$board->id}},{{$game->id}},{{$board->size}},{{Auth::user()->id}})">Play</div> 
                                                </div><br><br><br>
                                                @endif
                                        @endforeach
                                    @endif      
                                @endif
                        @endforeach
                    @endforeach
                @endforeach
            </div>
        </div> 
    </div> 
</div>
@endsection

