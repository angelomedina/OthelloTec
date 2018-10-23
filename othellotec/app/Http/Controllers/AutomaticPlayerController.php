<?php

namespace App\Http\Controllers; 
  
use Illuminate\Http\Request;

class AutomaticPlayerController extends Controller
{ 

    private $grid = array();  // matriz actual
    private $dir = array();  // matriz actual
 

    public function posblesJugada(Request $request){  // averiguar las celdas disponibles 
        $matriz = $request->grid;
        $this->grid = $matriz;
        $cantidad=0;

        $lenG = sizeof($matriz); // tamaño de la matriz
        $direccionesJugadas= array();  // array para guaradr direcciones de celdas a utilizar

        for( $i=0; $i< $lenG ;$i++) {   // contabiliza la cantidad de espacios vacios en tablero
            for( $j=0; $j< $lenG ;$j++) {
                if ($matriz[$i][$j] == 0){  // si halla espacio vacio
                    $valicion = $this->invalido($i, $j);  // se llama funcion para verificar que la posicion seria valida
                    if ($valicion == TRUE){
                        ++$cantidad;
                        $direc = $i.",".$j;  // crea un string concatendnado el row y col valido //ejemplo 1,3 ... 4,5 ... 5,6
                        array_push($direccionesJugadas, $direc);  // y lo ingresa a aarray
                    }    
                }
            }
        }

        $data = ['grid' => $this->grid, 'mensaje' => "OK",  'direcciones'=>$direccionesJugadas, 'cant'=>$cantidad];
        return $data;
    } 



    public function evaluarJugada(Request $request){  // si una jugada esta permitida o no

        $matriz = $request->grid;
        $this->grid = $matriz;
        $lenG = sizeof($matriz); // tamaño de la matriz
        $dificultad = $request->dif;  // facil, medio o dificil

        $direcciones = $request->direcc;
        $this->dir = $direcciones;



        if ($dificultad == 11){ // si es facil

            $num = rand(0,$request->cant-1);  // sae toma posicion randoom de lista de posiciones posibles
            $posicion = $direcciones[$num]; 
            $coord= explode(",",$posicion); // se hace split con delimitador de coma

            $rowAut= $coord[0];  // row y col 
            $colAut= $coord[1];  // a usar en turno de jugaaodr autoamtico

            $data = ['grid' => $this->grid, 'mensaje' => "OK", 'pos'=>$posicion, 'row'=>$rowAut, 'col'=>$colAut];
            return $data;
        }



        if($dificultad == 12){
           // $num = rand(0,$request->cant-1);  // sae toma posicion randoom de lista de posiciones posibles
            $posicion ="1,3"; 
            $coord= 0; // se hace split con delimitador de coma
  
            $media = $request->cant/2;
            $mediaR = round($media);  // cantidad de posiciones a evaluar (la mitad)

            $maximo=0; // variable para verificar ganacia de usar posicion
            $mejorPosicion="1,3";
 
            $antesPuntos=$this->puntajeAuto($matriz);       
            $n=0;
            $resultado=0;
            $hallado=0;

            
            
            while($n < $mediaR){
                $num = rand(0,$request->cant-1);
                $posicion = $direcciones[$num]; 
                $coord= explode(",",$posicion); // se hace split con delimitador de coma
                $rowU = $coord[0];
                $colU= $coord[1];

                
                $resultado = $this->evaluarJugadaPuntos($coord,$matriz); // se consigue el puntaje obtenido en caso de usar posuicion
               // $matrizResultante = $this->AutoJugada($rowU,$colU,$matriz);
          
                if($resultado >= $maximo){ // si se halla una puntuacion mayor a la actual
                   $maximo= $resultado;   // se guarada junto a la posicion
                    $mejorPosicion= $posicion;
                    $hallado=$n;
                }
                ++$hallado;
                ++$n;
            }

            $data = ['grid' => $this->grid, 'mensaje' => "OK",'halladoPos'=>$hallado, 'pos'=>$mejorPosicion, 'puntajeAnterior'=>$antesPuntos,'coord'=>$coord, 'mejorPosic'=>$mejorPosicion, 'maximoPuntajeObtenido'=>$maximo];
            
            return $data;
        }  // fin de dificultad media



        if($dificultad == 13){
            // $num = rand(0,$request->cant-1);  // sae toma posicion randoom de lista de posiciones posibles
             $posicion ="1,3"; 
             $coord= 0; // se hace split con delimitador de coma
   
             $media = $request->cant/2;
             $mediaR = round($media);  // cantidad de posiciones a evaluar (la mitad)
 
             $maximo=0; // variable para verificar ganacia de usar posicion
             $mejorPosicion="1,3";
  
             $antesPuntos=$this->puntajeAuto($matriz);       
             $n=0;
             $resultado=0;
             $hallado=0;
 
             
             
             while($n < $request->cant-1){
                 $posicion = $direcciones[$n]; 
                 $coord= explode(",",$posicion); // se hace split con delimitador de coma
                 $rowU = $coord[0];
                 $colU= $coord[1];
                 
                 $resultado = $this->evaluarJugadaPuntos($coord,$matriz); // se consigue el puntaje obtenido en caso de usar posuicion
                // $matrizResultante = $this->AutoJugada($rowU,$colU,$matriz);
                 
                 if($resultado >= $maximo){ // si se halla una puntuacion mayor a la actual
                    $maximo= $resultado;   // se guarada junto a la posicion
                     $mejorPosicion= $posicion;
                     $hallado=$n;
                 }
                 ++$n;
                 ++$hallado;
             }
 
             $data = ['grid' => $this->grid, 'mensaje' => "OK",'halladoPos'=>$hallado, 'pos'=>$mejorPosicion, 'puntajeAnterior'=>$antesPuntos,'coord'=>$coord, 'mejorPosic'=>$mejorPosicion, 'maximoPuntajeObtenido'=>$maximo];
             
             return $data;
         }  // fin de dificultad media





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
                    if ($this->grid[$row + 1][$col] !=0) {
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



        public function evaluarJugadaPuntos($coord, $matriz){
            $totalPuntosActual=0;
            $matrizCopia =  $matriz;  // usar matriz copia 
            $rowE = $coord[0];
            $colE= $coord[1]; 
            $matrizAEvaluaeR=0;

           $matrizAEvaluaeR = $this->AutoJugada($rowE,$colE,$matrizCopia);  // retorna varibale con jugada realizada

            $totalPuntosActual = $this->puntajeAuto($matrizAEvaluaeR);
            /// logica de evaluar jugadas
                    
            //
            return $totalPuntosActual; 
        }

        ///////////////////////////////////////////////////
        /////////////////////////////////////////////////////

        public function AutoJugada($rowE,$colE,$matriz){
            $matrizCopia =  $matriz;

            $lenC = sizeof($matrizCopia); // tamaño de  la matriz
            $count=0; // contador de fichas a pintar 
            $inicioCol = $colE; // valores de reserva
            $inicioRow = $rowE; // de columna y fila original 
            $matrizCopia[$rowE][$colE]=2; 


        ////////////////////////// HORIZONTAL

        if ($rowE <= $lenC - 1 && $colE <= $lenC - 1) {  // si no se sale del tamaño de matriz

                // derecha
                while($colE +1 <= $lenC - 1){
                    if( $matrizCopia[$rowE][$colE + 1] == 1 ){
                            ++$count; // incrementa contador
                        }
                    
                    if($matrizCopia[$rowE][$colE + 1]   == 2  && $count>0 ){ // si encuentra pieza de jugador , pero posee piezas intermedias
                            while( $colE > $inicioCol){   // retrocede hacia atras para marcar el ersto de fichas 
                                $matrizCopia[$rowE][$colE] = 2; 
                                --$colE;  // hasta el origen inicial
                            }
                            break;
                    } 
                    ++$colE;
                 // fin while player 1 
            }
     
        }  // fin logica horizontal derecha
       
                
            $colE= $inicioCol; // setaer variables originales
            $rowE = $inicioRow; // de col y row
            $count =0; // reseteo de contador
                // izquierda
            if ($rowE >= 0 && $colE >= 0) {  // si no se sale del tamaño de matriz

                // derecha
                while($colE - 1 >= 0){
                    if ($matrizCopia[$rowE][$colE -1] == 1) { // si la ficha derecha  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($matrizCopia[$rowE][$colE -1] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $colE < $inicioCol){   // retrocede hacia atras para marcar el resto de fichas 
                            $matrizCopia[$rowE][$colE] = 2; 
                            ++$colE;  // hasta el origen inicial
                        }
                        break;
                } 
                --$colE;
            } // fin while player 2
        

        }

        /////////////////////////////////////// VERTICAL
        $colE= $inicioCol; // setaer variables originales
        $rowE = $inicioRow; // de col y row
        $count =0; // reseteo de contador

        //ARRIBA
        if ($rowE >= 0 && $colE >= 0) {  // no se salga de matriz tamaño
                while($rowE-1 >=0){
                    if ($matrizCopia[$rowE-1][$colE] == 1) { // si la ficha arriba  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($matrizCopia[$rowE-1][$colE] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $rowE < $inicioRow){   // retrocede hacia atras para marcar el resto de fichas 
                            $matrizCopia[$rowE][$colE] = 2; 
                            ++$rowE;  // hasta el origen inicial
                        }
                        break;
                    }
                    --$rowE;
                }


        }
        $colE= $inicioCol; // setaer variables originales
        $rowE = $inicioRow; // de col y row
        $count =0; // reseteo de contador

        //AABAJO
        if ($rowE <= $lenC - 1 && $colE <= $lenC - 1) {  // no se salga de matriz tamaño

                while($rowE + 1 <= $lenC - 1){

                    if ($matrizCopia[$rowE+1][$colE] == 1) { // si la ficha arriba  es del rival
                        ++$count; // incrementa contador
                    }
                    if ($matrizCopia[$rowE+1][$colE] == 2  && $count>0) { // si encuentra pieza de jugador , pero posee piezas intermedias
                        while( $rowE > $inicioRow){   // retrocede hacia atras para marcar el resto de fichas 
                            $matrizCopia[$rowE][$colE] = 2; 
                            --$rowE;  // hasta el origen inicial
                        }
                        break; 
                    }
                    ++$rowE;
                }

           

        }

        //////////////////////////DIAGONAL
        $colE= $inicioCol; // setaer variables originales
        $rowE = $inicioRow; // de col y row
        $count =0; // reseteo de contador

            #upDer
            while ($rowE-1 >=0 && $colE+1 <= $lenC-1){
                if($matrizCopia[$rowE-1][$colE+1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($matrizCopia[$rowE-1][$colE+1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $colE >$inicioCol && $rowE <$inicioRow ){
                        $matrizCopia[$rowE][$colE] = 2; 
                        --$colE;
                        ++$rowE;
                    }
                    break;
                }
                --$rowE;
                ++$colE;
            }

            $colE= $inicioCol; // setaer variables originales
            $rowE = $inicioRow; // de col y row
            $count =0; // reseteo de contadorl
            #upIzq
            while ($rowE-1 >=0 && $colE-1 >= 0){
                if($matrizCopia[$rowE-1][$colE-1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($matrizCopia[$rowE-1][$colE-1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $colE < $inicioCol && $rowE <$inicioRow ){
                        $matrizCopia[$rowE][$colE] = 2; 
                        ++$colE;
                        ++$rowE;
                    }
                    break;
                }
                --$rowE;
                --$colE;
            }

            $colE= $inicioCol; // setaer variables originales
            $rowE = $inicioRow; // de col y row
            $count =0; // reseteo de contadorl
            #dowDer
            while ($rowE+1 <= $lenC-1 && $colE+1<= $lenC-1){
                if($matrizCopia[$rowE+1][$colE+1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($matrizCopia[$rowE+1][$colE+1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $colE > $inicioCol && $rowE >$inicioRow ){
                        $matrizCopia[$rowE][$colE] = 2; 
                        --$colE;
                        --$rowE;
                    }
                    break;
                }
                ++$rowE;
                ++$colE;
            }

            
            $colE= $inicioCol; // setaer variables originales
            $rowE = $inicioRow; // de col y row
            $count =0; // reseteo de contadorl
            #dowIzq

            
            while ($rowE+1 <= $lenC-1 && $colE-1>= 0){
                if($matrizCopia[$rowE+1][$colE-1] == 1){ // si la ficha derecha  es del rival
                    ++$count;  // incrementa conntador
                }
                if($matrizCopia[$rowE+1][$colE-1] == 2 && $count>0){  // si encuentra pieza de jugador , pero posee piezas intermedias
                    while( $colE < $inicioCol && $rowE >$inicioRow ){
                        $matrizCopia[$rowE][$colE] = 2; 
                        ++$colE;
                        --$rowE;
                    }
                    break;
                }
                ++$rowE;
                --$colE;
            }

        return $matrizCopia;  // al final de todo retorna la matriz resultante

    }// fin de funcion autoJugada






        ///////////////////////////////////////////////////
        /////////////////////////////////////////////////////


    public function puntajeAuto($matriz){
        $total=0;
        $lenM = sizeof($matriz); // tamaño de la matriz

        for( $i=0; $i< $lenM ;$i++) {   // contabiliza la cantidad de espacios vacios en tablero
            for( $j=0; $j< $lenM ;$j++) {
                if ($matriz[$i][$j] == 2){  // si halla ficha del automatico
                    ++$total;  // se llama funcion para verificar que la posicion seria valida    
                }
            }
        }

        return $total;
    }

       
}



 