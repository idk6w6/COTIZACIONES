document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productoForm');
    const mensajeResultado = document.getElementById('mensajeResultado');
    const submitButton = form.querySelector('button[type="submit"]');

    // estado de carga
    function toggleLoadingState(loading) {
        submitButton.disabled = loading;
        submitButton.innerHTML = loading ? 
            '<span class="spinner-border spinner-border-sm"></span> Procesando...' : 
            (form.querySelector('input[name="id"]') ? 'Actualizar' : 'Guardar');
    }

    // mostrar mensajes
    function mostrarMensaje(mensaje, tipo) {
        mensajeResultado.innerHTML = mensaje;
        mensajeResultado.className = `alert alert-${tipo} mt-3`;
        mensajeResultado.classList.remove('d-none');
        
        setTimeout(() => {
            mensajeResultado.classList.add('d-none');
        }, 5000);
    }

    //envío del formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            toggleLoadingState(true);
            
            const formData = new FormData(form);
            const response = await fetch('/Cotizaciones/app/controllers/ProductosController.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                mostrarMensaje(result.message, 'success');
                if (!formData.get('id')) {
                    form.reset(); // Solo limpiar si es creación
                }
            } else {
                mostrarMensaje(result.message || 'Error al procesar la solicitud', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarMensaje('Error al procesar la solicitud', 'danger');
        } finally {
            toggleLoadingState(false);
        }
    });
});