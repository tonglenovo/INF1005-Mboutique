<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    include 'dbinfo.php';

    $errorMsg = "";
    $member_id = $_SESSION['member_id'];
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT * FROM member WHERE member_id=?");
        $stmt->bind_param("s", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $stmt->close();
    }
    $conn->close();

    $address_array = explode(" ", $row['address']);
    $address1 = $address_array[count($address_array) - 1];
    $address2 = $address_array[count($address_array) - 3];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include "nav.inc.php"; ?>
        <main class="container">
            <h1>Member Detail</h1>
            <form action="process_member_detail.php" method="post">
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input class="form-control" type="text" id="fname"
                           name="fname" placeholder="Enter first name" value='<?php echo $row['fname'] ?>'>
                </div>
                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input class="form-control" type="text" id="lname"
                           name="lname" placeholder="Enter last name" value='<?php echo $row['lname'] ?>'>
                </div>
                <div class="form-group">
                    <label for="email">Address</label>
                    <input class="form-control" type="text" id="address"
                           name="address" placeholder="Address" value='<?php echo $row['address']; ?>' disabled>
                </div>
                <div class="form-group">
                    <label for="email">Address Line 1:</label>
                    <input class="form-control" type="text" id="address1"
                           name="address1" placeholder="Just postal code will do Eg. 123456" value='<?php echo $address1 ?>'>
                </div>
                <div class="form-group">
                    <label for="email">Address Line 2:</label>
                    <input class="form-control" type="text" id="address2"
                           name="address2" placeholder="Just door number Eg. #12-345" value='<?php echo $address2 ?>'>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" id="email"
                           name="email" placeholder="Enter email" value='<?php echo $row['email'] ?>'>
                </div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input class="form-control" type="password" id="pwd"
                           name="pwd" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <label for="pwd_confirm">Confirm Password:</label>
                    <input class="form-control" type="password" id="pwd_confirm"
                           name="pwd_confirm" placeholder="Confirm password">
                </div>
                <div class="form-group">
                    <input type='hidden' value='<?php echo $_SESSION['member_id'] ?>' name='mem_id'>
                    <button class="btn btn-success" type="submit">Edit Member Detail</button>
                   <a href='index.php' class="btn btn-danger">Cancel</a>
                </div>
            </form>

        </main>
        <?php include "footer.inc.php"; ?>
    </body>
</html>