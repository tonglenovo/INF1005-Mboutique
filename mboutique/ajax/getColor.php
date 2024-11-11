<?php

$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
//    $success = false;
} else {
    // Prepare the statement: 
    // SELECT * FROM clothing c, clothing_size cs WHERE c.clothing_id = cs.clothing_id AND c.clothing_id = 19
//        $stmt = $conn->prepare("SELECT * FROM clothing WHERE clothing_id = ?");
    $stmt = $conn->prepare("SELECT * FROM color");
    // Bind & execute the query statement: 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $colors[] = $row;
        }
    }
    echo json_encode($colors);
    $stmt->close();
}
$conn->close();
?>