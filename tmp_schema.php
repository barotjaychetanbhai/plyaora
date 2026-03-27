<?php
require 'c:\xampp\htdocs\play\player\includes\db.php';
$res = $conn->query("DESCRIBE turfs");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
