<?php

$errorMsg = "";
$success = true;
$payment_statue = 'not_yet';

if (isset($_POST['c_id'])) {
    $clothing_id = sanitize_input($_POST['c_id']);
}

if (isset($_POST['m_id'])) {
    $member_id = sanitize_input($_POST['m_id']);
}

if($success){
    checkClothing();
} else{
    echo $errorMsg;
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkClothing() {
    global $errorMsg, $success, $clothing_id,$member_id,$payment_statue;
    // Create database connection. 
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT * FROM cart WHERE clothing_id = ? AND member_id = ? AND payment_status=?");
        // Bind & execute the query statement:
        $stmt->bind_param("sss", $clothing_id, $member_id, $payment_statue);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "exists";
            $success = true;
        } else {
            echo "not_exists";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

?>