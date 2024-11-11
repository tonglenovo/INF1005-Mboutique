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
        $email = $errorMsg = "";
        $address = "";
        $checker = 0;
        if (empty($_POST["email"])) {
            $errorMsg .= "Email field is required.<br>";
            $success = false;
        } else {
            $checker += 1;
            $email = sanitize_input($_POST["email"]);
            // Additional check to make sure e-mail address is well-formed.
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg .= "Invalid email format.<br>";
                $success = false;
            }
        }

        include 'dbinfo.php';
        $conn = new mysqli($config['servername'], $config['username'],
                $config['password'], $config['dbname']);

        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            $stmt = $conn->prepare("SELECT email FROM member WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $success = false;
                $errorMsg .= "Email is used.<br>";
            } else {

                $name = "";
                $success = true;

                if (empty($_POST["fname"])) {
                    $errorMsg .= "First Name field is required.<br>";
                    $success = false;
                } else {
                    $checker += 1;
                    $fname = sanitize_input($_POST["fname"]);
                    $name .= $_POST["fname"] . " ";
                }
                if (empty($_POST["lname"])) {
                    $errorMsg .= "Last Name field is required.<br>";
                    $success = false;
                } else {
                    $checker += 1;
                    $name .= $_POST["lname"];
                    $lname = sanitize_input($_POST["lname"]);
                }
                if (empty($_POST["address1"])) {
                    $errorMsg .= "Address Line 1 field is required.<br>";
                    $success = false;
                } else {
                    $address1 = $_POST["address1"];
                    $address1 = sanitize_input($_POST["address1"]);
                }
                if (empty($_POST["address2"])) {
                    $errorMsg .= "Address Line 2 field is required.<br>";
                    $success = false;
                } else {
                    $address2 = $_POST["address2"];
                    $address2 = sanitize_input($_POST["address2"]);
                }
                if (empty($_POST["pwd"])) {
                    $errorMsg .= "Password field is required.<br>";
                    $success = false;
                } else {
                    $checker += 1;
                    //$pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
                }
                if (empty($_POST["pwd_confirm"])) {
                    $errorMsg .= "Password Confirm field is required.<br>";
                    $success = false;
                } else {
                    $checker += 1;
                    //$pwd_confirm = password_hash($_POST["pwd_confirm"], PASSWORD_DEFAULT);
                }



                //        if (password_verify($_POST['pwd'], $pwd) && (password_verify($_POST["pwd_confirm"], $pwd_confirm))) {
                //            $errorMsg .= "Passwords do not match. <br>";
                //            $success = false;
                //        }
                if ($_POST['pwd'] != $_POST['pwd_confirm']) {
                    $errorMsg .= "Passwords do not match. <br>";
                    $success = false;
                } else {
                    if (strlen($_POST['pwd']) < 8 || strlen($_POST['pwd_confirm']) < 8) {
                        $errorMsg .= "Password must be a minimum of 8 characters. <br>";
                        $success = false;
                    }
                }
            }
        }

        if(!getAddress($address1, $address2)){
            $success = false;
        }

        if ($success) {
            $role = 'Member';
            $pwd_hashed = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
            saveMemberToDB();
            echo "<main class='container' id='message'>";
            echo "<h4>Registration successful!</h4>";
            echo "<h5>Thank you for signing up, $name.</h5>";
            //            echo "<p>Email: " . $email . "</p>";
            echo "<a href='login.php' role='button' class='btn btn-success'>Log-in</a>";
            echo "</main>";
        } else {
            echo "<main class='container' id='message'>";
            echo "<h4>Opps!</h4>";
            echo "<h4>The following input errors were detected:</h4>";
            echo "<p>" . $errorMsg . "</p>";
            //echo "<button onclick='window.location.href=register.php;' type='button' class='btn btn-danger'>Return to Sign Up</button>";
            echo "<a href='register.php' role='button' class='btn btn-danger'>Return to Sign Up</a>";
            echo "</main>";
        }

        //Helper function that checks input for malicious or unwanted content.
        function sanitize_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        function saveMemberToDB() {
            global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success, $role, $address;

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
                $stmt = $conn->prepare("INSERT INTO member (fname, lname, email, password, address, role) VALUES (?, ?, ?, ?, ?, ?)");
                // Bind & execute the query statement: 
                $stmt->bind_param("ssssss", $fname, $lname, $email, $pwd_hashed, $address, $role);
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
        ?>

        <?php include 'footer.inc.php'; ?>
    </body>
</html>