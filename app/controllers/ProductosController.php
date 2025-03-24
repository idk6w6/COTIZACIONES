<?php

require_once(__DIR__ . '/../models/Conexion.php');
require_once(__DIR__ . '/../models/Producto.php');

class ProductosController {

    public function index() {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->query("SELECT * FROM productos");
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);

        return $productos;
    }

    public function store($nombre, $descripcion, $precio) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->prepare("INSERT INTO productos (nombre, descripcion, precio) VALUES (:nombre, :descripcion, :precio)");
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':descripcion', $descripcion);
        $query->bindParam(':precio', $precio);
        $query->execute();
    }

    public function update($id, $nombre, $descripcion, $precio) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->prepare("UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':descripcion', $descripcion);
        $query->bindParam(':precio', $precio);
        $query->execute();
    }

    public function destroy($id) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->prepare("DELETE FROM productos WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
    }
}
?>

