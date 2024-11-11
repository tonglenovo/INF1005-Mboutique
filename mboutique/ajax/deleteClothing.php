<?php

if (isset($_POST['d_id'])) {
    $id = $_POST['d_id'];
}

if (isset($_POST['d_img'])) {
    $d_img = $_POST['d_img'];
}

$errorMsg = "";

$config = parse_ini_file('../../../private/db-config.ini');

$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
} else {
    if (!empty($id)) { // check if $id is not empty
        deleteClothing();
        deleteSize();
        deleteCart();
        deleteColor();
    } else {
        $errorMsg = "ID is empty";
    }
}

$conn->close();
echo $errorMsg;

function deleteClothing() {
    global $conn, $errorMsg, $id, $d_img;
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("DELETE FROM clothing WHERE clothing_id=?");
        $stmt->bind_param("s", $id); // specify data type as "i" for integer
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $image_path = '../images/' . $d_img;
            unlink($image_path);
            $errorMsg = "Delete success";
        }
        $stmt->close();
    }
}

function deleteSize() {
    global $conn, $errorMsg, $id;
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("DELETE FROM clothing_size WHERE clothing_id=?");
        $stmt->bind_param("s", $id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $errorMsg = "Delete Success";
        }
        $stmt->close();
    }
}

function deleteColor() {
    global $conn, $errorMsg, $id;
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("DELETE FROM clothing_color WHERE clothing_id=?");
        $stmt->bind_param("s", $id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $errorMsg = "Delete Success";
        }
        $stmt->close();
    }
}

function deleteCart() {
    global $conn, $errorMsg, $id;
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("DELETE FROM cart WHERE clothing_id=?");
        $stmt->bind_param("s", $id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $errorMsg = "Clothing delete success";
        }
        $stmt->close();
    }
}

?>