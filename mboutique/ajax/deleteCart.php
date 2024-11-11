<?php

$errorMsg = "";
if (isset($_POST['m_id'])) {
    $m_id = $_POST['m_id'];
}

if (isset($_POST['clothing_id'])) {
    $clothing_id = $_POST['clothing_id'];
}

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
}

$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);
if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
} else {
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->bind_param("s", $cart_id);
    if (!$stmt->execute()) {
        $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    } else {
        $errorMsg = "Delete Successfully";
    }
    $stmt->close();
}
$conn->close();
echo $errorMsg;
?>