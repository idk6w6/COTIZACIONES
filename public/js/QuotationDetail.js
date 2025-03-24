const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const Quotation = require('./Quotation');
const Product = require('./Product');

const QuotationDetail = sequelize.define('QuotationDetail', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    cotizacion_id: {
        type: DataTypes.INTEGER,
        references: {
            model: Quotation,
            key: 'id'
        }
    },
    producto_id: {
        type: DataTypes.INTEGER,
        references: {
            model: Product,
            key: 'id'
        }
    },
    cantidad: {
        type: DataTypes.INTEGER
    },
    precio: {
        type: DataTypes.DECIMAL(10, 2)
    },
    iva: {
        type: DataTypes.DECIMAL(5, 2)
    },
    descuento: {
        type: DataTypes.DECIMAL(10, 2)
    },
    neto: {
        type: DataTypes.DECIMAL(10, 2)
    },
    total: {
        type: DataTypes.DECIMAL(10, 2)
    }
}, {
    tableName: 'detalles_cotizacion',
    timestamps: false
});

module.exports = QuotationDetail;
