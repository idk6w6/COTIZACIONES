function inicializarCalculos() {
    const productoId = document.getElementById('producto_id')?.value;
    if (productoId) {
        actualizarCantidadMax(productoId);
    }
}

function actualizarCantidadMax(producto_id) {
    fetch(`/Cotizaciones/app/controllers/ProductosController.php?action=get&id=${producto_id}`)
        .then(response => response.json())
        .then(producto => {
            if (producto) {
                const cantidadInput = document.getElementById('cantidad');
                cantidadInput.max = producto.stock;
                cantidadInput.value = Math.min(cantidadInput.value || 1, producto.stock);
                calcularTotales(producto);
            }
        });
}

function calcularTotales(producto) {
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const precio = parseFloat(producto.precio) || 0;
    const iva = parseFloat(producto.iva) || 0;
    const descuento = parseFloat(producto.descuento) || 0;

    const subtotal = cantidad * precio;
    const montoDescuento = (subtotal * descuento) / 100;
    const baseIva = subtotal - montoDescuento;
    const montoIva = (baseIva * iva) / 100;
    const total = baseIva + montoIva;

    document.getElementById('subtotal').value = formatCurrency(subtotal);
    document.getElementById('montoDescuento').value = formatCurrency(montoDescuento);
    document.getElementById('montoIva').value = formatCurrency(montoIva);
    document.getElementById('total').value = formatCurrency(total);

    document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
    document.getElementById('montoDescuento_hidden').value = montoDescuento.toFixed(2);
    document.getElementById('montoIva_hidden').value = montoIva.toFixed(2);
    document.getElementById('total_hidden').value = total.toFixed(2);
}

function formatCurrency(value) {
    return new Intl.NumberFormat('es-MX', { 
        style: 'currency', 
        currency: 'MXN'
    }).format(value);
}
