<?php

require_once(__DIR__ . '/../models/Conexion.php');
require_once(__DIR__ . '/../models/Cotizacion.php');

class CotizacionesController {

    public function index() {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->query("SELECT * FROM cotizaciones");
        $cotizaciones = $query->fetchAll(PDO::FETCH_ASSOC);

        return $cotizaciones;
    }

    public function store($nombre, $descripcion, $precio) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->prepare("INSERT INTO cotizaciones (nombre, descripcion, precio) VALUES (:nombre, :descripcion, :precio)");
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':descripcion', $descripcion);
        $query->bindParam(':precio', $precio);
        $query->execute();
    }

    public function update($id, $nombre, $descripcion, $precio) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->prepare("UPDATE cotizaciones SET nombre = :nombre, descripcion = :descripcion, precio = :precio WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':descripcion', $descripcion);
        $query->bindParam(':precio', $precio);
        $query->execute();
    }

    public function destroy($id) {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->prepare("DELETE FROM cotizaciones WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
    }
}
?>

