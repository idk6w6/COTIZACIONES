<?php
class Database {
    private $host = "localhost";
    private $db = "gestor_cotizacion";
    private $user = "postgres";
    private $pass = "Luh1234567";
    private $port = "5432"; // Add PostgreSQL port

    public function connect() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db}";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception('Error de conexión a la base de datos');
        }
    }
}
?>
