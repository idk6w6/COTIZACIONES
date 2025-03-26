<?php

require_once(__DIR__ . '/../models/Conexion.php');

class ReportesController {

    public function generarReporte() {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        $query = $db->query("SELECT * FROM cotizaciones");
        $cotizaciones = $query->fetchAll(PDO::FETCH_ASSOC);

        return $cotizaciones;
    }
}
?>

