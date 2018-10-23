<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



 
class OthelloController extends Controller
{
 
    private $grid = array();
    private $posValidas = array();  // arreglo para almacenar las casillas para marcar como validas
    private $playerI  = 0;
    private $playerII = 0; 



    public function crearMatriz(Request $request){

        $fila = $request->tamaño; 
        $columna = $request->tamaño;
     

        $elemento = array();
        $matriz = array(); 


        for( $i=0; $i< $fila ;$i++) {
	
            for( $j=0; $j< $columna; $j++) {
		
                if (  (($fila/2)-1) == $i and (($columna/2)-1) == $j):

                     array_push($elemento,1);

                elseif (  (($fila/2)-1) == $i and (($columna/2)) == $j):

                    array_push($elemento,2);

                elseif (  (($fila/2)) == $i and (($columna/2)-1) == $j):

                    array_push($elemento,2);

                elseif (  (($fila/2)) == $i and (($columna/2)) == $j):

                    array_push($elemento,1);

                else:

                    array_push($elemento,0);
        
                endif;


            }
            array_push($matriz,$elemento);
            $elemento = array();
            
        } 
        
        return $matriz;
  

    }


    // Logica one vs one -----------------------------------------------------------------------------------------------------------------------

    //funcion: coloca las fichas en la matriz
    public function selectCell(Request $request) {
        
        $matriz = $request->grid;
        $row = $request->row;
        $col = $request->col;
        $actualRow = $request->row;  // variables para conservar los valores iniciales
        $actualRow = $request->row;  // del request 
        $player = $request->player;


        $this->grid = $matriz;


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
        $this->diagonal($row, $col, $player);  // XD
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
                if ($this->grid[$row + 1][$col]!=0) {
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
                        --$col;  // hasta el origen inicial
                    }
                    break;
            } 
            ++$col;
        } // fin while player 1
    }}  // fin logica horizontal derecha

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
//////////////////////////////////////////////////////////////////////////////////////////////
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
    //////////////////////////////////////////////////////////////////////////////////////////////
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
    //////////////////////////////////////////////////////////////////////////////////////////////
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
