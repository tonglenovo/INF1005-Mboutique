<?php

if (isset($_POST['c_id'])) {
    $c_id = $_POST['c_id'];
}

if (isset($_POST['size_id'])) {
    $s_id = $_POST['size_id'];
}

if (isset($_POST['color_id'])) {
    $color_id = $_POST['color_id'];
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
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $colors[] = $row;
            }
            // Prepare the statement:
            //$stmt = $conn->prepare("SELECT clothing.*, count(*) AS qty, size.size_name, clothing.clothing_price * count(*) AS total_price, cart.*, color.color_name FROM cart, clothing, size, color WHERE cart.clothing_id = clothing.clothing_id AND cart.size_id = size.size_id AND cart.color_id = color.color_id AND cart.member_id = ? GROUP BY cart.clothing_id,cart.size_id");
            $stmt = $conn->prepare("SELECT clothing.*, size.size_name, cart.* FROM cart, clothing, size WHERE cart.clothing_id = clothing.clothing_id AND cart.size_id = size.size_id AND cart.clothing_id = ? AND cart.size_id = ? AND cart.color_id = ?");
            $stmt->bind_param("sss", $c_id, $s_id, $color_id);
            // Bind & execute the query statement:
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Note that email field is unique, so should only have
                // one row in the result set.
                $row = $result->fetch_assoc();
            }
        }

        // Merge the arrays
        $mergedArray = array(
            'sizing' => $sizing,
            'colors' => $colors,
            'clothing' => $row
        );
    }

    echo json_encode($mergedArray);
}
$stmt->close();

$conn->close();
?>