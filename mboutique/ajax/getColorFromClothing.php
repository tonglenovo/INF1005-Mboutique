<?php

if (isset($_POST['id'])) {
    $id = $_POST['id'];
}

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
    $stmt = $conn->prepare("SELECT co.* FROM clothing c, clothing_color cc, color co WHERE c.clothing_id = cc.clothing_id AND co.color_id = cc.color_id AND c.clothing_id = ?");
    $stmt->bind_param("s", $id);
    // Bind & execute the query statement: 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clothing[] = $row;
        }
    }
    $stmt->close();
    echo json_encode($clothing);
}


$conn->close();
?>