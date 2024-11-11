<?php
//print_r($_POST);

$errorMsg = "";
$success = true;
$verify = "yes";
$token = "0";
$msg = "";

if (!isset($_POST['pwd'])) {
    $errorMsg .= "Password is require";
    $success = false;
} else {
    $pwd = sanitize_input($_POST['pwd']);
}

if (!isset($_POST['email'])) {
    $errorMsg .= "Password is require";
    $success = false;
} else {
    $email = sanitize_input($_POST['email']);
}

if ($success) {
    updatePassword();
} else {
    echo $errorMsg;
}

function updatePassword() {
    global $pwd, $token, $verify, $errorMsg, $success, $email, $msg;
    $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    // Check connection 

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("UPDATE member SET password =?, token_code = ?, verify = ? WHERE email = ?");
        // Bind & execute the query statement: 
        $stmt->bind_param("ssss", $pwd_hashed, $token, $verify, $email);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $success = false;
        } else {
            $msg = "Your password has been changed successfully.";
        }
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

        <?php if (!($success)) { ?>
            <main class='container' id='message'>
                <h4>Opps!</h4>
                <h4>The following input errors were detected:</h4>
                <p><?php echo " . $errorMsg . "; ?></p>
                <a href='process_forgot_password.php' role='button' class='btn btn-danger'>Back</a>

            </main>
        <?php } else { ?>
            <main class='container' id='message'>
                <h4>Update was successful</h4>
                <p><?php echo $msg; ?></p>
                <a href='login.php' role='button' class='btn btn-success'>Login</a>

            </main>
        <?php } ?>

        <?php include'footer.inc.php' ?>
    </body>
</html>
