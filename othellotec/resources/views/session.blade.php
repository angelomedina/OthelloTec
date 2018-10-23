@extends('layouts.app')


@section('content')

        <!-- Config y matriz -->
        <div class="main">
            <main class="py-4">
            <div class="card-group">
                    <div class="container" id="contenido" class="animated fadeIn fast">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                
                                <div class="card">
                                    <div class="card-header">Configuración de sesión</div> 
                                        <div class="card-body">
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Tamaño Matriz</label>
                                                <div class="col-md-6">
                                                    <input id="tamaño" type="number" value="8" name="tamaño" required="required" class="form-control">
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
                                                            <input id="TokenColorI" value="#ed6868" class="input-color" type="color">
                                                        </div>
                    
                                                    </div>
                                            </div>   
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Color de fondo</label>
                                                    <div class="col-md-6">

                                                        <div class="input-color-container">
                                                            <input id="background" value="#F5EEF8" class="input-color" type="color">
                                                        </div>

                                                    </div>
                                            </div> 
                                            <div class="form-group row mb-0"> 
                                                <div class="col-md-8 offset-md-4">
                                                    <button  class="btn btn-dark" onclick="crear_session()"> Crear sesión </button>
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

