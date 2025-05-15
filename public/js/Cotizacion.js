const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const Client = require('./Client');
const User = require('./User');

//  modelo de cotizacion
function definirCotizacion() {
    return sequelize.define('Quotation', {
        id: {
            type: DataTypes.INTEGER,
            autoIncrement: true,
            primaryKey: true
        },
        cliente_id: {
            type: DataTypes.INTEGER,
            references: {
                model: Client,
                key: 'id'
            }
        },
        usuario_id: {
            type: DataTypes.INTEGER,
            references: {
                model: User,
                key: 'id'
            }
        },
        fecha_cotizacion: {
            type: DataTypes.DATE
        },
        subtotal: {
            type: DataTypes.DECIMAL(10, 2)
        },
        descuento: {
            type: DataTypes.DECIMAL(10, 2)
        },
        iva: {
            type: DataTypes.DECIMAL(5, 2)
        },
        total: {
            type: DataTypes.DECIMAL(10, 2)
        }
    }, {
        tableName: 'cotizaciones',
        timestamps: false
    });
}

const Quotation = definirCotizacion();
module.exports = Quotation;

// calcular los totales
function calcularTotales(campos) {
    const precio = parseFloat(campos.precio.value) || 0;
    const cantidad = parseInt(campos.cantidad.value) || 0;
    const iva = parseFloat(campos.iva.value) || 0;

    // calcular subtotal
    const subtotal = precio * cantidad;
    
    // calcular monto de iva
    const montoIva = subtotal * (iva / 100);
    
    // calcular total
    const total = subtotal + montoIva;

    // actualizar campos
    campos.subtotal.value = subtotal.toFixed(2);
    campos.montoIva.value = montoIva.toFixed(2);
    campos.total.value = total.toFixed(2);

    return total;
}

// habilitar o deshabilitar el boton de enviar
function manejarBotonSubmit(form, total) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (total <= 0) {
        submitBtn.disabled = true;
        alert('El total debe ser mayor a cero');
    } else {
        submitBtn.disabled = false;
    }
}

// validacion del formulario
function validarFormulario(e, campos, form) {
    e.preventDefault();
    const total = parseFloat(campos.total.value);
    if (total <= 0) {
        alert('El total de la cotización debe ser mayor a cero');
        return false;
    }

    // actualizar campos ocultos
    document.getElementById('subtotal_hidden').value = campos.subtotal.value;
    document.getElementById('montoIva_hidden').value = campos.montoIva.value;
    document.getElementById('montoDescuento_hidden').value = campos.montoDescuento.value;

    // enviar formulario
    form.action = '/Cotizaciones/app/controllers/CotizacionesController.php';
    form.submit();
}

// logica de cotizacion
function iniciarCotizacion() {
    const form = document.getElementById('cotizacionForm');
    const campos = {
        precio: document.getElementById('precio'),
        cantidad: document.getElementById('cantidad'),
        iva: document.getElementById('iva'),
        descuento: document.getElementById('descuento'),
        subtotal: document.getElementById('subtotal'),
        montoIva: document.getElementById('montoIva'),
        montoDescuento: document.getElementById('montoDescuento'),
        total: document.getElementById('total')
    };

    // calcular los totales al cambiar cantidad o descuento
    ['cantidad', 'descuento'].forEach(field => {
        campos[field].addEventListener('input', function() {
            const total = calcularTotales(campos);
            manejarBotonSubmit(form, total);
        });
    });

    // validacion antes de enviar el formulario
    form.addEventListener('submit', function(e) {
        validarFormulario(e, campos, form);
    });

    // calcular totales iniciales
    calcularTotales(campos);
}

function validarAntesDeEnviar() {
    const form = document.getElementById('cotizacionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
            const montoIva = parseFloat(document.getElementById('montoIva').value) || 0;
            const montoDescuento = parseFloat(document.getElementById('montoDescuento').value) || 0;
            const total = parseFloat(document.getElementById('total').value) || 0;

            // verificar montos antes de enviar
            if (subtotal <= 0 || montoIva < 0 || total <= 0) {
                alert('Por favor verifique los montos. Deben ser valores numericos validos.');
                return false;
            }

            // actualizar campos ocultos
            document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
            document.getElementById('montoIva_hidden').value = montoIva.toFixed(2);
            document.getElementById('montoDescuento_hidden').value = montoDescuento.toFixed(2);

            // enviar formulario
            form.action = '/Cotizaciones/app/controllers/CotizacionesController.php';
            form.submit();
        });
    }
}

// escuchar el evento para iniciar la cotizacion
document.addEventListener('DOMContentLoaded', function() {
    iniciarCotizacion();
    validarAntesDeEnviar();
});
