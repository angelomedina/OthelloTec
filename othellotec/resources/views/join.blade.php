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
                                    <div class="card-header">Unirme a uno vs uno</div> 
                                        <div class="card-body">
                                            
                                            
                                            <div class="form-group row"> 
                                                <label  class="col-sm-4 col-form-label text-md-right">Ficha II: {{Auth::user()->username}}</label>
                                                    <div class="col-md-6">

                                                        <div class="input-color-container">
                                                            <input id="TokenColorII" value="#202360" class="input-color" type="color">
                                                        </div>
                    
                                                    </div>
                                            </div>   

                                            <div class="form-group row mb-0"> 
                                                <div class="col-md-8 offset-md-4">
                                                    <button  class="btn btn-dark" onclick="recuperar_datos()"> Play </button>
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

