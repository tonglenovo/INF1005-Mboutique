<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $errorMsg = "";
    $success = true;
    $memberError = true;
    $itemsAdmin = [];
    $itemsMember = [];

//    print_r($_SESSION);
//    if (isset($_SESSION['loggedIn'])) {
//        if ($_SESSION['loggedIn'] == '') {
//            $errorMsg .= "Please login with member account to check this webpage";
//            $success = false;
//        }
//    }

    if (empty($_SESSION['member_id'])) {
        $errorMsg .= "Please login with member account to check this webpage";
        $success = false;
    } else {
        $member_id = $_SESSION['member_id'];
    }

    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'admin') {
            orderRecordAdmin();
        } else if ($_SESSION['role'] == 'Member') {
            orderRecordMember();
        }
    }
}

function orderRecordMember() {
    include 'dbinfo.php';
    global $errorMsg, $success, $member_id, $itemsMember, $memberError;

    // Check connection 
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT * FROM cart, payment, clothing WHERE cart.payment_id = payment.payment_id AND cart.clothing_id = clothing.clothing_id AND payment.member_id = ? ORDER BY payment.payment_id");
        $stmt->bind_param("s", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $itemsMember[] = $row;
            }
        } else {
            $errorMsg .= "No match found";
            $success = false;
            $memberError = false;
        }
        $stmt->close();
    }
    $conn->close();
}

function orderRecordAdmin() {

    include 'dbinfo.php';
    global $errorMsg, $success, $itemsAdmin, $memberError;

    // Check connection 
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $stmt = $conn->prepare("SELECT * 
FROM cart, payment, clothing, member, size, color
WHERE cart.payment_id = payment.payment_id 
AND cart.clothing_id = clothing.clothing_id 
AND member.member_id = cart.member_id
AND size.size_id = cart.size_id
AND color.color_id = cart.color_id
ORDER BY payment.payment_id");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $itemsAdmin[] = $row;
            }
        } else {
            $errorMsg .= "No match found";
            $memberError = false;
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
//    print_r($itemsAdmin);
}
?>

<!DOCTYPE html>
<html lang="en">
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
                <a href='index.php' role='button' class='btn btn-danger'>Back to home</a>

            </main>
        <?php } else if ($memberError == false) { ?>
            <main class='container' id='message'>
                <p>Empty Order</p>

            </main>
        <?php } else { ?>
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <main class="container">
                    <h1>View Order Detail</h1>
                    <div class="table-responsive">
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th>Clothing ID</th>
                                    <th>Customer Name</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Qty</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Delivery Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <?php for ($i = 0; $i < count($itemsAdmin); $i++) { ?>
                                <tbody>
                                    <tr>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['clothing_id'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['fname'] . " " . $itemsAdmin[$i]['lname'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['size_name'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['color_name'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['payment_paypal_id'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['payment_date'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['qty'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['address'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['email'] ?></th>
                                        <th class="align-middle"><?php echo $itemsAdmin[$i]['delivery_status'] ?></th>
                                        <?php if ($itemsAdmin[$i]['delivery_status'] == 'Shipping') { ?>
                                            <th class="align-middle">Await</th>
                                        <?php } else { ?>
                                            <th class="align-middle"><button class="btn btn-success btn-delivery" data-payment-id="<?php echo $itemsAdmin[$i]['payment_id'] ?>">Shipping</button></th>
                                        <?php } ?>

                                    </tr>

                                </tbody>

                            <?php } ?>
                        </table>
                    </div>
                </main>

            <?php } else if ($_SESSION['role'] == 'Member') { ?>
                <main class="container">
                    <h1>View Order Detail</h1>
                    <div class="container">
                        <div class="row mt-3 mb-3 text-center mt-3 mb-3 justify-content-center align-self-center">
                            <div class="col-lg-3 col-md-3 col-sm-12 align-middle">Clothing Title</div>
                            <div class="col-lg-3 col-md-3 col-sm-12 align-middle">Clothing Image</div>
                            <div class="col-lg-2 col-md-2 col-sm-12 align-middle">Qty</div>
                            <div class="col-lg-2 col-md-2 col-sm-12 align-middle">Total Price</div>
                            <div class="col-lg-2 col-md-2 col-sm-12 align-middle">Delivery Status</div>
                        </div>
                        <div class="row mt-3 mb-3 text-center mb-3 justify-content-center align-self-center">
                            <?php for ($i = 0; $i < count($itemsMember); $i++) { ?>
                                <div class="col-lg-3 col-md-3 col-sm-12 align-middle"><?php echo $itemsMember[$i]['clothing_title'] ?></div>
                                <?php $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $itemsMember[$i]['clothing_image']); ?>
                                <div class="col-lg-3 col-md-3 col-sm-12 align-middle"><img src="images/<?php echo $itemsMember[$i]['clothing_image'] ?>" alt="$withoutExt" style="width: 50%;"></div>
                                <div class="col-lg-2 col-md-2 col-sm-12 align-middle"><?php echo $itemsMember[$i]['qty'] ?></div>
                                <div class="col-lg-2 col-md-2 col-sm-12 align-middle">$<?php echo number_format((float) $itemsMember[$i]['clothing_price'], 2, '.', '') ?></div>
                                <div class="col-lg-2 col-md-2 col-sm-12 align-middle mb-3"><?php echo $itemsMember[$i]['delivery_status'] ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </main>


            <?php } ?>


        <?php } ?>




        <?php include'footer.inc.php' ?>
    </body>
</html>