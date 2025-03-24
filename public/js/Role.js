const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Role = sequelize.define('Role', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    tipo: {
        type: DataTypes.STRING(50),
        unique: true
    }
}, {
    tableName: 'roles',
    timestamps: false
});

module.exports = Role;
