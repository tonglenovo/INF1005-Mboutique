<?php

if (isset($_POST['c_id'])) {
    $c_id = $_POST['c_id'];
}

$config = parse_ini_file('../../../private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
//    $success = false;
} else {
    // Prepare the statement: 
    $stmt = $conn->prepare("SELECT cs.*, s.size_name FROM `clothing_size`cs, size s WHERE cs.size_id = s.size_id and cs.clothing_id = ?");
    $stmt->bind_param("s", $c_id);
    // Bind & execute the query statement: 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sizing[] = $row;
        }
        $stmt = $conn->prepare("SELECT cc.*, c.color_name FROM `clothing_color`cc, color c WHERE cc.color_id = c.color_id and cc.clothing_id = ?");
        $stmt->bind_param("s", $c_id);
        // Bind & execute the query statement: 
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $colors[] = $row;
        }
        // Merge the arrays
        $mergedArray = array(
            'sizing' => $sizing,
            'colors' => $colors
        );
    }

    echo json_encode($mergedArray);
}
$stmt->close();

$conn->close();
?>