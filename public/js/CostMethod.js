const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const CostMethod = sequelize.define('CostMethod', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    descripcion: {
        type: DataTypes.STRING(50)
    }
}, {
    tableName: 'metodos_costeo',
    timestamps: false
});

module.exports = CostMethod;
