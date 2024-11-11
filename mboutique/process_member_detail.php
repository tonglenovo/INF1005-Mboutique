<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errorMsg = "";
$success = true;
$name = "";
$address = "";
$setPassword = false;
$pwd_hashed = "";

if (empty($_POST['fname'])) {
    $success = false;
    $errorMsg = "First Name is required";
} else {
    $fname = sanitize_input($_POST['fname']);
    $name .= $fname . " ";
}

if (empty($_POST['lname'])) {
    $success = false;
    $errorMsg = "Last Name is required";
} else {
    $lname = sanitize_input($_POST['lname']);
    $name .= $lname;
}

if (empty($_POST['address1'])) {
    $success = false;
    $errorMsg = "Address1 is required";
} else {
    $address1 = sanitize_input($_POST['address1']);
}

if (empty($_POST['address2'])) {
    $success = false;
    $errorMsg = "Address2 is required";
} else {
    $address2 = sanitize_input($_POST['address2']);
}

if (empty($_POST['email'])) {
    $success = false;
    $errorMsg = "Email is required";
} else {
    $email = sanitize_input($_POST['email']);
}
if (empty($_POST['mem_id'])) {
    $success = false;
} else {
    $member_id = sanitize_input($_POST['mem_id']);
}

if (sanitize_input($_POST['pwd']) != sanitize_input($_POST['pwd_confirm'])) {
    $success = false;
    $errorMsg .= "Password does not match";
} else {
    if (sanitize_input($_POST['pwd']) == '' || sanitize_input($_POST['pwd_confirm']) == '') {
        $setPassword = false;
    } else {
        if (strlen(sanitize_input($_POST['pwd'])) < 8 || strlen(sanitize_input($_POST['pwd_confirm'])) < 8) {
            $errorMsg .= "Password must be min 8 length long";
            $success = false;
        } else {
            $pwd_hashed = password_hash(sanitize_input($_POST["pwd"]), PASSWORD_DEFAULT);
            $setPassword = true;
        }
    }
}
if (!getAddress($address1, $address2)) {
    $success = false;
}

if ($success) {
    if ($setPassword) {
        updateWithPassword();
    } else {
        updateWithoutPassword();
    }
}

function updateWithoutPassword() {
    global $address, $errorMsg, $success, $fname, $lname, $email, $member_id;
    // Create database connection. 
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    // Check connection 
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("UPDATE member SET fname = ?, lname = ?, email = ?, address = ? WHERE member_id = ?");
        // Bind & execute the query statement: 
        $stmt->bind_param("sssss", $fname, $lname, $email, $address, $member_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function updateWithPassword() {
    global $pwd_hashed, $address, $errorMsg, $success, $fname, $lname, $email, $member_id;
    // Create database connection. 
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    // Check connection 
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("UPDATE member SET fname = ?, lname = ?, email = ?, password = ?, address = ? WHERE member_id = ?");
        // Bind & execute the query statement: 
        $stmt->bind_param("ssssss", $fname, $lname, $email, $pwd_hashed, $address, $member_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function getAddress($postal_code, $address2) {
    global $errorMsg, $address;
    $url = "https://developers.onemap.sg/commonapi/search?searchVal=" . urlencode($postal_code) . "&returnGeom=Y&getAddrDetails=Y&pageNum=1";

    $data = file_get_contents($url);
    $results = json_decode($data);

    if ($results->found > 0) {
//    print_r($results);
        $address = "BLK " . $results->results[0]->BLK_NO . " " . $results->results[0]->ROAD_NAME . " " . $address2 . " SINGAPORE " . $results->results[0]->POSTAL;
        return true;
//    echo gettype($address);
    } else {
        $errorMsg .= "Address not found for postal code " . $postal_code;
        return false;
    }
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>

        <?php if ($success) { ?>
            <main class='container' id='message'>
                <h4>Member record update successful!</h4>
                <a href='index.php' role='button' class='btn btn-success'>Back to homepage</a>
            </main>
        <?php } else { ?>
            <main class='container' id='message'>
                <h4>Opps!</h4>
                <h4>The following input errors were detected:</h4>
                <p><?php echo " . $errorMsg . "; ?></p>
                <a href='viewMemberPage.php' role='button' class='btn btn-danger'>Return to Member Detail</a>
            </main>
        <?php } ?>
        <?php include'footer.inc.php' ?>
    </body>
</html>
