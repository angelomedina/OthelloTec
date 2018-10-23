/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: variables globales
var matriz = [];
var color_playerI = "";
var color_playerII = "";
var user_propietario = 0;
var actual_user = 0;
var interval;
var colorBoard = "";
var username = "";
var turno = 0;

var usernameI = "";
var usernameII = "";

var mensajes = [];



//#SESSIONES


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: recuperar valores de la url
function paramentros_url(name) {

    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: eviar valores por url
function redireccionar(board_id, session_id, boardSize) {
    window.location.assign('http://othellotec.herokuapp.com/join?' + 'board=' + board_id + '&session=' + session_id + '&size=' + boardSize);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: crear session
function crear_session() {

    var background = $('#background').val();
    var size = $('#tamaño').val();
    var piece_color = $('#TokenColorI').val();

    $.ajax({
        method: 'get',
        url: '/session/crearSession',
        data: { 'piece_color': piece_color, 'size': size, 'background': background },

        success: function(response) {

            alerta_personalizada("Session", "La partida iniciara cuando otro jugador se una a la partida!", "success");
            window.location.assign("http://othellotec.herokuapp.com/home");

        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit crearSession", "warning");
        }
    });
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: cuando un usuario se une a ua session ocupa los datos de esta y al mismo tiempo agrega las piezas iniciales
function recuperar_datos(session_id) {

    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/session/obtenerColorPiece',
        data: { 'session_id': session_id },

        success: function(response) {

            var piece_color1 = response[0].piece_color;
            var piece_color2 = $('#TokenColorII').val();
            var board_id = paramentros_url('board');
            var board_size = paramentros_url('size');

            //para ver cual jugador es 
            color_playerI = piece_color1;
            color_playerII = piece_color2;

            piezas_iniciales(piece_color1, piece_color2, board_id, session_id, board_size);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit obtenerColorSession", "warning")
        }
    });
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: calcula las cordenadas de las piezas iniciales
function piezas_iniciales(piece_color1, piece_color2, board_id, session_id, board_size) {

    /*
    [[0,0,0,0],
     [0,1,2,0],
     [0,2,1,0],
     [0,0,0,0]]
    */

    // Palyer uno
    var inicial_x1 = ((board_size / 2) - 1);
    var inicial_y1 = ((board_size / 2) - 1);

    var inicial_x4 = ((board_size / 2));
    var inicial_y4 = ((board_size / 2));

    //player dos
    var inicial_x2 = ((board_size / 2) - 1);
    var inicial_y2 = ((board_size / 2));

    var inicial_x3 = ((board_size / 2));
    var inicial_y3 = ((board_size / 2) - 1);

    var json = {
        "user1": [inicial_x1, inicial_y1, inicial_x4, inicial_y4, piece_color1],
        "user2": [inicial_x2, inicial_y2, inicial_x3, inicial_y3, piece_color2],
        "llaves": [board_id, session_id]
    };

    unirme_session(json, board_size);

}




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: se une a una session ya creada
function unirme_session(json, size) {

    $.ajax({
        method: 'get',
        url: '/session/unirmeSession',
        data: {
            'inicial_x1': json.user1[0],
            'inicial_y1': json.user1[1],
            'inicial_x4': json.user1[2],
            'inicial_y4': json.user1[3],
            'piece_color1': json.user1[4],
            'inicial_x2': json.user2[0],
            'inicial_y2': json.user2[1],
            'inicial_x3': json.user2[2],
            'inicial_y3': json.user2[3],
            'piece_color2': json.user2[4],
            'board_id': json.llaves[0],
            'session_id': json.llaves[1]
        },

        success: function(response) {

            alerta_personalizada("Exito", "Te as unido a una session", "success");
            window.location.assign('http://othellotec.herokuapp.com/home');
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit unirmeSession", "warning")
        }
    });

}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//#JUGABILIDAD

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: inicio la partida recuperando los datos de la partida y mostrando la matriz grafica
function iniciar_partida() {

    document.getElementById("main_session").style.display = "none";
    document.getElementById("chat").style.display = "block";
    var size = paramentros_url('size');

    color_board();
    recuperar_array();
    recuperar_sms();
    crear_matriz_logica(size);
    recuperar_colores();
    escucha_jugadas();

}




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: obtengo las coordenadas de la matriz grafica
function recuperar_array() {

    var board_id = paramentros_url('board');
    var size = paramentros_url('size');

    $.ajax({
        method: 'get',
        url: '/session/arrayPieces',
        data: { 'board_id': board_id },

        success: function(response) {

            tabla_grafica(size, size);
            document.getElementById('grid').style.background = colorBoard;

            response.forEach(function(element) {
                pintar_matriz(element.x, element.y, element.color);
                igual_matriz(element.x, element.y, element.color);

                if (element.color == color_playerI) {
                    turno = 1;
                }
                if (element.color == color_playerII) {
                    turno = 2;
                }
            });

        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit recuperar array", "warning")
        }
    });
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: obtengo x,y ademas de un color y pinto 
function pintar_matriz(x, y, color) {

    document.getElementById(x + ',' + y).childNodes[0].style.backgroundColor = color;
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: creo una tabla grafica 
function tabla_grafica(fila, columna) {
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
            col.setAttribute("onclick", "coordenadas(this.id)");

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


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: me redirrecciona a sessionboard y envia por parametros los datos de la sesion
function play(board_id, session_id, boardSize, user_id) {

    window.location.assign('http://othellotec.herokuapp.com/sessionboard?' + 'board=' + board_id + '&session=' + session_id + '&size=' + boardSize + '&id=' + user_id);

}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: cuando doy click en una celda del juego de da una coordenada que la envio a verificar y reflejo los cambios 
function coordenadas(posicion) {

    var elemento = posicion.split(',');
    var x = parseInt(elemento[0]);
    var y = parseInt(elemento[1]);

    var board_id = paramentros_url('board');
    var piece_color;
    var jugador;
    actual_user = paramentros_url('id');
    var session_id = paramentros_url('session');



    if (Number(actual_user) === user_propietario) {

        piece_color = color_playerI;
        jugador = 1;

        if (jugador == turno) {
            $('.turno_player_session').text("Turno: " + usernameI);
            document.getElementById('turno_player_session').style.color = color_playerI;
        } else {
            $('.turno_player_session').text("Turno: " + usernameII);
            document.getElementById('turno_player_session').style.color = color_playerII;
        }


    } else {

        piece_color = color_playerII;
        jugador = 2;

        if (jugador == turno) {
            $('.turno_player_session').text("Turno: " + usernameI);
            document.getElementById('turno_player_session').style.color = color_playerI;
        } else {
            $('.turno_player_session').text("Turno: " + usernameII);
            document.getElementById('turno_player_session').style.color = color_playerII;
        }

    }

    console.log(jugador, "==", turno);

    if (jugador != turno) {

        $.ajax({
            method: 'get',
            url: '/session/selectCell',
            data: { 'grid': matriz, 'row': x, 'col': y, 'player': jugador, 'board_id': board_id, 'piece_color': piece_color, 'user_id': actual_user, 'session_id': session_id },


            success: function(response) {

                if (response.mensaje == "OK") {

                    // actualizo la matriz
                    matriz = response.grid;

                    //turno_player(jugador);
                    //guarda la coordenada actual
                    crearPieces(x, y, board_id, piece_color);
                    //refresco las coordenadas de la jugada y donde coloco el click
                    refrescar_array();
                    puntos_session();
                    tiempo_session('0.5');

                } else {

                    toastr.warning('jugada invalida!');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit seleccionar celda", "warning")
            }
        });

    } else {
        alertify.error('No es tu turno!!');
    }

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: para cambiar turnos
function turno_player(jugador) {

    if (jugador == 1) {
        $('.turno_player_session').text("Turno: " + usernameII);
        document.getElementById('turno_player_session').style.color = color_playerII;
        turno = 2;
    }
    if (jugador == 2) {
        $('.turno_player_session').text("Turno: " + usernameI);
        document.getElementById('turno_player_session').style.color = color_playerI;
        turno = 1;
    }


}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: creo solo una piza con x y y; debido a que el jugo aun no valida la posc actual
function crearPieces(x, y, board_id, color) {

    $.ajax({
        method: 'get',
        url: '/session/crearPieces',
        data: { 'piece_color': color, 'x': x, 'y': y, 'board_id': board_id },

        success: function(response) {
            refrescar_array();
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en agregar pieza celda", "warning")
        }
    });

}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: creo la matriz logica
function crear_matriz_logica(tamaño) {

    $.ajax({
        method: 'get',
        url: '/othelloboard/crearMatriz',
        data: { 'tamaño': tamaño },

        success: function(response) {

            //se crea la matriz logica desde el controlador
            matriz = response;

        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit crearMatriz", "warning")
        }
    });

}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: recupero los colores de los jugadores de la partida
function recuperar_colores() {

    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/session/obtenerColorPiece',
        data: { 'session_id': session_id },

        success: function(response) {

            $('.playerI_label').text(response[0].score);
            $('.playerII_label').text(response[1].score);

            var piece_color1 = response[0].piece_color;
            var piece_color2 = response[1].piece_color;

            //para ver cual jugador es 
            color_playerI = piece_color1;
            color_playerII = piece_color2;
            //propietario
            user_propietario = response[0].user_id;

            get_user(response[0].user_id, 1);
            get_user(response[1].user_id, 2);
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit obtenerColorSession", "warning")
        }
    });
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: refresco el array
function refrescar_array() {

    var board_id = paramentros_url('board');

    $.ajax({
        method: 'get',
        url: '/session/arrayPieces',
        data: { 'board_id': board_id },

        success: function(response) {

            console.log(response);

            response.forEach(function(element) {
                pintar_matriz(element.x, element.y, element.color);
                igual_matriz(element.x, element.y, element.color);

                if (element.color == color_playerI) {
                    turno = 1;
                }
                if (element.color == color_playerII) {
                    turno = 2;
                }
            });

            if (turno == 1) {
                $('.turno_player_session').text("Turno: " + usernameII);
                document.getElementById('turno_player_session').style.color = color_playerII;
            } else {
                $('.turno_player_session').text("Turno: " + usernameI);
                document.getElementById('turno_player_session').style.color = color_playerI;
            }

        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit recuperar array", "warning")
        }
    });
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: optiene user por id 
function get_user(user_id, player) {

    $.ajax({
        method: 'get',
        url: '/session/userID',
        data: { 'user_id': user_id },

        success: function(response) {

            document.getElementById("nav_session").style.display = "block";

            if (player == 1) {

                usernameI = response.username;
                $('.playerI_username').text(response.username);
                document.getElementById("playerI_username").style.color = color_playerI;

            } else {

                usernameII = response.username;
                $('.playerII_username').text(response.username);
                document.getElementById("playerII_username").style.color = color_playerII;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en get user by id", "warning")
        }
    });

}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: escucha los cambios por jugada
function escucha_jugadas() {
    refrescar_array();
    recuperar_sms();
    puntos_session();
    setTimeout(escucha_jugadas, 5000);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: iguala la matriz grafica y logica
function igual_matriz(x, y, color) {

    var X = parseInt(x);
    var Y = parseInt(y);

    try {
        if (color == color_playerI) {
            matriz[X][Y] = 1;
        } else {
            matriz[X][Y] = 2;
        }
    } catch (err) {
        console.log(err.message);
    }

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: me devuelve el score de los usuarios
function score() {

    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/session/scoreSession',
        data: { 'session_id': session_id, 'user_id': actual_user },

        success: function(response) {},
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en get score by id", "warning")
        }
    });
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: colocar puntos en db
function points(puntos) {

    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/session/score',
        data: { 'session_id': session_id, 'user_id': actual_user, 'puntos': puntos },

        success: function(response) {
            score();
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en get score by id", "warning")
        }
    });
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: obtiene los puntos de la session
function puntos_session() {

    if (matriz.length > 0) {

        $.ajax({
            method: 'get',
            url: '/session/ganador',
            data: { 'grid': matriz },

            success: function(response) {

                $('.playerI_label').text(response.playerI);
                $('.playerII_label').text(response.playerII);

                if (response.ganador == 'continua') {

                    if (user_propietario == actual_user) {
                        points(response.playerI);
                        $('.playerI_label').text(response.playerI);
                    } else {
                        points(response.playerII);
                    }

                } else {

                    if (response.ganador == "playerI") {

                        toastr.info("Ganó " + usernameI, "Game Over" + " (presiona Ménu: abandonar para dejar de ver este mensaje!)");

                    } else {

                        toastr.info("Ganó " + usernameII, "Game Over" + " (presiona Ménu: abandonar para dejar de ver este mensaje!)");
                    }
                    finalizar_partida();

                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit ganador", "warning")
            }
        });
    }

}

function finalizar_partida() {

    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/session/finalizar',
        data: { 'session_id': session_id },

        success: function(response) {},
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoint finalizar partida", "warning")
        }
    });

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: funciones de tiempo
function tiempo_session(tiempo) {

    //cambie los tiempos por prueba
    switch (tiempo) {
        case '0.5':
            cronometro_session(2);
            break;
        case '1':
            cronometro_session(15);
            break;
        default:
            cronometro_session(20);
    }
}

function cronometro_session(tiempo) {

    $("#esperando_session").modal('show');
    clearInterval(interval);

    var timer = tiempo;

    interval = setInterval(function() {
        timer--;
        var result = convertion(timer);
        $('.wait_session').text(result);
        if (timer === 0) {
            clearInterval(interval);
            $("#esperando_session").modal('hide');
        }
    }, 1000);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: funcion para el chat
function slide_chat() {
    var arrow = $('.chat-head img');
    var src = arrow.attr('src');

    $('.chat-body').slideToggle('fast');
    if (src == 'https://maxcdn.icons8.com/windows10/PNG/16/Arrows/angle_down-16.png') {
        arrow.attr('src', 'https://maxcdn.icons8.com/windows10/PNG/16/Arrows/angle_up-16.png');
    } else {
        arrow.attr('src', 'https://maxcdn.icons8.com/windows10/PNG/16/Arrows/angle_down-16.png');
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: obtiene el texto del cliente
function process(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13) {
        var msg = document.getElementById('txt').value;
        document.getElementById('txt').value = '';

        if (mensajes.includes(msg) == false) {

            mensajes.push(msg);
            enviar_sms(msg);
            $('.msg-insert').prepend("<div class='msg-send'>" + msg + "</div>");
        }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: respalda los sms en db
function enviar_sms(texto) {

    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/chat/mensaje',
        data: { 'session_id': session_id, 'message': texto },

        success: function(response) {},
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoint chat", "warning")
        }
    });

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: recupera lista de mensajes de la session
function recuperar_sms() {

    actual_user = paramentros_url('id');
    var session_id = paramentros_url('session');

    $.ajax({
        method: 'get',
        url: '/chat/arraySMS',
        data: { 'session_id': session_id },

        success: function(response) {

            response.forEach(function(element) {

                if (mensajes.includes(element.message) == false) {

                    mensajes.push(element.message);

                    if (element.user_id == actual_user) {
                        $('.msg-insert').prepend("<div  class='msg-send'>" + element.message + "</div>");
                    } else {
                        $('.msg-insert').prepend("<div  class='msg-receive'>" + element.message + "</div>");
                    }
                }
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoint chat", "warning")
        }
    });

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcion: recupero el background del board
function color_board() {
    var session_id = paramentros_url('session');
    $.ajax({
        method: 'get',
        url: '/session/background',
        data: { 'session_id': session_id },

        success: function(response) {

            colorBoard = response;
        },
        error: function(jqXHR, textStatus, errorThrown) {

            alerta_personalizada("Mensaje", "Ha ocurrido un error en endpoit background", "warning")
        }
    });
}