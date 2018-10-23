var matriz = []; // variable que almacena la matrizz actual 
var interval;
var jugador = 1;
var tipoJuego = 0;

var jugadasPosibles = []; // array de jugadas para el jugador automatico
var cantidadJugadasPosibles = 0;
var rowAuto;
var colAuto;
var posicionAuto;
// banderas logicas de request
var listoJugadasPosibles = false;
var listoSeleccionarJugada = false;

// funciones del lado clinete ---------------------------------------------------------------------------------------------------------------


function muestra_oculta(id) {

    if (document.getElementById) { //se obtiene el id
        var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
        el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
    }
}

function carga_datos() {

    tipoJuego = localStorage.getItem("tipo"); // obtiene el tipo de juego (automatico o multiplayer);
    // asi como su dificultad 
    localStorage.clear(); // elimina el localStorage

    // duración del tiempo de jugada
    var dropdownTime = document.getElementById("time");
    var selectedItem = dropdownTime.options[dropdownTime.selectedIndex].value;

    // colores del fondo y las fichas de los jugadores
    var tokenColorI = $('#TokenColorI').val();
    var tokenColorII = $('#TokenColorII').val();
    var background = $('#background').val();

    //  tamaño de la matrix
    var tamaño = $('#tamaño').val();

    // validaciones
    if (tamaño === '' || selectedItem === '' || tokenColorI === '' || tokenColorII === '' || background === '') {

        alerta_personalizada("Mensaje", "Verifica llenar correctamente el formulario!", "info");

    } else {

        // Los valores de la matriz deben ser divisibles entre dos 
        if ((tamaño % 2) == 0) {

            swal("Mensaje!", "Configuración de OthelloTec exitosa", "success")
                .then((value) => {

                    // matriz logica
                    crear_matriz(tamaño);
                    // mostrar ocultar las opciones de contenido
                    muestra_oculta('contenido');
                    // matriz grfica
                    genera_tabla(tamaño, tamaño);
                    //agrego los nuevos cambios
                    document.getElementById('grid').style.background = background;
                    // matriz grafica/logica
                    refrescar_matriz();
                    // colocamos el tiempo de la jugada
                    document.getElementById("turno").style.display = "block";
                    document.getElementById("menu").style.display = "block";
                    document.getElementById("players").style.display = "block";
                    document.getElementById('turno_player').style.color = tokenColorI;

                    //seleccionar_tiempo(selectedItem);


                });

        } else { alerta_personalizada("Mensaje", "Verifica que el tamaño de la matriz sea par!", "info"); }
    }
}

function genera_tabla(fila, columna) {
    // Obtener la referencia del elemento body
    var body = document.getElementsByTagName("body")[0];

    // Crea un elemento <table> y un elemento <tbody>
    var tabla = document.createElement("table");
    // Agrego una clase a la tabla
    tabla.setAttribute('class', 'grid');
    tabla.setAttribute('id', 'grid');

    var tblBody = document.createElement("tbody");

    // Crea las celda
    for (var i = 0; i < fila; i++) {

        // Crea las fila de la tabla
        var row = document.createElement("tr");
        // Agrego una clase a la fila
        //row.setAttribute('class', 'row');

        for (var j = 0; j < columna; j++) {
            // Crea un elemento <td> y un nodo de texto, haz que el nodo de
            // texto sea el contenido de <td>, ubica el elemento <td> al final
            // de la fila de la tabla

            // Creo la columna 
            var col = document.createElement("td");
            // Agrego una clase celda
            col.setAttribute('class', 'cell');
            // Agrego un ID unico
            col.setAttribute('id', i + ',' + j);
            // Agrego evento que envia la posicion del elecmeto 
            col.setAttribute("onclick", "posiciones_matriz(this.id)");

            // Agrego el disco dentro de la fila y columna
            var disco = document.createElement("div");
            // Agrego la clase disc
            disco.setAttribute('class', 'disc');
            // A la columna le agreco un disco
            col.appendChild(disco);
            // A la fila le agrego una columna
            row.appendChild(col);
        }
        // agrega la hilera al final de la tabla (al final del elemento tblbody)
        tblBody.appendChild(row);
    }
    // posiciona el <tbody> debajo del elemento <table>
    tabla.appendChild(tblBody);
    // appends <table> into <body>
    body.appendChild(tabla);
    // modifica el atributo "border" de la tabla y lo fija a "2";
    tabla.setAttribute("border", "4");
}


function refrescar_matriz() {
    var tokenColorI = $('#TokenColorI').val();
    var tokenColorII = $('#TokenColorII').val();
    var background = $('#background').val();


    // Basicamente: lee la matriz global y pinta de colores
    // 0: significa vacio y se pinta de verde
    // 1: significa jugador I y se pinta de blanco
    // 2: significa jugador II y se pinta de negro

    for (var row = 0; row < matriz.length; row++) {

        for (var col = 0; col < matriz.length; col++) {

            if (matriz[row][col] == 0) {

                document.getElementById(row + ',' + col).childNodes[0].style.backgroundColor = background;

            }
            if (matriz[row][col] == 1) {

                document.getElementById(row + ',' + col).childNodes[0].style.backgroundColor = tokenColorI;

            }
            if (matriz[row][col] == 2) {

                document.getElementById(row + ',' + col).childNodes[0].style.backgroundColor = tokenColorII;

            }
        }
    }

}


// funciones para mostrar alestas  --------------------------------------------------------------------------------------------------------------------

function alerta_personalizada(titulo, texto, icono) {

    swal({
        title: titulo,
        text: texto,
        icon: icono,
        button: true,
    });
}


// funciones del cronometro  --------------------------------------------------------------------------------------------------------------------

function seleccionar_tiempo(tiempo) {

    //cambie los tiempos por prueba
    switch (tiempo) {
        case '0.5':
            iniciar_cronometro(2);
            break;
        case '1':
            iniciar_cronometro(15);
            break;
        default:
            iniciar_cronometro(20);
    }
}

function iniciar_cronometro(tiempo) {

    clearInterval(interval);

    var timer = tiempo;

    interval = setInterval(function() {
        timer--;
        var result = convertion(timer);
        $('.wait_timer').text(result);
        if (timer === 0) {
            clearInterval(interval);
            $("#esperando_jugador").modal('hide');
        }
    }, 1000);
}

function convertion(time) {

    var minutes = Math.floor((time % 3600) / 60);
    var seconds = time % 60;

    minutes = minutes < 10 ? '0' + minutes : minutes;

    seconds = seconds < 10 ? '0' + seconds : seconds;

    var result = minutes + ":" + seconds; // 2:41:30

    return result;
}

// funciones del lado servidor ----------------------------------------------------------------------------------------------------------------

function crear_matriz(tamaño) {

    $.ajax({
        method: 'get',
        url: '/othelloboard/crearMatriz',
        data: { 'tamaño': tamaño },

        success: function(response) {

            //se crea la matriz logica desde el controlador
            matriz = response;
            //se actualiza la matriz grafica
            refrescar_matriz();
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit crearMatriz", "warning")
        }
    });

}

function posiciones_matriz(posicion) {

    // El div de las posiciones viene en un string: '1,2'
    // Lo que se hace es separarlos y actualizar la matriz global 
    // Seguidamente se actualiza la matriz grafica
    console.log("POSICION: ", posicion);

    var elemento = posicion.split(',');
    var fila = parseInt(elemento[0]);
    var columna = parseInt(elemento[1]);

    var dropdownTime = document.getElementById("time");
    var tiempo = dropdownTime.options[dropdownTime.selectedIndex].value;

    var tokenColorI = $('#TokenColorI').val();
    var tokenColorII = $('#TokenColorII').val();


    $.ajax({
        method: 'get',
        url: '/othelloboard/selectCell',
        data: { 'grid': matriz, 'row': fila, 'col': columna, 'player': jugador },


        success: function(response) {


            if (response.mensaje == "OK") {

                // actualizo la mareiz
                matriz = response.grid;
                refrescar_matriz();
                puntos_jugador();

                document.getElementById("cover-spin").style.visibility = "hidden";



                // cambio el tipo de jugador 
                if (response.player == 1) {
                    jugador = 2;
                    $('.turno_player').text('Turno:  Palyer II');
                    document.getElementById('turno_player').style.color = tokenColorII;



                    if (tipoJuego == 11) { //// si no es de tipo multiplayer
                        document.getElementById("cover-spin").style.visibility = "visible";

                        console.log("-------Se usa jugador automatico facil xdxd-------");

                        posiblesJugadas(); // primero se obtienen las posibles jugadas admitibles de la matriz actual
                        console.log("ahorrando time");

                    }

                    if (tipoJuego == 12) { //// si no es de tipo multiplayer
                        document.getElementById("cover-spin").style.visibility = "visible";

                        console.log("-------Se usa jugador automatico medio xdxd-------");

                        posiblesJugadas(); // primero se obtienen las posibles jugadas admitibles de la matriz actual
                        console.log("ahorrando time");
                    }

                    if (tipoJuego == 13) { //// si no es de tipo multiplayer
                        document.getElementById("cover-spin").style.visibility = "visible";

                        console.log("-------Se usa jugador automatico dificil xdxd-------");

                        posiblesJugadas(); // primero se obtienen las posibles jugadas admitibles de la matriz actual
                        console.log("ahorrando time");
                    }


                } else {
                    jugador = 1;
                    $('.turno_player').text('Turno:  Player I');
                    document.getElementById('turno_player').style.color = tokenColorI;
                }

            } else {

                toastr.warning('jugada invalida!');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit seleccionar celda", "warning")
        }
    });

}

// puntos de los jugadores
function puntos_jugador() {
    $.ajax({
        method: 'get',
        url: '/othelloboard/ganador',
        data: { 'grid': matriz },

        success: function(response) {

            if (response.ganador == 'continua') {

                $('.playerI_label').text('Player I:  ' + response.playerI);
                $('.playerII_label').text('Player II:  ' + response.playerII);

            } else {

                $('.playerI_label').text('Player I:  ' + response.playerI);
                $('.playerII_label').text('Player II:  ' + response.playerII);

                alerta_personalizada("Mensaje", "Fin del juego: ganó  " + response.ganador, "success")

            }
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit ganador", "warning")
        }
    });

}


function posiblesJugadas() {

    $.ajax({
        method: 'get',
        url: '/automatic/posiblesJugadas',
        data: { 'grid': matriz, 'player': jugador },


        success: function(response) {


            if (response.mensaje == "OK") {
                jugadasPosibles = response.direcciones;
                cantidadJugadasPosibles = response.cant;
                console.log("JUGADAS POSIBLES: ", jugadasPosibles);
                console.log("-");
                console.log("Cantidades de espacios a usar: ", cantidadJugadasPosibles);
                seleCeldAutomatic();

            } else {

                toastr.warning('jugada invalida!');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit posiblesJugadas()", "warning")
        }
    });


}


function seleCeldAutomatic() {

    $.ajax({
        method: 'get',
        url: '/automatic/evaluarJugada',
        data: { 'grid': matriz, 'dif': tipoJuego, 'player': jugador, 'direcc': jugadasPosibles, 'cant': cantidadJugadasPosibles },


        success: function(response) {

            if (response.mensaje == "OK") {
                console.log("response de seleCeldAutomatic: ", response);
                posicionAuto = response.pos;
                posiciones_matriz(posicionAuto);

            } else {

                toastr.warning('jugada invalida!');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("response fail de seleCeldAutomatic");
            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit seleccionar celda", "warning")
        }
    });
}