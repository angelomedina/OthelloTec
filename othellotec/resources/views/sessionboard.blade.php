@extends('layouts.app')
@section('content')

<!-- esperando jugador-->
<div id="esperando_session" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog"> 
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Esperando Jugador  </h4>
            </div>
            <div class="modal-body">
                
                <br><br><br>
                    <div class="wait_session"></div>    
                    <div class="spinner">
                </div><br><br><br>

            </div>
        </div>
    </div>
</div>

<div class="container" id="nav_session" style="display:none">
    <div class="row">
        <nav class="navbar navbar-light bg-light" style="width:100%">

           <h6 class="turno_player_session" id="turno_player_session"></h6> 
           
            <h6 class="playerI_username" id="playerI_username"></h6> <h6 class="playerI_label">: 0</h6> 
            VS
            <h6 class="playerII_username" id="playerII_username"></h6> <h6 class="playerII_label">: 0</h6> 

            <div class="col-xs-6 col-sm-3 " id='menu'>
                    <!-- Trigger the modal with a button -->            
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-cog"></span>Menu</button>    
                                <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog">
                        
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Configuración</h4>
                            </div>
                            <div class="modal-body">
                            <p>Abandonar: salir de la partida.</p>
                            <p>Continuar: seguir jugando.</p>
                            </div>
                            <div class="modal-footer">
                                <a name="" id="" class="btn btn-dark" href="/home" role="button"> Abandonar</a>
                                <button name="" id="" class="btn btn-dark" data-dismiss="modal"> Continuar</button>
                            </div>
                        </div>
                        
                        </div>
                    </div>
                </div>   
                    </nav>
                </div>
            </div>
        </nav>
    </div>
</div>

<!-- Config y matriz -->
<div class="main" id="main_session">
    <main class="py-4">
        <div class="card-group">
            <div class="container" id="contenido" class="animated fadeIn fast">
                <div class="row justify-content-center">
                    <div class="col-md-8">    
                        <div class="card">
                            <div class="card-header">Othello Game</div> 
                                <div class="card-body">
                                    <div class="form-group row"> 
                                        <label  class="col-sm-4 col-form-label text-md-right">Continuar partida</label>
                                        <div class="col-md-6">
                                            <button  class="btn btn-dark" onclick="iniciar_partida()"> Play </button>
                                        </div>
                                    </div>   
                                </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- chat -->
<div class="wrapper" style="display:none" id="chat">
	<div class="chat-box">
		<div class="chat-head">
            <h2>Chat</h2>
			<img src="https://maxcdn.icons8.com/windows10/PNG/16/Arrows/angle_down-16.png"  onClick='slide_chat()' title="Expand Arrow" width="10">
		</div>
		<div class="chat-body">
			<div class="msg-insert" id="msg-insert">
            </div>
			<div class="chat-text">
				<textarea placeholder="Send message" id="txt" onkeypress="process(event,this)"></textarea>
			</div>
		</div>
	</div>
</div>
        
@endsection

