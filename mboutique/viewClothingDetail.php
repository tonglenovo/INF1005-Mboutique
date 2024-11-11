<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errorMsg = "";
$success = true;
if (isset($_GET['cid'])) {
    $c_id = $_GET['cid'];
} else {
    $success = false;
    $errorMsg .= "Id not found <br/>";
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
}


if ($success) {
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    // Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT * FROM clothing WHERE clothing_id=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $c_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Note that email field is unique, so should only have
            // one row in the result set.
            $row = $result->fetch_assoc();
            $stmt = $conn->prepare("SELECT * FROM clothing_size, size  WHERE clothing_size.size_id=size.size_id AND clothing_id=?");
            // Bind & execute the query statement:
            $stmt->bind_param("s", $c_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row1 = $result->fetch_assoc()) {
                    $size[] = $row1;
                }
                $stmt = $conn->prepare("SELECT * FROM clothing_color, color  WHERE clothing_color.color_id=color.color_id AND clothing_id=?");
                // Bind & execute the query statement:
                $stmt->bind_param("s", $c_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row1 = $result->fetch_assoc()) {
                        $color[] = $row1;
                    }
                }
            }

//            print_r($size);
        } else {
            $errorMsg = "No result found!";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
} else {
    echo $errorMsg;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>
        
        <main class="container">
            <h1>Clothing: <?php echo $row['clothing_title'] ?></h1>
            <a href="viewClothing.php?type=<?php echo $type; ?>" class="btn btn-danger mt-3 mb-3">Back</a>

            <div class="card mb-3 ">
                <div class="row mb-3 align-items-center">
                    <div class="col-sm-6 col-md-4 col-lg-3 mt-3 mb-3">
                        <?php $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $row['clothing_image']); ?>
                        <img class="ml-3" alt='<?php echo $withoutExt; ?>' src="<?php echo 'images/' . $row['clothing_image']; ?>" style="max-width: 100%">
                    </div>
                    <div class="text-center col-sm-6 col-md-8 col-lg-9 mt-3 mb-3  ">

                        <div class="col mb-1">Type: <?php echo $row['clothing_type'] ?></div>
                        <div class="col mb-1">Name: <?php echo $row['clothing_title'] ?></div>
                        <div class="col mb-1">Description: <?php echo $row['clothing_description'] ?></div>
                        <div class="col mb-1">Available Size:
                            <?php for ($i = 0; $i < count($size); $i++) { ?>
                                <?php
                                echo $size[$i]['size_name'];
                                if ($i < count($size) - 1) {
                                    echo ",";
                                }
                                ?>
                            <?php } ?>
                            <div class="col mb-1">Available Color:
                                <?php for ($i = 0; $i < count($color); $i++) { ?>
                                    <?php
                                    echo $color[$i]['color_name'];
                                    if ($i < count($color) - 1) {
                                        echo ",";
                                    }
                                    ?>
                                <?php } ?>

<!--                        <div class="col mb-1">Available Size: <?php echo $row['clothing_size'] ?></div>-->
<!--                            <div class="col mb-1">Available Color: <?php echo $row['clothing_color'] ?></div>-->
                                <div class="col mb-1">Price: $<?php echo number_format((float) $row['clothing_price'], 2, '.', '') ?></div>
                                <div class="col mb-1"><button class="btn btn-primary add_to_cart" data-clothing-id="<?php echo $row['clothing_id'] ?>" data-member-role="<?php echo $_SESSION['role'] ?>" data-price="<?php echo $row['clothing_price'] ?>">Add to cart</button></div>
                                <div><input type="hidden" class="checkLogin" data-check-id="<?php echo $_SESSION['loggedIn'] ?>"></div>
                                <div><input type="hidden" class="cp_price"></div>

                            </div>
                        </div>
                    </div>
                </div>
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
                                <input type='number' value='1' id='qty'>
                            </div>
                            <input type='hidden' id='cid'>
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
