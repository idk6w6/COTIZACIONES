function editarProducto(id) {
    if (!id) {
        alert('Error: ID de producto no válido');
        return;
    }
    window.location.href = `/Cotizaciones/app/views/productos/productos_crear_formulario.php?id=${id}&action=edit`;
}

function eliminarProducto(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminarán todas las referencias a este producto en las cotizaciones existentes. Esta acción no se puede revertir.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/Cotizaciones/app/controllers/ProductosController.php?action=delete&id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Eliminado',
                        text: 'El producto y sus referencias han sido eliminados.',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error',
                        data.error || 'Ocurrió un error al eliminar el producto',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'Error',
                    'Ocurrió un error al eliminar el producto',
                    'error'
                );
            });
        }
    });
}

function Tooltip() {
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
}

Tooltip();

document.addEventListener('DOMContentLoaded', function() {
    
    // Test SweetAlert2
    console.log('SweetAlert2:', typeof Swal !== 'undefined');
    initializeTooltips();
});