<?php

if (isset($_POST['id'])) {
    $id = $_POST['id'];
}

$errorMsg = "";
$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
} else {
    $stmt = $conn->prepare("DELETE FROM color WHERE color_id = ? ");
    // Bind & execute the query statement: 
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        $stmt = $conn->prepare("DELETE FROM clothing_color WHERE color_id = ?");
        // Bind & execute the query statement: 
        $stmt->bind_param("s", $id);
        $errorMsg = "Delete Color Successfully";
        if ($stmt->execute()) {
            $errorMsg = "Delete Color Successfully";
        }
    }
    $stmt->close();
}
$conn->close();
echo $errorMsg;
?>
