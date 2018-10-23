@extends('layouts.app')


@section('content')

        <!-- esperando jugador-->
        <div id="esperando_jugador" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">

            <div class="modal-dialog"> 
                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Esperando Jugador  </h4>
                </div>
                <div class="modal-body">
                    
                    <br><br><br>
                        <div class="wait_timer"></div>    
                        <div class="spinner">
                    </div><br><br><br>

                </div>
                </div>

            </div>
        </div>

        <!-- Barra lateral-->
        <div class="row"><br><br>

            <div class="col-xs-6 col-sm-3">
            </div>

            <div class="col-xs-6 col-sm-3" id="turno" style="display:none">
                <h5 class="turno_player" id="turno_player">Turno: Player I</h5>   
            </div>

            <div class="col-xs-6 col-sm-3" id='players' style="display:none" >
                <div class="input-group">
                    <h5 class="playerI_label">Player I: 0</h5> 
                        &nbsp &nbsp &nbsp      
                    <h5 class="playerII_label">Player II: 0</h5> 
                </div>
            </div>

            <div class="col-xs-6 col-sm-3 " id='menu' style="display:none">
                <!-- Trigger the modal with a button -->            
                <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-cog"></span>Menu</button>  
                <div id="cover-spin" style="visibility:hidden"></div>  
                            <!-- Modal -->
                <div class="modal fade" id="myModal" role="dialog">
                    <div class="modal-dialog">
                    
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Configuración</h4>
                        </div>
                        <div class="modal-body">
                        <p>Abandonar: salir de la partida y darle los puntos al rival.</p>
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
        </div>

        <!-- Config y matriz -->
        <div class="main">
            <main class="py-4">
            <div class="card-group">
                    <div class="container" id="contenido" class="animated fadeIn fast">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                
                                <div class="card">
                                    <div class="card-header">Configuración</div> 
                                        <div class="card-body">
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Tamaño Matriz</label>
                                                <div class="col-md-6">
                                                    <input id="tamaño" type="number" value="6" name="tamaño" required="required" class="form-control">
                                                </div>
                                            </div>   
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Duración por jugada</label>
                                                    <div class="col-md-6">

                                                        <select class="custom-select" id="time">
                                                            <option value="0.5">30 sec</option>
                                                            <option value="1">1 min</option>
                                                            <option value="2">2 min</option>
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Ficha I: {{Auth::user()->username}}</label>
                                                    <div class="col-md-6">

                                                        <div class="input-color-container">
                                                            <input id="TokenColorI" value="#FF0040" class="input-color" type="color">
                                                        </div>
                    
                                                    </div>
                                            </div>   
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Ficha II</label>
                                                    <div class="col-md-6">

                                                        <div class="input-color-container">
                                                            <input id="TokenColorII" value="#04B404" class="input-color" type="color">
                                                        </div>

                                                    </div>
                                            </div>  
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Color de fondo</label>
                                                    <div class="col-md-6">

                                                        <div class="input-color-container">
                                                            <input id="background" value="#AC58FA" class="input-color" type="color">
                                                        </div>

                                                    </div>
                                            </div> 
                                            <div id="cover-spin" style="visibility:hidden"></div>
                                            <div class="form-group row mb-0"> 
                                                <div class="col-md-8 offset-md-4">
                                                    <button  class="btn btn-dark" onclick="carga_datos()"> Play </button>
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

@endsection

