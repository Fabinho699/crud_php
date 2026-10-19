// Pide confirmación antes de eliminar un usuario.
(function () {
    var dialogo = document.getElementById('dialogo-eliminar');
    var puedeUsarDialogo = dialogo && typeof dialogo.showModal === 'function';

    document.addEventListener('click', function (evento) {
        var boton = evento.target.closest('[data-eliminar]');
        if (!boton) return;

        var nombre = boton.getAttribute('data-nombre');
        var id = boton.form.querySelector('input[name="id"]').value;

        evento.preventDefault();

        if (!puedeUsarDialogo) {
            if (window.confirm('¿Eliminar a ' + nombre + '? Se borrará de forma permanente.')) {
                boton.form.submit();
            }
            return;
        }

        document.getElementById('dialogo-nombre').textContent = nombre;
        document.getElementById('dialogo-id').value = id;
        dialogo.showModal();
    });

    if (puedeUsarDialogo) {
        dialogo.querySelector('[data-cerrar-dialogo]').addEventListener('click', function () {
            dialogo.close();
        });
        // Cerrar al hacer clic fuera de la ventana
        dialogo.addEventListener('click', function (evento) {
            if (evento.target === dialogo) dialogo.close();
        });
    }

    // Oculta el mensaje de éxito después de unos segundos
    var aviso = document.querySelector('.aviso-exito');
    if (aviso) {
        setTimeout(function () { aviso.classList.add('aviso-saliendo'); }, 4000);
    }
})();
