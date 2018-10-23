$(document).ready(function() {
    // ==============================

    // Ejemplo de mensajes de Notificaciones
    // Notificación Normal
    $("#normal").click(function() {
        alertify.log("Esto es una notificación cualquiera.");
    });

    // Notificación de Error
    $("#logout").click(function() {

        alertify.error("Deslogueo correcto.");
    });

});