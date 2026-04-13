<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "playora";
    public $conn;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        $this->conn = null;
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
            $this->conn->set_charset("utf8mb4");
        } catch(Exception $e) {
            die("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
$db = new Database();
$conn = $db->conn;
?>
