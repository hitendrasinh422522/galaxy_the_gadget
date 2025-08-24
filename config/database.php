<?php
class Database {
    private $host = "localhost";
    private $dbname = "galaxy_gadget";  // must match your DB
    private $username = "root";
    private $password = "";
    private $conn;

    public function connect() {
        if ($this->conn == null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database Connection Failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
?>
