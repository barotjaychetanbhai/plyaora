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

// Dynamic System: Auto-complete bookings after the slot time has passed
$current_time = date('Y-m-d H:i:s');
// Extract the end time from the time_slot (e.g., "10:00 AM - 11:00 AM" -> "11:00 AM")
// This is a simplified approach, we update bookings where date is older or date is today and time has passed
$conn->query("
    UPDATE bookings 
    SET status = 'completed' 
    WHERE status = 'confirmed' 
    AND (
        booking_date < CURRENT_DATE 
        OR (booking_date = CURRENT_DATE AND STR_TO_DATE(SUBSTRING_INDEX(time_slot, ' - ', -1), '%h:%i %p') < CURRENT_TIME)
    )
");
?>
