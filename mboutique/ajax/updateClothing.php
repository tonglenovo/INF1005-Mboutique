<?php

$errorMsg = "";
$countSize = 0;
$countColor = 0;
$success = true;

if (isset($_FILES['e_image'])) {
    $e_image = $_FILES['e_image'];
}

if (isset($_POST['e_image_hidden'])) {
    $e_image_hidden = $_POST['e_image_hidden'];
}

if (isset($_POST['e_id'])) {
    $e_id = $_POST['e_id'];
}
if (isset($_POST['e_name'])) {
    $e_name = $_POST['e_name'];
}
if (isset($_POST['e_price'])) {
    $e_price = $_POST['e_price'];
}
if (isset($_POST['e_desc'])) {
    $e_desc = $_POST['e_desc'];
}
if (isset($_POST['e_type'])) {
    $e_type = $_POST['e_type'];
}

//if (isset($_POST['e_color'])) {
//    $e_color = $_POST['e_color'];
//}

if (isset($_POST['e_size_value'])) {
    $e_size_value = $_POST['e_size_value'];
}

if (isset($_POST['e_size_value_hidden'])) {
    $e_size_value_hidden = $_POST['e_size_value_hidden'];
}

if (isset($_POST['e_color_value'])) {
    $e_color_value = $_POST['e_color_value'];
}

if (isset($_POST['e_color_value_hidden'])) {
    $e_color_value_hidden = $_POST['e_color_value_hidden'];
}
$size_id_array = explode(",", $e_size_value);
$size_id_array_hidden = explode(",", $e_size_value_hidden);
$color_id_array = explode(",", $e_color_value);
$color_id_array_hidden = explode(",", $e_color_value_hidden);

$differencesSize = array_diff($size_id_array_hidden, $size_id_array);
$differencesColor = array_diff($color_id_array_hidden, $color_id_array);
$differencesSize1 = array_diff($size_id_array, $size_id_array_hidden);
$differencesColor1 = array_diff($color_id_array, $color_id_array_hidden);

if (count($differencesSize) == 0) {
    if (count($differencesSize1) == 0) {
        $countSize = 0;
    } else {
        $countSize = -1;
    }
} else if (count($differencesSize) > 0) {
    $countSize = -1;
}

if (count($differencesColor) == 0) {
    if (count($differencesSize1) == 0) {
        $countColor = 0;
    } else {
        $countColor = -1;
    }
} else if (count($differencesColor) > 0) {
    $countColor = -1;
}

//if e_image not empty mean need to change photo;
if (!empty($e_image)) {
    $image_path = '../images/' . $e_image_hidden;
    unlink($image_path);

    $target_dir = "../images/";
    $target_file = $target_dir . basename($e_image['name']);
    move_uploaded_file($e_image['tmp_name'], $target_file);
    $imgName = basename($e_image['name']);
    editWithPicture();
    echo $errorMsg;
} else {
    editWithoutPicture();
    echo $errorMsg;
}

function editWithPicture() {
    global $config, $e_name, $e_desc, $e_type, $imgName, $e_price, $e_id, $countSize, $countColor, $errorMsg;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
//    $success = false;
    } else {
        // Prepare the statement: 
        $stmt = $conn->prepare("UPDATE clothing 
    SET 
        clothing_title = ?, 
        clothing_description = ?, 
        clothing_type = ?, 
        clothing_image = ?, 
        clothing_price = ?
            WHERE clothing_id = ?");
        // Bind & execute the query statement: 
        $stmt->bind_param("ssssss", $e_name, $e_desc, $e_type, $imgName, $e_price, $e_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        $success = false;
        } else {
            if ($countSize == -1) {
                // delete all size option
                deleteSize();
                // re-add
                reAddSize();
            }
            if ($countColor == -1) {
                // delete all size option
                deleteColor();
                // re-add
                reAddColor();
            }
            $errorMsg = "Update clothing success";
        }
        $stmt->close();
    }
    $conn->close();
}

function deleteSize() {
    global $config, $e_id, $errorMsg, $success;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("DELETE FROM clothing_size WHERE clothing_id = ?");
        $stmt->bind_param("s", $e_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        } else {
            $errorMsg = "Create Success";
        }
        $stmt->close();
    }
    $conn->close();
}

function reAddSize() {
    global $config, $e_id, $errorMsg, $success, $size_id_array;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // add all size back
        foreach ($size_id_array as $sizeID) {
            // Code block to be executed for each size ID
//            echo $sizeID . "<br/>";
            $stmt = $conn->prepare("INSERT INTO clothing_size (clothing_id, size_id) VALUES(?,?)");
            // Bind & execute the query statement: 
            $stmt->bind_param("ss", $e_id, $sizeID);
            $stmt->execute();
        }
        $stmt->close();
    }
    $conn->close();
}

function editWithoutPicture() {
    global $config, $e_name, $e_desc, $e_type, $e_image_hidden, $e_price, $e_id, $errorMsg, $countSize, $countColor;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
//    $success = false;
    } else {
        // Prepare the statement: 
        $stmt = $conn->prepare("UPDATE clothing 
SET 
    clothing_title = ?, 
    clothing_description = ?, 
    clothing_type = ?, 
    clothing_image = ?, 
    clothing_price = ?
WHERE clothing_id = ?");
        // Bind & execute the query statement: 
        $stmt->bind_param("ssssss", $e_name, $e_desc, $e_type, $e_image_hidden, $e_price, $e_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        $success = false;
        } else {
            if ($countSize == -1) {
                // delete all size option
                deleteSize();
                // re-add
                reAddSize();
            }
            if ($countColor == -1) {
                // delete all size option
                deleteColor();
                // re-add
                reAddColor();
            }
            $errorMsg = "Update Success";
        }
        $stmt->close();
    }
    $conn->close();
}

function deleteColor() {
    global $config, $e_id, $errorMsg, $success;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("DELETE FROM clothing_color WHERE clothing_id = ?");
        $stmt->bind_param("s", $e_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        } else {
            $errorMsg = "delete Success";
        }
        $stmt->close();
    }
    $conn->close();
}

function reAddColor() {
    global $config, $e_id, $errorMsg, $success, $color_id_array;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // add all size back
        foreach ($color_id_array as $colorID) {
            // Code block to be executed for each size ID
//            echo $sizeID . "<br/>";
            $stmt = $conn->prepare("INSERT INTO clothing_color (clothing_id, color_id) VALUES(?,?)");
            // Bind & execute the query statement: 
            $stmt->bind_param("ss", $e_id, $colorID);
            $stmt->execute();
        }
        $stmt->close();
    }
    $conn->close();
}

?>
