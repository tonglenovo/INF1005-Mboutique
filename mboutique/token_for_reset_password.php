<?php
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

if (isset($_GET['tokenCode'])) {
    $tokenCode = $_GET['tokenCode'];
} else {
    $success = false;
    $errorMsg .= "Token code is require using GET <br>";
}
if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    $success = false;
    $errorMsg .= "Email is require using GET <br>";
}

checkTokenAndVaildEmail();

function checkTokenAndVaildEmail() {
    global $success, $errorMsg, $tokenCode, $email;
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT * FROM member WHERE email=? AND token_code=?");
        $stmt->bind_param("ss", $email,$tokenCode);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $success = true;
        } else {
            $success = false;
            $errorMsg .= "Wrong Email or Invaild token token";
        }
    }
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
                <p><?php echo " " . $errorMsg . "<br>" ?></p>
                <a href="index.php" class="btn btn-danger">Back to home</a>

            </main>
        <?php } else { ?>
            <main class="container">
                <h1>New Password</h1>
                <form action="process_new_password.php" method="post" id="passwordForm">

                    <div class="form-group">
                        <?php if (isset($row)) { ?>
                            <input type="hidden" name="mid" id="mid" value="<?php echo $row['member_id'] ?>"
                        <?php } ?>
                               <label for="email">New Password:</label>
                        <input class="form-control" type="password" id="pwd"
                               name="pwd" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="email">Confirm Password:</label>
                        <input class="form-control" type="password" id="cfm_pwd"
                               name="cfm_pwd" placeholder="Confirm Password">
                    </div>
                    <div class="form-group">
                        <p id="errorMsg" style="color: red;"></p>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="email" id="hidden_email" value="<?php echo $email ?>" >
                        <button class="btn btn-primary" id="btnResetNewPassword" type="submit">Submit</button>
                    </div>

                </form>
            </main>
        <?php } ?>

        <?php include'footer.inc.php' ?>
    </body>
</html>