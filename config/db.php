<?php

//Recuerda crear un archivo "credenciales.php" en la carpeta config/ asi:
/* <?php
const HOST = "----";
const DB = "----";
const USER = "----";
const PASS = "----";
const PORT = "----"; */
//cambialo por los datos de tu bd
//credenciales.php no se sube al github por el .gitignore
//pato estuvo aqui

include 'credenciales.php';

class Database {
    private $host = HOST;
    private $db = DB;
    private $user = USER;
    private $pass = PASS;
    private $port = PORT;  
    private $conn = null;


    public function connect() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db}";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        return $pdo;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>
