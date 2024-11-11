<?php

$delivery_status = "Shipping";
$errorMsg = "";
$success = true;
if (empty($_POST['payment_id'])) {
    $errorMsg = "Payment id is require";
    $success = false;
} else {
    $payment_id = $_POST['payment_id'];
}

$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
    $success = false;
} else {
    $stmt = $conn->prepare("UPDATE payment SET delivery_status = ? WHERE payment_id=?");
    $stmt->bind_param("ss", $delivery_status, $payment_id);
    if (!$stmt->execute()) {
        $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $success = false;
    }

    $stmt->close();
}

$conn->close();
if($success){
    echo "Delivery updated";
} else {
    echo $errorMsg;
}
?>