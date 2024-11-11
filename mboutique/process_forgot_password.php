<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();

    $success = true;
    $errorMsg = "";

    if (isset($_SESSION['loggedIn'])) {
        if ($_SESSION['loggedIn'] != '') {
            $errorMsg = "You are not allow to view this page";
            $success = false;
        }
    }
}


$verify = 'no';
$errorMsg = "";
$success = true;
$row = [];
$tokenCode = generateToken();

if (!empty($_POST['email'])) {
    $email = $_POST['email'];
} else {
    $errorMsg .= "Email is require";
    $success = false;
}

if ($success) {
    if (checkEmailIsOnDatabase()) {
        sentEmailWithToken();
        updateTokenToDB();
    }
}

function checkEmailIsOnDatabase() {
    global $row, $email, $errorMsg, $success;
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
// Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT * FROM member WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return true;
        } else {
            return false;
        }
        $stmt->close();
    }
    $conn->close();
}

function generateToken() {
    $length = 12;
    //$randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()"), 0, $length);
    $randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
    return $randomletter;
}

function sentEmailWithToken() {
    global $tokenCode, $email;
    $body = "<p>"
            . "Thank you for contacting us, <br>"
            . "A request to reset the password for your account has been made at M Boutique. To reset your password, kindly follow the link provided. <br>"
            . "<a href='http://35.209.19.240/mboutique/token_for_reset_password.php?tokenCode=$tokenCode&email=$email'>Click here to set new password</a>"
            . "</p>";

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tong95gaming@gmail.com'; // Gmail
    $mail->Password = 'ifapfmhxgpjmkccu'; // app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('tong95gaming@gmail.com', "no-reply-admin@MBoutique.com"); // Gmail

    $mail->addAddress($email);

    $mail->Subject = 'Reset Passwork link';
    $mail->Body = $body;
    $mail->isHTML(true);
    $mail->send();
}

function updateTokenToDB() {
    global $row, $email, $errorMsg, $success, $tokenCode, $verify;
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
// Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("UPDATE member SET token_code = ?, verify = ? WHERE email = ?");
        $stmt->bind_param("sss", $tokenCode, $verify, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$stmt->execute()) {
            $errorMsg .= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $errorMsg .= "Cannot set token and verify to false";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>

        <?php if (!$success) { ?>
            <main class='container' id='message'>
                <h4>Opps!</h4>
                <h4>The following input errors were detected:</h4>
                <p><?php echo " " . $errorMsg . " " ?></p>
                <a href='forgetPassword.php' role='button' class='btn btn-danger'>Back</a>

            </main>
        <?php } else { ?>
            <main class="container mb-3">
                <h1>Reset New Password</h1>
                <p>Further instructions have been sent to your email address.</p>
                <a href='index.php' role='button' class='btn btn-success'>Back</a>
            </main>
        <?php } ?>

        <?php include'footer.inc.php' ?>
    </body>
</html>