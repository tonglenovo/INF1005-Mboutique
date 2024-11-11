<?php

//print_r($_POST);
$newId = "";
$success = true;
$errorMsg = "";

if (isset($_POST['c_name'])) {
    //$c_name = sanitize_input($_POST['c_name']);
    $c_name = $_POST['c_name'];
}
if (isset($_POST['c_desc'])) {
//    $c_desc = sanitize_input($_POST['c_desc']);
    $c_desc = $_POST['c_desc'];
}

if (isset($_POST['c_type'])) {
//    $c_size = sanitize_input($_POST['c_size']);
    $c_type = $_POST['c_type'];
}

if (isset($_POST['c_price'])) {
//    $c_price = sanitize_input($_POST['c_price']);
    $c_price = $_POST['c_price'];
}
if (isset($_FILES['c_image'])) {
//    $c_image = sanitize_input($_FILES['c_image']);
    $c_image = $_FILES['c_image'];
}

if (isset($_POST['c_color'])) {
//    $c_color = sanitize_input($_POST['c_color']);
    $c_color = $_POST['c_color'];
}
if (isset($_POST['c_size_value'])) {
    $c_size_value = $_POST['c_size_value'];
}
$size_id_array = explode(",", $c_size_value);
$color_id_array = explode(",", $c_color);

//print_r($size_id_array);
addDetail();
addSize();
addColor();


if($success){
    echo "Clothing create success";
} else {
    echo $errorMsg;
}


function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//echo $errorMsg;

function addDetail() {
    global $c_name, $c_desc, $c_type, $c_image, $imgName, $c_price, $newId, $errorMsg, $config, $success;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

    //// Handle the uploaded image file
    $target_dir = "../images/";
    $target_file = $target_dir . basename($c_image['name']);
    move_uploaded_file($c_image['tmp_name'], $target_file);

    $imgName = basename($c_image['name']);

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement: 
        $stmt = $conn->prepare("INSERT INTO clothing (clothing_title, clothing_description, clothing_type, clothing_image, clothing_price) VALUES (?, ?, ?, ?, ?)");
        // Bind & execute the query statement: 
        $stmt->bind_param("sssss", $c_name, $c_desc, $c_type, $imgName, $c_price);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        } else {
            $newId = $stmt->insert_id; // Get the ID of the newly inserted record
            $errorMsg = "Create Success";
        }
        $stmt->close();
    }
    $conn->close();
}

function addSize() {
    global $newId, $size_id_array, $errorMsg, $conn;
    
   $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    
    foreach ($size_id_array as $sizeID) {
        // Code block to be executed for each size ID
//            echo $sizeID . "<br/>";
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            $stmt = $conn->prepare("INSERT INTO clothing_size (clothing_id, size_id) VALUES(?,?)");
            // Bind & execute the query statement: 
            $stmt->bind_param("ss", $newId, $sizeID);
            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
            } else {
                $errorMsg = "Create Success";
            }
            $stmt->close();
        }
    }
    $conn->close();
}

function addColor() {
    global $newId, $color_id_array, $errorMsg, $conn;
    
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    
    foreach ($color_id_array as $colorID) {
        // Code block to be executed for each size ID
//            echo $sizeID . "<br/>";
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            $stmt = $conn->prepare("INSERT INTO clothing_color (clothing_id, color_id) VALUES(?,?)");
            // Bind & execute the query statement: 
            $stmt->bind_param("ss", $newId, $colorID);
            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
            } else {
                $errorMsg = "Clothing create successfully";
            }
            $stmt->close();
        }
    }
    $conn->close();
}

?>