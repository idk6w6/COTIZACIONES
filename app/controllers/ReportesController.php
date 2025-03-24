<?php

require_once(__DIR__ . '/../models/Conexion.php');

class ReportesController {

    public function generarReporte() {
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        // Aquí puedes agregar la lógica para generar el reporte
        $query = $db->query("SELECT * FROM cotizaciones");
        $cotizaciones = $query->fetchAll(PDO::FETCH_ASSOC);

        // Puedes devolver los datos del reporte a una vista o exportarlos a un archivo
        return $cotizaciones;
    }
}
?>

