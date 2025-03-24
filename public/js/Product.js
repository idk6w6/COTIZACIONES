const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Product = sequelize.define('Product', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
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
