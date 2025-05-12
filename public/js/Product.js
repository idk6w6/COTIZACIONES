document.addEventListener('DOMContentLoaded', function() {
    const productoForm = document.getElementById('productoForm');
    if (productoForm) {
        productoForm.addEventListener('submit', validateForm);
    }
});

function validateForm(e) {
    const precio = parseFloat(document.getElementById('precio').value);
    const iva = parseFloat(document.getElementById('iva').value);
    const peso = parseFloat(document.getElementById('unidad_peso').value);
    const stock = parseInt(document.getElementById('stock').value);

    //Validar precio
    if (precio < 0 || precio > 99999.99) {
        e.preventDefault();
        alert('El precio debe estar entre 0 y 99,999.99');
        return false;
    }

    //Validar tasas de IVA
    const tasasValidas = [0, 8, 16];
    if (!tasasValidas.includes(iva)) {
        e.preventDefault();
        alert('La tasa de IVA debe ser 0% (Exento), 8% (Fronterizo) o 16% (General)');
        return false;
    }

    //Validar peso
    if (peso < 0 || peso > 999.99) {
        e.preventDefault();
        alert('El peso debe estar entre 0 y 999.99');
        return false;
    }

    //Validar stock
    if (stock < 0 || stock > 9999) {
        e.preventDefault();
        alert('El stock debe estar entre 0 y 9,999 unidades');
        return false;
    }

    return true;
}

function editarProducto(id) {
    if (!id) {
        alert('Error: No se pudo identificar el producto');
        return;
    }
    window.location.href = `/Cotizaciones/app/views/productos/productos_crear_formulario.php?id=${id}&action=edit`;
}

function eliminarProducto(id) {
    if (confirm('¿Está seguro de que desea eliminar este producto?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('/Cotizaciones/app/controllers/ProductosController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            try {
                const result = JSON.parse(data);
                if (result.success) {
                    window.location.reload();
                } else {
                    alert('Error al eliminar el producto: ' + (result.error || 'Error desconocido'));
                }
            } catch (e) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el producto');
        });
    }
}

function cotizarProducto(id) {
    if (!id) {
        alert('Error: No se pudo identificar el producto');
        return;
    }
    window.location.href = `/Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?producto_id=${id}`;
}
