<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include "nav.inc.php"; ?>


        <?php
        /*
         * Helper function to authenticate the login.
         */
        $success = true;
        $name = "";
        $email = "";
        $errorMsg = "";
        $pwd = "";
        $id = "";
        $role = "";
        if (empty($_POST["email"])) {
            $success = false;
            $errorMsg .= "Email is required.<br>";
        } else {
            $email = sanitize_input($_POST['email']);
        }
        if (empty($_POST['pwd'])) {
            $success = false;
            $errorMsg .= "Password is required.<br>";
        }
        if (!empty($_POST['email']) && !empty($_POST['pwd'])) {
            authenticateUser();
        }

        if ($success) {

            $_SESSION['member_name'] = $name;
            $_SESSION['loggedIn'] = true;
            $_SESSION['role'] = $role;
            $_SESSION['member_id'] = $id;
            echo "<main class='container' id='message'>";
            echo "<h4>Login successful!</h4>";
//            echo "<h5>Welcome back, $fullName.</h5>";
            echo "<h5>Welcome back, $name</h5>";
            echo "<a href='index.php' role='button' class='btn btn-success'>Return to Home</a>";
            echo "</main>";
        } else {
            echo "<main class='container' id='message'>";
            echo "<h4>Opps!</h4>";
            echo "<h4>The following input errors were detected:</h4>";
            echo "<p>" . $errorMsg . "</p>";
            //echo "<button onclick='window.location.href=register.php;' type='button' class='btn btn-danger'>Return to Sign Up</button>";
            echo "<a href='login.php' role='button' class='btn btn-warning'>Return to Login</a>";
            echo "</main>";
        }

        function authenticateUser() {
            global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success, $name, $id, $role;
            // Create database connection.
            include 'dbinfo.php';
            $conn = new mysqli($config['servername'], $config['username'],
                    $config['password'], $config['dbname']);
            // Check connection
            if ($conn->connect_error) {
                $errorMsg = "Connection failed: " . $conn->connect_error;
                $success = false;
            } else {
                // Prepare the statement:
                $stmt = $conn->prepare("SELECT * FROM member WHERE email=?");
                // Bind & execute the query statement:
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    // Note that email field is unique, so should only have
                    // one row in the result set.
                    $row = $result->fetch_assoc();
                    $fname = $row["fname"];
                    $lname = $row["lname"];
                    $pwd_hashed = $row["password"];
                    $id = $row['member_id'];
                    $role = $row['role'];
                    $name = $fname . " " . $lname;
                    // Check if the password matches:
                    if (!password_verify($_POST['pwd'], $pwd_hashed)) {
                        // Don't be too specific with the error message - hackers don't
                        // need to know which one they got right or wrong. :)
                        $errorMsg = "Password doesn't match...";
                        $success = false;
                    }
                } else {
                    $errorMsg = "Email not found";
                    $success = false;
                }
                $stmt->close();
            }
            $conn->close();
        }

        function sanitize_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        ?>

        <?php include 'footer.inc.php'; ?>
    </body>
</html>