const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Product = sequelize.define('Product', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    clave: {
        type: DataTypes.STRING(50),
        allowNull: false,
        unique: true
    },
    nombre_producto: {
        type: DataTypes.STRING(100)
    },
    descripcion: {
        type: DataTypes.TEXT
    },
    precio: {
        type: DataTypes.DECIMAL(10, 2)
    },
    iva: {
        type: DataTypes.DECIMAL(5, 2)
    },
    unidad_medida_id: {
        type: DataTypes.INTEGER
    },
    unidad_peso: {
        type: DataTypes.DECIMAL(10, 2)
    },
    metodo_costeo_id: {
        type: DataTypes.INTEGER
    }
}, {
    tableName: 'productos',
    timestamps: false
});

module.exports = Product;

// Client-side validation and functionality
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

    if (precio < 0) {
        e.preventDefault();
        alert('El precio no puede ser menor a 0');
        return false;
    }

    // Validar tasas de IVA mexicanas
    const tasasValidas = [0, 8, 16];
    if (!tasasValidas.includes(iva)) {
        e.preventDefault();
        alert('La tasa de IVA debe ser 0% (Exento), 8% (Fronterizo) o 16% (General)');
        return false;
    }

    if (peso < 0) {
        e.preventDefault();
        alert('El peso no puede ser menor a 0');
        return false;
    }

    return true;
}

function editarProducto(id) {
    // Implement edit functionality
    fetch(`/Cotizaciones/app/controllers/ProductosController.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Fill form with product data
            document.getElementById('clave').value = data.clave;
            document.getElementById('nombre_producto').value = data.nombre_producto;
            document.getElementById('descripcion').value = data.descripcion;
            document.getElementById('precio').value = data.precio;
            document.getElementById('iva').value = data.iva;
            document.getElementById('unidad_medida_id').value = data.unidad_medida_id;
            document.getElementById('unidad_peso').value = data.unidad_peso;
            document.getElementById('metodo_costeo_id').value = data.metodo_costeo_id;
            
            // Change form action to update
            document.getElementById('productoForm').action = '/Cotizaciones/app/controllers/ProductosController.php';
            document.querySelector('input[name="action"]').value = 'update';
            
            // Add hidden input for product id
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = 'id';
            hiddenId.value = id;
            document.getElementById('productoForm').appendChild(hiddenId);
        });
}

function eliminarProducto(id) {
    if (confirm('¿Está seguro de que desea eliminar este producto?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/Cotizaciones/app/controllers/ProductosController.php';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;

        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}
