const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Client = sequelize.define('Client', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    clave: {
        type: DataTypes.STRING(50)
    },
    nombre: {
        type: DataTypes.STRING(100)
    },
    celular1: {
        type: DataTypes.STRING(20)
    },
    tel_oficina: {
        type: DataTypes.STRING(20)
    },
    correo: {
        type: DataTypes.STRING(100)
    },
    direccion: {
        type: DataTypes.TEXT
    }
}, {
    tableName: 'clientes',
    timestamps: false
});

module.exports = Client;


