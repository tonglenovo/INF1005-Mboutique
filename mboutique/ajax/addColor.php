<?php

if (isset($_POST['color'])) {
    $color = $_POST['color'];
}
$color = ucfirst(strtolower($color));

$errorMsg = "";
$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
} else {
    $stmt = $conn->prepare("SELECT * FROM color WHERE color_name= ? ");
    // Bind & execute the query statement: 
    $stmt->bind_param("s", $color);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // error
        $errorMsg = "Already have the color";
    } else {
        // add to database
        $stmt = $conn->prepare("INSERT INTO color (color_name) VALUES (?)");
        // Bind & execute the query statement: 
        $stmt->bind_param("s", $color);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        } else {
            $errorMsg = "Add color Successfully";
        }
    }
    $stmt->close();
}
$conn->close();
echo $errorMsg;
?>
