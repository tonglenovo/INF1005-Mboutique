<?php

if (isset($_POST['c_id'])) {
    $c_id = $_POST['c_id'];
}

if (isset($_POST['size_id'])) {
    $s_id = $_POST['size_id'];
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
    $stmt = $conn->prepare("SELECT clothing.*, count(*) AS qty, size.size_name, clothing.clothing_price * count(*) AS total_price, cart.* FROM cart, clothing, size WHERE cart.clothing_id = clothing.clothing_id AND cart.size_id = size.size_id AND cart.clothing_id = ? AND cart.size_id = ? GROUP BY cart.clothing_id,cart.size_id");
    $stmt->bind_param("ss", $c_id,$s_id );
    // Bind & execute the query statement: 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    echo json_encode($row);
}
$stmt->close();

$conn->close();
?>