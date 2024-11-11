<?php

$errorMsg = "";

if (isset($_POST['qty'])) {
    $qty = $_POST['qty'];
}
if (isset($_POST['old_qty'])) {
    $old_qty = $_POST['old_qty'];
}
if (isset($_POST['size'])) {
    $size = $_POST['size'];
}
if (isset($_POST['old_size'])) {
    $old_size = $_POST['old_size'];
}
if (isset($_POST['color'])) {
    $color = $_POST['color'];
}
if (isset($_POST['old_color'])) {
    $old_color = $_POST['old_color'];
}
if (isset($_POST['m_id'])) {
    $m_id = $_POST['m_id'];
}
if (isset($_POST['clothing_id'])) {
    $clothing_id = $_POST['clothing_id'];
}

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
}

if (isset($_POST['o_price'])) {
    $o_price = $_POST['o_price'];
}

$temp_cart_id = 0;
$temp_qty = 0;
$temp_price = 0;
$org_price = 0;

if (checkDuplicate() == 1) {
    deleteCartForMerge();
    updateCartWithDelete();
    echo $errorMsg;
} else {
    if (($qty == $old_qty) && ($color == $old_color) && ($size == $old_size)) {
        echo "NO CHANGE";
    } else {
        updateCartNormal();
        echo $errorMsg;
    }
}

function checkDuplicate() {
    global $errorMsg, $temp_cart_id, $size, $color, $clothing_id, $cart_id, $temp_price, $temp_qty, $m_id;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("SELECT cart.*, clothing.*  FROM cart, clothing WHERE cart.clothing_id = clothing.clothing_id AND size_id = ? AND color_id = ? AND cart.clothing_id = ? AND cart.cart_id != ? AND cart.member_id = ?");
        $stmt->bind_param("sssss", $size, $color, $clothing_id, $cart_id, $m_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $temp_cart_id = $row['cart_id'];
            $temp_price = $row['total_price'];
            $temp_qty = $row['qty'];
            return 1;
        } else {
            return 0;
        }

        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $errorMsg = "";
        }
        $stmt->close();
    }
    $conn->close();
    return 0;
}

function deleteCartForMerge() {
    global $errorMsg, $cart_id;

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
            $errorMsg = "Delete Success";
        }
        $stmt->close();
    }
    $conn->close();
}

function updateCartWithDelete() {
    global $errorMsg, $temp_cart_id, $o_price, $qty, $temp_price, $temp_qty, $size, $color, $clothing_id, $m_id, $cart_id;
    $final_price = $temp_price + ($o_price * $qty);
    $final_qty = $temp_qty + $qty;

    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("UPDATE cart set size_id = ?, color_id = ?, qty = ?, total_price = ? WHERE clothing_id = ? AND cart_id = ? AND member_id = ?");
        $stmt->bind_param("sssssss", $size, $color, $final_qty, $final_price, $clothing_id, $temp_cart_id, $m_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $errorMsg = "Update Success";
        }
        $stmt->close();
    }
    $conn->close();
}

function updateCartNormal() {
    global $errorMsg, $old_qty, $o_price, $qty, $size, $color, $clothing_id, $m_id, $cart_id;
    $final_price = $o_price * $qty;

    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("UPDATE cart set size_id = ?, color_id = ?, qty = ?, total_price = ? WHERE clothing_id = ? AND cart_id = ? AND member_id = ?");
        $stmt->bind_param("sssssss", $size, $color, $qty, $final_price, $clothing_id, $cart_id, $m_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            $errorMsg = "Update successfully";
        }
        $stmt->close();
    }
    $conn->close();
}

//function check_update($new_qty, $old_qty) {
//
//
//    return $new_qty > $old_qty;
//}
//function check_update_size($new_qty, $old_qty) {
//    if (check_update($new_qty, $old_qty)) {
//        return $new_qty - $old_qty;
//    }
//
//    return 0;
//}
//function check_delete($new_qty, $old_qty) {
//
//
//    return $new_qty < $old_qty;
//}
//function check_delete_size($new_qty, $old_qty) {
//    if (check_delete($new_qty, $old_qty)) {
//        return $old_qty - $new_qty;
//    }
//
//    return 0;
//}
//function check_changes($new_qty, $old_qty) {
//    if (check_update($new_qty, $old_qty) || check_delete($new_qty, $old_qty)) {
//        echo "got changes\n";
//
//        $check_qty = 0;
//        if (check_update_size($new_qty, $old_qty)) {
//            $check_qty = check_update_size($new_qty, $old_qty);
//        }
//        if (check_delete_size($new_qty, $old_qty)) {
//            $check_qty = check_delete_size($new_qty, $old_qty);
//        }
//
//        echo $check_qty . "\n";
//    } else {
//        echo "no changes\n";
//    }
//}
//function checkChanges($newChange, $oldChange) {
//    if ($newChange != $oldChange) {
//        return 1;
//    } else {
//        return 0;
//    }
//    return 0;
//}
//function addCart() {
//    global $errorMsg, $m_id, $clothing_id, $old_size, $old_color;
//    include '../dbinfo.php';
//    $conn = new mysqli($config['servername'], $config['username'],
//            $config['password'], $config['dbname']);
//    if ($conn->connect_error) {
//        $errorMsg = "Connection failed: " . $conn->connect_error;
//    } else {
//        $stmt = $conn->prepare("INSERT INTO cart (member_id,clothing_id,size_id,color_id) VALUES (?,?,?,?)");
//        $stmt->bind_param("ssss", $m_id, $clothing_id, $old_size, $old_color);
//        if (!$stmt->execute()) {
//            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        } else {
//            $errorMsg = "";
//        }
//        $stmt->close();
//    }
//    $conn->close();
//}
//function deleteCart() {
//    global $errorMsg, $m_id, $clothing_id, $delete_size;
//    include '../dbinfo.php';
//    $conn = new mysqli($config['servername'], $config['username'],
//            $config['password'], $config['dbname']);
//    if ($conn->connect_error) {
//        $errorMsg = "Connection failed: " . $conn->connect_error;
//    } else {
//        $stmt = $conn->prepare("DELETE FROM cart WHERE member_id = ? AND clothing_id = ? LIMIT " . $delete_size);
//        $stmt->bind_param("ss", $m_id, $clothing_id);
//        if (!$stmt->execute()) {
//            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        } else {
//            $errorMsg = "";
//        }
//        $stmt->close();
//    }
//    $conn->close();
//}
//function updateCartSize() {
//    global $errorMsg, $size, $m_id, $clothing_id;
//    include '../dbinfo.php';
//    $conn = new mysqli($config['servername'], $config['username'],
//            $config['password'], $config['dbname']);
//    if ($conn->connect_error) {
//        $errorMsg = "Connection failed: " . $conn->connect_error;
//    } else {
//        $stmt = $conn->prepare("UPDATE cart set size_id = ? WHERE member_id = ? AND clothing_id = ?");
//        $stmt->bind_param("sss", $size, $m_id, $clothing_id);
//        if (!$stmt->execute()) {
//            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        } else {
//            $errorMsg = "";
//        }
//        $stmt->close();
//    }
//    $conn->close();
//}
//function updateCartColor() {
//    global $errorMsg, $color, $m_id, $clothing_id;
//    include '../dbinfo.php';
//    $conn = new mysqli($config['servername'], $config['username'],
//            $config['password'], $config['dbname']);
//    if ($conn->connect_error) {
//        $errorMsg = "Connection failed: " . $conn->connect_error;
//    } else {
//        $stmt = $conn->prepare("UPDATE cart set color_id = ? WHERE member_id = ? AND clothing_id = ?");
//        $stmt->bind_param("sss", $color, $m_id, $clothing_id);
//        if (!$stmt->execute()) {
//            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        } else {
//            $errorMsg = "";
//        }
//        $stmt->close();
//    }
//    $conn->close();
//}
?>