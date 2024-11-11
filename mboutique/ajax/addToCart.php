<?php

$success = true;
$errorMsg = "";
$payment_status = "not_yet";
$payment_id = 0;
if (isset($_POST['c_id'])) {
    $cid = $_POST['c_id'];
}

if (isset($_POST['m_id'])) {
    $mid = $_POST['m_id'];
}

if (isset($_POST['size_id'])) {
    $size_id = $_POST['size_id'];
}

if (isset($_POST['quantity'])) {
    $quantity = $_POST['quantity'];
}
if (isset($_POST['color_id'])) {
    $color_id = $_POST['color_id'];
}

if (isset($_POST['price'])) {
    $price = $_POST['price'];
}

//print_r($_POST);

$totalPrice = $price * $quantity;
//echo $totalPrice;


$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
    $success = false;
} else {
    $stmt = $conn->prepare("INSERT INTO cart (member_id,clothing_id,size_id,color_id,total_price,qty,payment_id,payment_status) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssss", $mid, $cid, $size_id, $color_id, $totalPrice, $quantity, $payment_id, $payment_status);
    if (!$stmt->execute()) {
        $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $success = false;
    }

    if ($success) {
        echo "Added to cart";
    } else {
        echo $errorMsg;
    }
}
?>