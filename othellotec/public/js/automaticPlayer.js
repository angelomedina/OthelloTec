/////////////
//////////////

function setTipoJuego(tipo) { // guarda en localstorage el tipo de juego que se va a realizar
    localStorage.setItem("tipo", tipo); // Modularizar , mientras es como prueba de funcionamiento
    if (tipo == 11) {
        console.log("facil xdx");
        alerta_personalizada("Dificultad", "Dificultad Fácil seleccionada Correctamente 7u7 ", "success");
        setTimeout(function() {
            window.location.assign("http://othellotec.herokuapp.com/othelloboard");
        }, 300)
    }
    if (tipo == 12) {
        alerta_personalizada("Dificultad", "Dificultad Media seleccionada Correctamente 7u7", "success");
        setTimeout(function() {
            window.location.assign("http://othellotec.herokuapp.com/othelloboard");
        }, 300)
    }
    if (tipo == 13) {
        alerta_personalizada("Dificultad", "Dificultad Dificl seleccionada Correctamente 7u7", "success");
        setTimeout(function() {
            window.location.assign("http://othellotec.herokuapp.com/othelloboard");
        }, 300)
    }
    if (tipo == 0) {
        alerta_personalizada("Multijugador", "Multijugador seleccionado correctamente", "success");
        window.location.assign("http://othellotec.herokuapp.com/session");
    }
}


// funciones para mostrar alertas  --------------------------------------------------------------------------------------------------------------------
// no es necesaria 
/*
function alerta_personalizada(titulo, texto, icono) {

    swal({
        title: titulo,
        text: texto,
        icon: icono,
        button: true,
    });
}
*/