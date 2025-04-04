const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const Client = require('./Client');
const User = require('./User');

const Quotation = sequelize.define('Quotation', {
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

module.exports = Quotation;

document.addEventListener('DOMContentLoaded', function() {
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

    //calcular todos los valores
    function calcularTotales() {
        const precio = parseFloat(campos.precio.value) || 0;
        const cantidad = parseInt(campos.cantidad.value) || 0;
        const iva = parseFloat(campos.iva.value) || 0;

        //Calcular subtotal
        const subtotal = precio * cantidad;
        
        //Calcular montos de IVA
        const montoIva = subtotal * (iva / 100);
        
        //Calcular total
        const total = subtotal + montoIva;

        //Actualizar campos
        campos.subtotal.value = subtotal.toFixed(2);
        campos.montoIva.value = montoIva.toFixed(2);
        campos.total.value = total.toFixed(2);

        //Validar totales
        const submitBtn = form.querySelector('button[type="submit"]');
        if (total <= 0) {
            submitBtn.disabled = true;
            alert('El total debe ser mayor a cero');
        } else {
            submitBtn.disabled = false;
        }
    }

    ['cantidad', 'descuento'].forEach(field => {
        campos[field].addEventListener('input', calcularTotales);
    });

    //Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const total = parseFloat(campos.total.value);
        if (total <= 0) {
            alert('El total de la cotización debe ser mayor a cero');
            return false;
        }

        //Actualizar campos ocultos

        document.getElementById('subtotal_hidden').value = campos.subtotal.value;
        document.getElementById('montoIva_hidden').value = campos.montoIva.value;
        document.getElementById('montoDescuento_hidden').value = campos.montoDescuento.value;

        //Enviar formulario

        form.action = '/Cotizaciones/app/controllers/CotizacionesController.php';
        form.submit();
    });

    //Calcular totales iniciales
    calcularTotales();
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cotizacionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
            const montoIva = parseFloat(document.getElementById('montoIva').value) || 0;
            const montoDescuento = parseFloat(document.getElementById('montoDescuento').value) || 0;
            const total = parseFloat(document.getElementById('total').value) || 0;
            
            if (subtotal <= 0 || montoIva < 0 || total <= 0) {
                alert('Por favor verifique los montos. Deben ser valores numéricos válidos.');
                return false;
            }
            
       document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
            document.getElementById('montoIva_hidden').value = montoIva.toFixed(2);
            document.getElementById('montoDescuento_hidden').value = montoDescuento.toFixed(2);
            
            form.action = '/Cotizaciones/app/controllers/CotizacionesController.php';
            form.submit();
        });
    }
});
