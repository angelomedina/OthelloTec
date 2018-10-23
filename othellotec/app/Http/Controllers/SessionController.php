<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionController extends Controller
{
    private $grid        = array();
    private $posValidas  = array();  // arreglo para almacenar las casillas para marcar como validas
    private $playerI     = 0;
    private $playerII    = 0; 
    private $board_id    = 0;
    private $user_id     = 0;
    private $session_id  = 0;
    private $piece_color = "";
 
    public function background(Request $request){

        $board  = \App\Board::where('session_id', $request->session_id)->first();
        return $board->color;
    }



    public function finalizar(Request $request){

        //cambiar estado a una session ya creada
        $session = \App\Session::find($request->session_id);
        $session->state = "finalizada";
        $session->save();
    }

    public function scoreSession(Request $request){

        $user     = Auth::user();
        $session = \App\UserSession::where(['session_id' => $request->session_id,'user_id' => $user->id])->get();
        return $session;
    }

    public function score(Request $request){

        $session            = \App\Session::find($request->session_id);
        $user               = $session->users()->where('user_id',$request->user_id)->first();
        $user->pivot->score = $request->puntos;
        $user->pivot->save();
    
    }

    public function userID(Request $request){

        $session = \App\User::find($request->user_id);
        return $session;
    }

    public function crearSession(Request $request){

        $piece_color = $request->piece_color;
        $size        = $request->size;
        $background  = $request->background;

        $user = Auth::user();
        
        //creo una session
        $session = new \App\Session;
        $session->state = 'disponible';
        $session->current_player_id = $user->id;
        $session->save();
        $user->sessions()->attach($session->id,['score'=>0,'piece_color'=>$piece_color]);
        
        //creo board
        $board = new \App\Board;
        $board->size  =  $size ;
        $board->color =  $background;
        $board->session_id = $session->id;
        $board->save();

        //creo el chat vinculado a una session
        
        $chat = new \App\Chat;
        $chat->session_id = $session->id;
        $chat->save();
        
    }

    public function obtenerColorPiece(Request $request){

        $session_id = $request->session_id;

        $userSession = \App\UserSession::where('session_id', $session_id)->get();

        return $userSession;
        
    }

    public function unirmeSession(Request $request){

        //datos iniciales de la partida
        $inicial_x1 = $request->inicial_x1;
        $inicial_y1 = $request->inicial_y1;
        $inicial_x4 = $request->inicial_x4;
        $inicial_y4 = $request->inicial_y4;
        $piece_color1 = $request->piece_color1;

        $inicial_x2 = $request->inicial_x2;
        $inicial_y2 = $request->inicial_y2;
        $inicial_x3 = $request->inicial_x3;
        $inicial_y3 = $request->inicial_y3;
        $piece_color2 = $request->piece_color2;

        //datos de la session y el board
        $board_id = $request->board_id;
        $session_id = $request->session_id;

    
        //me uno a una sessión
        $user = Auth::user();
        $user->sessions()->attach($session_id,['score'=>0,'piece_color'=>$piece_color2]);

        //creo piezas iniciales asocciadas a un board
        $piece1           = new \App\Piece;
        $piece1->color    = $piece_color1;
        $piece1->x        = $inicial_x1;
        $piece1->y        = $inicial_y1;
        $piece1->board_id = $board_id;
        $piece1->save();

        $piece2           = new \App\Piece;
        $piece2->color    = $piece_color1;
        $piece2->x        = $inicial_x4;
        $piece2->y        = $inicial_y4;
        $piece2->board_id = $board_id;
        $piece2->save();

        $piece3           = new \App\Piece;
        $piece3->color    = $piece_color2;
        $piece3->x        = $inicial_x2;
        $piece3->y        = $inicial_y2;
        $piece3->board_id = $board_id;
        $piece3->save();

        $piece4           = new \App\Piece;
        $piece4->color    = $piece_color2;
        $piece4->x        = $inicial_x3;
        $piece4->y        = $inicial_y3;
        $piece4->board_id = $board_id;
        $piece4->save();
        

        //cambiar estado a una session ya creada
        $session = \App\Session::find($session_id);
        $session->state = "reservada";
        $session->save();
    }

    public function arrayPieces(Request $request){

        $board_id    = $request->board_id;
        $pieces      = \App\Piece::where('board_id', $board_id)->get();
        return $pieces;
    }


    public function crearPiece($x,$y){

        $piece            = new \App\Piece;
        $piece->color     = $this->piece_color;
        $piece->x         = $x;
        $piece->y         = $y;
        $piece->board_id  = $this->board_id;
        $piece->save();
    }

    public function crearPieces(Request $request){

        $piece            = new \App\Piece;
        $piece->color     = $request->piece_color;
        $piece->x         = $request->x;
        $piece->y         = $request->y;
        $piece->board_id  = $request->board_id;
        $piece->save();
    }
    // Logica one vs one -----------------------------------------------------------------------------------------------------------------------

    //funcion: coloca las fichas en la matriz
    public function selectCell(Request $request) {
        
        //data: { 'grid': matriz, 'row': x, 'col': y, 'player': jugador, 'board_id': board_id, 'piece_color': piece_color },

        $matriz = $request->grid;
        $row = $request->row;
        $col = $request->col;
        $actualRow = $request->row;  // variables para conservar los valores iniciales
        $actualRow = $request->row;  // del request 
        $player = $request->player;


        $this->grid        = $matriz;
        $this->board_id    = $request->board_id;
        $this->piece_color = $request->piece_color;
        $this->userID      = $request->user_id;
        $this->session_id  = $request->session_id;



        //validacion verifica que el moviemotno en filas y columnas en valido
        $valicion = $this->invalido($row, $col);

    
        //si la fila, columna esta vacia coloca el valor dej jugador
        if ($valicion == TRUE ) { 

            if (($player == 1) && ($this->grid[$row][$col] == 0)) {

                $this->grid[$row][$col] = 1;
                $this->isValid($row, $col, $player);


                $data = ['grid' => $this->grid , 'player' => $player , 'mensaje' => "OK"];
                return $data;

            } elseif (($player == 2) && ($this->grid[$row][$col] == 0)) {

                $this->grid[$row][$col] = 2;
                $this->isValid($row, $col, $player);


                $data = ['grid' => $this->grid , 'player' => $player , 'mensaje' => "OK"];
                return $data;
            }

        } 
        else{

                $data = ['grid' => $this->grid , 'player' => $player , 'mensaje' => "Error"];
                return $data;
        }
    
    }

    //funcion: envia a revision las 4 posibilidades de movimiento
    public function isValid($row,$col, $player) {

        $this->horizontal($row, $col, $player);
        $this->vertical($row, $col, $player);
        $this->diagonal($row, $col, $player);
        
    }

    ////funcion: verifica las 4 posibilidades de movimiento
    public function invalido($row, $col) {

        $lenG = sizeof($this->grid); // de abajo hacia arriba
    
        //de abajo hacia arriba
        if ($row >= 0) {
            if ($row - 1 >= 0) {
                if ($this->grid[$row - 1][$col] != 0) {
                    return TRUE;
                }
            }
        }
    
        //de arriba hacia abajo
        if ($row <= $lenG - 1) {
            if ($row + 1 <= $lenG - 1) {
                if ($this->grid[$row + 1][$col]) {
                    return TRUE;
                }
            }
        }
    
        //de izquierda hacia derecha
        if ($col <= $lenG - 1) {
            if ($col + 1 <= $lenG - 1) {
                if ($this->grid[$row][$col + 1] != 0) {
                    return TRUE;
                }
            }
        }
    
        //de derecha hacia izquierda
        if ($col >= 0) {
            if ($col - 1 >= 0) {
                if ($this->grid[$row][$col - 1] != 0) {
                    return TRUE;
                }
            }
        }
    
        //de diagonal hacia abajo: izquierda --> abajo
        if ($row <= $lenG-1 && $col  <= $lenG-1){
            if ($row + 1 <= $lenG-1 && $col + 1 <= $lenG-1){
                if ($this->grid[$row + 1][$col + 1] != 0){
                    return TRUE;
                }
            }
        }
        //de diagonal abajo hacia arriba: izquierda --> arriba
        if ($row >= 0 && $col <= $lenG - 1){
            if ($row - 1 >=  0 && $col + 1 <= $lenG - 1){
                if ($this->grid[$row - 1][$col + 1] != 0){
                    return TRUE;
                }
            }
        }
        //de diagonal arriba hacia abajo: derecha --> abajo
        if ($row <= $lenG-1 && $col >= 0){
                if ($row + 1 <= $lenG-1 && $col - 1 >= 0){
                    if ($this->grid[$row + 1][$col - 1] != 0){
                        return TRUE;
                    }
                }
        }
        //de diagonal hacia arriba: derecha --> arriba
        if($row >= 0 && $col >= 0){
            if($row-1 >= 0 && $col-1 >= 0){
                if($this->grid[$row-1][$col-1] != 0){
                    return TRUE;
                }
            }
        }

        else{
            return FALSE;
        }

        
    }


    //verifica las jugadas horizontales
    public function horizontal($row, $col, $player) {

        $lenG = sizeof($this->grid); // tamaño de  la matriz
        $count=0; // contador d efichas a pintar 
        $inicioCol = $col; // valores de reserva
        $inicioRow = $row; // de columna y fila original


        // derecha
        if ($row <= $lenG - 1 && $col <= $lenG - 1) {  // si no se sale del tamaño de matriz

            if ($player == 1) {
                    // derecha
                    while($col +1 <= $lenG - 1){
                        if ($this->grid[$row][$col + 1] == 2) { // si la ficha derecha  es del rival
                            ++$count; // incrementa contador
                        }
                        if ($this->grid[$row][$col + 1] == 1  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                            while( $col > $inicioCol){   // retrocede hacia atras para marcar el ersto de fichas 
                                $this->grid[$row][$col] = 1; 
                                $this->crearPiece($row,$col);
                                //$this->score(10);
                                --$col;  // hasta el origen inicial
                            }
                            break;
                    } 
                    ++$col;
                } // fin while player 1
            }


            if ($player == 2) {
                    // derecha
                    while($col +1 <= $lenG - 1){
                        if ($this->grid[$row][$col + 1] == 1) { // si la ficha derecha  es del rival
                            ++$count; // incrementa contador
                        }
                        if ($this->grid[$row][$col + 1] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                            while( $col > $inicioCol){   // retrocede hacia atras para marcar el resto de fichas 
                                $this->grid[$row][$col] = 2; 
                                $this->crearPiece($row,$col);
                                //$this->score(10);
                                --$col;  // hasta el origen inicial
                            }
                            break;
                        } 
                        ++$col;
                    } // fin while player 1
                }
            }  // fin logica horizontal derecha

            $col= $inicioCol; // setaer variables originales
            $row = $inicioRow; // de col y row
            $count =0; // reseteo de contador
            // izquierda
            if ($row >= 0 && $col >= 0) {  // si no se sale del tamaño de matriz

                if ($player == 1) {
                    // derecha
                    while($col - 1 >= 0){
                        if ($this->grid[$row][$col -1] == 2) { // si la ficha derecha  es del rival
                            ++$count; // incrementa contador
                        }
                        if ($this->grid[$row][$col -1] == 1  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                            while( $col < $inicioCol){   // retrocede hacia atras para marcar el resto de fichas 
                                $this->grid[$row][$col] = 1; 
                                $this->crearPiece($row,$col);
                                //$this->score(10);
                                ++$col;  // hasta el origen inicial
                            }
                            break;
                    } 
                    --$col;
                } // fin while player 1
            }

            if ($player == 2) {
                // derecha
                while($col - 1 >= 0){
                    if ($this->grid[$row][$col -1] == 1) { // si la ficha derecha  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($this->grid[$row][$col -1] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $col < $inicioCol){   // retrocede hacia atras para marcar el resto de fichas 
                            $this->grid[$row][$col] = 2; 
                            $this->crearPiece($row,$col);
                            //$this->score(10);
                            ++$col;  // hasta el origen inicial
                        }
                        break;
                    } 
                    --$col;
                } // fin while player 2
            }

        }  // fin logica horizontal izquierda
    }


    //////////////////////////////////////////////////////////////////////////////////////////////
    //verifica las jugadas verticales
    public function vertical($row, $col, $player) {

        $lenG = sizeof($this->grid); // tamaño de  la matriz
        $count=0; // contador d efichas a pintar 
        $inicioCol = $col; // valores de reserva
        $inicioRow = $row; // de columna y fila original

        //ARRIBA
        if ($row >= 0 && $col >= 0) {  // no se salga de matriz tamaño

            if ($player ==1){
                while($row-1 >=0){
                    if ($this->grid[$row-1][$col] == 2) { // si la ficha arriba  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($this->grid[$row-1][$col] == 1  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $row < $inicioRow){   // retrocede hacia atras para marcar el resto de fichas 
                            $this->grid[$row][$col] = 1; 
                            $this->crearPiece($row,$col);
                            //$this->score(10);
                            ++$row;  // hasta el origen inicial
                        }
                        break; //x
                    }
                    --$row;
                }

            }
            if ($player ==2){
                while($row-1 >=0){
                    if ($this->grid[$row-1][$col] == 1) { // si la ficha arriba  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($this->grid[$row-1][$col] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $row < $inicioRow){   // retrocede hacia atras para marcar el resto de fichas 
                            $this->grid[$row][$col] = 2; 
                            $this->crearPiece($row,$col);
                            //$this->score(10);
                            ++$row;  // hasta el origen inicial
                        }
                        break;
                    }
                    --$row;
                }

            }

        }
        $col= $inicioCol; // setaer variables originales
        $row = $inicioRow; // de col y row
        $count =0; // reseteo de contador

        //AABAJO
        if ($row <= $lenG - 1 && $col <= $lenG - 1) {  // no se salga de matriz tamaño

            if ($player ==1){
                while($row + 1 <= $lenG - 1){

                    if ($this->grid[$row+1][$col] == 2) { // si la ficha arriba  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($this->grid[$row+1][$col] == 1  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $row > $inicioRow){   // retrocede hacia atras para marcar el resto de fichas 
                            $this->grid[$row][$col] = 1; 
                            $this->crearPiece($row,$col);
                            //$this->score(10);
                            --$row;  // hasta el origen inicial
                        }
                        break; //x
                    }
                    ++$row;
                }

            }
            if ($player ==2){
                while($row + 1 <= $lenG - 1){

                    if ($this->grid[$row+1][$col] == 1) { // si la ficha arriba  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($this->grid[$row+1][$col] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $row > $inicioRow){   // retrocede hacia atras para marcar el resto de fichas 
                            $this->grid[$row][$col] = 2; 
                            $this->crearPiece($row,$col);
                            //$this->score(10);
                            --$row;  // hasta el origen inicial
                        }
                        break; //x
                    }
                    ++$row;
                }

            }

        }

    }


    //////////////////////////////////////////////////////////////////////////////////////////////

    //verifica las jugadas diagonales
    public function diagonal($row,$col,$player){

        $lenG = sizeof($this->grid); // tamaño de  la matriz
        $count=0; // contador d efichas a pintar 
        $inicioCol = $col; // valores de reserva
        $inicioRow = $row; // de columna y fila original

        if ($player == 1){
            #upDer
            while ($row-1 >=0 && $col+1 <= $lenG-1){
                if($this->grid[$row-1][$col+1] == 2){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row-1][$col+1] == 1 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col >$inicioCol && $row <$inicioRow ){
                        $this->grid[$row][$col] = 1; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        --$col;
                        ++$row;
                    }
                    break;
                }
                --$row;
                ++$col;
            }

            $count=0; // contador d efichas a pintar 
            $col=$inicioCol; // valores de reserva
            $row=$inicioRow; // de columna y fila original
            #upIzq
            while ($row-1 >=0 && $col-1 >= 0){
                if($this->grid[$row-1][$col-1] == 2){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row-1][$col-1] == 1 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col < $inicioCol && $row <$inicioRow ){
                        $this->grid[$row][$col] = 1; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        ++$col;
                        ++$row;
                    }
                    break;
                }
                --$row;
                --$col;
            }

            $count=0; // contador de fichas a pintar 
            $col=$inicioCol; // valores de reserva
            $row=$inicioRow; // de columna y fila original
            #dowDer
            while ($row+1 <= $lenG-1 && $col+1<= $lenG-1){
                if($this->grid[$row+1][$col+1] == 2){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row+1][$col+1] == 1 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col > $inicioCol && $row >$inicioRow ){
                        $this->grid[$row][$col] = 1; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        --$col;
                        --$row;
                    }
                    break;
                }
                ++$row;
                ++$col;
            }

            $count=0; // contador de fichas a pintar 
            $col=$inicioCol; // valores de reserva
            $row=$inicioRow; // de columna y fila original
            #dowIzq
            while ($row+1 <= $lenG-1 && $col-1>= 0){
                if($this->grid[$row+1][$col-1] == 2){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row+1][$col-1] == 1 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col < $inicioCol && $row >$inicioRow ){
                        $this->grid[$row][$col] = 1; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        ++$col;
                        --$row;
                    }
                    break;
                }
                ++$row;
                --$col;
            }

        }
        if ($player == 2){
            #upDer
            while ($row-1 >=0 && $col+1 <= $lenG-1){
                if($this->grid[$row-1][$col+1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row-1][$col+1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col >$inicioCol && $row <$inicioRow ){
                        $this->grid[$row][$col] = 2;
                        $this->crearPiece($row,$col); 
                        //$this->score(10);
                        --$col;
                        ++$row;
                    }
                    break;
                }
                --$row;
                ++$col;
            }
            $count=0; // contador d efichas a pintar 
            $col=$inicioCol; // valores de reserva
            $row=$inicioRow; // de columna y fila original
            #upIzq
            while ($row-1 >=0 && $col-1 >= 0){
                if($this->grid[$row-1][$col-1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row-1][$col-1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col < $inicioCol && $row <$inicioRow ){
                        $this->grid[$row][$col] = 2; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        ++$col;
                        ++$row;
                    }
                    break;
                }
                --$row;
                --$col;
            }
            $count=0; // contador de fichas a pintar 
            $col=$inicioCol; // valores de reserva
            $row=$inicioRow; // de columna y fila original
            #dowDer
            while ($row+1 <= $lenG-1 && $col+1<= $lenG-1){
                if($this->grid[$row+1][$col+1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row+1][$col+1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col > $inicioCol && $row >$inicioRow ){
                        $this->grid[$row][$col] = 2; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        --$col;
                        --$row;
                    }
                    break;
                }
                ++$row;
                ++$col;
            }
            $count=0; // contador de fichas a pintar 
            $col=$inicioCol; // valores de reserva
            $row=$inicioRow; // de columna y fila original
            #dowIzq

            
            while ($row+1 <= $lenG-1 && $col-1>= 0){
                if($this->grid[$row+1][$col-1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($this->grid[$row+1][$col-1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $col < $inicioCol && $row >$inicioRow ){
                        $this->grid[$row][$col] = 2; 
                        $this->crearPiece($row,$col);
                        //$this->score(10);
                        ++$col;
                        --$row;
                    }
                    break;
                }
                ++$row;
                --$col;
            }
        }                        
    }

    //////////////////////////////////////////////////////////////////////////////////////////////

    //verifico si la matriz esta llena
    public function matrizLLena($grid){

        $lenG = sizeof($grid);

        for( $i=0; $i< $lenG ;$i++) {

            for( $j=0; $j< $lenG ;$j++) {

                if ($grid[$i][$j]==0){

                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    //cuento los puntos de la matriz
    public function contadorPuntos($grid){

        $lenG = sizeof($grid);

        for( $i=0; $i< $lenG ;$i++) {

            for( $j=0; $j< $lenG ;$j++) {

                if ($grid[$i][$j] == 1){  $this->playerI  = $this->playerI+1;}
                if ($grid[$i][$j] == 2){  $this->playerII = $this->playerII+1;}
            }
        }
    }

    //metodo que clacula cual fue el ganador de la partida
    public function ganador(Request $request) {

        $grid = $request->grid;
        $this->contadorPuntos($grid);
        $llena =  $this->matrizLLena($grid);

        if($llena == TRUE){

    
            if( ($this->playerI) > ($this->playerII) ){

                $data = ['playerI' => $this->playerI , 'playerII' =>$this->playerII,'ganador' => 'playerI' ];
                return $data;
            }

            if( ($this->playerII) > ($this->playerI) ){

                $data = ['playerI' => $this->playerI , 'playerII' =>$this->playerII,'ganador' => 'playerII' ];
                return $data;
            }

            if( ($this->playerII) == ($this->playerI)){

                $data = ['playerI' => $this->playerI , 'playerII' =>$this->playerII,'ganador' => 'empate' ];
                return $data;
            }

        }else{
            
            $data = ['playerI' => $this->playerI , 'playerII' =>$this->playerII,'ganador' => 'continua' ];
            return $data;
        }
            
    }
    
}
