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
