<?php
// Database configuration for the refactored Playora structure
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'playora_v2');

class Database {
    public $conn;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        $this->conn = null;
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $this->conn->set_charset("utf8mb4");
        } catch(mysqli_sql_exception $e) {
            // For production, log the error rather than displaying it
            die("Database connection failed. Please try again later.");
        }
        return $this->conn;
    }
}

// Instantiate and provide a global $conn variable
$db = new Database();
$conn = $db->conn;
?>