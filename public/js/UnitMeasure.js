const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const UnitMeasure = sequelize.define('UnitMeasure', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    descripcion: {
        type: DataTypes.STRING(50)
    }
}, {
    tableName: 'unidades_medida',
    timestamps: false
});

module.exports = UnitMeasure;
