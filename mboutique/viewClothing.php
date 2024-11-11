<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

$success = true;
//$type = "T-shirt";

include 'dbinfo.php';
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
    $success = false;
} else {
    // Prepare the statement:
    $stmt = $conn->prepare("SELECT * FROM clothing WHERE clothing_type = ?");
    $stmt->bind_param("s", $type);
    // Bind & execute the query statement:
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Note that email field is unique, so should only have
        // one row in the result set.
        while ($row = $result->fetch_assoc()) {
            $clothings[] = $row;
        }
    } else {
        $errorMsg = "Database no match found";
        $success = false;
//        $clothings[] = Array();
    }
    $stmt->close();
}
$conn->close();
//print_r($clothings);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>

        <main class="container-fluid">
            <h1 class="text-center mb-3">View Men <?php echo str_replace('_', ' ', $type); ?></h1>
            
            <div class="row">
                <?php if (!$success) { ?>
                    <div class='container' id='message'>
                        <h4>Opps!</h4>
                        <h4>The following input errors were detected:</h4>
                        <p><?php echo " . $errorMsg . "; ?></p>
                        <a href='index.php' role='button' class='btn btn-danger'>Return to Home</a>
                    </div>
                <?php } else { ?>
                    <?php for ($i = 0; $i < count($clothings); $i++) { ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 mt-3 mb-3">
                            <div class="card text-center">      
                                <a href="viewClothingDetail.php?cid=<?php echo $clothings[$i]['clothing_id']; ?>&type=<?php echo $type; ?>" class="text-center">
                                    <img class="card-img-top img_detail" src="images/<?php echo $clothings[$i]['clothing_image'] ?>" style="max-width: 50%; margin: 0 auto;" alt="Card image cap" 
                                         id="<?php echo $clothings[$i]['clothing_id']; ?>"></a>

                                <div class="card-body text-center">
                                    <strong class="card-title"><?php echo $clothings[$i]['clothing_title'] ?></strong>
        <!--                                <p class="card-text"><i>description</i></p>-->

                                    <p class="card-text">$<?php echo number_format((float) $clothings[$i]['clothing_price'], 2, '.', '') ?></p>
                                    <i class="fa-solid fa-cart-shopping"><button class="btn btn-primary add_to_cart" data-clothing-id="<?php echo $clothings[$i]['clothing_id'] ?>" data-member-role="<?php echo $_SESSION['role'] ?>" data-member-id="<?php echo $_SESSION['member_id'] ?>" data-price="<?php echo $clothings[$i]['clothing_price'] ?>">Buy</button></i>
                                    <input type="hidden" class="checkLogin" data-check-id="<?php echo $_SESSION["loggedIn"] ?>">
                                    <input type="hidden" class="c_id" value="<?php echo $clothings[$i]['clothing_id'] ?>">
                                    <input type="hidden" class="cp_price">
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
            <div class="modal fade" id="cartModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add to cart</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class='form-group'>
                                <label for="size_select">Select Size:</label>
                                <!-- base on database and add to size -->
                                <select id='size_select'></select>
                            </div>
                            <div class='form-group'>
                                <label for="color_select">Select Color:</label>
                                <!-- base on database and add to size -->
                                <select id='color_select'></select>
                            </div>
                            <div class='form-group'>
                                <label for="qty">Select Quantity:</label>
                                <input type='number' value='1' id='qty' min="1" max="100">
                            </div>
                            <input type='hidden' id='cid'>
                            <input type='hidden' id='cprice'>
                            <input type='hidden' id='mid' value='<?php echo $_SESSION['member_id'] ?>'>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="add_to_cart_ajax"><span class="fas fa-plus"></span>Add to cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>


        <?php include'footer.inc.php' ?>
    </body>
</html>
