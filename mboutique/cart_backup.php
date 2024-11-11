<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
//    print_r($_SESSION);
    $success = true;
    $errorMsg = "";
    $memberError = true;
    $payment_status = 'not_yet';
    if (isset($_SESSION['member_id'])) {
        $mid = $_SESSION['member_id'];
    } else {
        $success = false;
        $errorMsg .= "Member is require";
    }

    if (isset($_SESSION['role'])) {
        if ($_SESSION == 'admin') {
            echo "Trigger";
            $success = false;
            $errorMsg .= "admin is not allow to view the cart";
        }
    }

    if ($success) {
        include 'dbinfo.php';
        $conn = new mysqli($config['servername'], $config['username'],
                $config['password'], $config['dbname']);
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            // Prepare the statement:
//        $stmt = $conn->prepare("SELECT clothing.*, count(*) AS qty, size.size_name, clothing.clothing_price * count(*) AS total_price, cart.*, color.color_name FROM cart, clothing, size, color WHERE cart.clothing_id = clothing.clothing_id AND cart.size_id = size.size_id AND cart.color_id = color.color_id AND cart.member_id = ? GROUP BY cart.clothing_id,cart.size_id,cart.color_id");
//        $stmt = $conn->prepare("SELECT c.*, clothing.*, size.size_name, color.color_name FROM cart c LEFT JOIN payment p ON c.cart_id = p.cart_id LEFT JOIN clothing ON clothing.clothing_id = c.clothing_id LEFT JOIN size ON c.size_id = size.size_id LEFT JOIN color ON c.color_id = color.color_id WHERE p.payment_id IS NULL AND c.member_id = ?");
//        $stmt = $conn->prepare("SELECT clothing.*, count(*) AS qty, size.size_name, clothing.clothing_price * count(*) AS total_price, cart.*, color.color_name FROM cart, clothing, size, color WHERE cart.clothing_id = clothing.clothing_id AND cart.size_id = size.size_id AND cart.color_id = color.color_id AND cart.member_id = ? AND cart.payment_status = ? GROUP BY cart.clothing_id,cart.size_id,cart.color_id");
            $stmt = $conn->prepare("SELECT clothing.*, size.size_name, cart.*, color.color_name 
FROM cart, clothing, size, color 
WHERE cart.clothing_id = clothing.clothing_id 
AND cart.size_id = size.size_id 
AND cart.color_id = color.color_id 
AND cart.member_id = ?
AND cart.payment_status = ?");
            // Bind & execute the query statement:
            $stmt->bind_param("ss", $mid, $payment_status);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Note that email field is unique, so should only have
                // one row in the result set.
                while ($row = $result->fetch_assoc()) {
                    $clothings[] = $row;
                }
                // Bind & execute the query statement:
//            $stmt->bind_param("s", $mid);
//            $stmt->execute();
            } else {
                $errorMsg .= "There are no cart and no pending payment to process";
                $memberError = false;
                $success = false;
            }
            $stmt->close();
        }
        $conn->close();
    }


//    print_r($clothings);
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
                <a href='index.php' role='button' class='btn btn-danger'>Back to home</a>

            </main>
        <?php } else if ($memberError == false) { ?>
            <main class='container' id='message'>
                <p>Empty Cart</p>
            </main>
        <?php } else { ?>
            <div class="container">
                <div class='card mt-3 mb-3 text-center'>
                    <div class='row  d-none d-sm-flex'>
                        <div class="col-lg-2 col-md-3 col-sm-12">CheckBox</div>
                        <div class="col-lg-4 col-md-3 col-sm-12">Image</div>
                        <div class="col-lg-4 col-md-3 col-sm-12">Detail</div>
                        <div class="col-lg-2 col-md-3 col-sm-12">Action</div>
                    </div>
                </div>
                <?php for ($i = 0; $i < count($clothings); $i++) { ?>
                    <div class='card mb-3 text-center'>
                        <div class='row align-items-center'>
                            <div class="col-lg-2 col-md-3 col-sm-12"><input type="checkbox" class="amtCheckBox" id="checkAmt" name="<?php echo $clothings[$i]['clothing_id'] ?>" value="<?php echo $clothings[$i]['total_price'] ?>" /></div>
                            <div class="col-lg-4 col-md-3 col-sm-12"><img src="<?php echo 'images/' . $clothings[$i]['clothing_image'] ?>" style="max-width: 50%" class="clothing-image"></div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <div><input type='hidden' id='ce_id' value='<?php echo $clothings[$i]['cart_id'] ?>' ></div>
                                <div id="ce_type" value="<?php echo $clothings[$i]['clothing_type'] ?>">Type: <?php echo $clothings[$i]['clothing_type'] ?></div>
                                <div id="ce_name" value="<?php echo $clothings[$i]['clothing_title'] ?>">Name: <?php echo $clothings[$i]['clothing_title'] ?></div>
                                <div id="ce_price" value="<?php echo number_format((float) $clothings[$i]['total_price'], 2, '.', '') ?>">Price: $<?php echo number_format((float) $clothings[$i]['total_price'], 2, '.', '') ?></div>
                                <div id="ce_size">Size: <?php echo $clothings[$i]['size_name'] ?></div>
                                <div id="ce_color">Color: <?php echo $clothings[$i]['color_name'] ?></div>
                                <div id="ce_qty" data-qty="<?php echo $clothings[$i]['qty'] ?>">Qty: <?php echo $clothings[$i]['qty'] ?></div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-12">
        <!--                            <button class="btn btn-success" id="<?php echo $clothings[$i]['clothing_id'] ?>">Edit</button>-->
                                <button class="btn btn-primary btn-edit-cart" id="<?php echo $clothings[$i]['clothing_id'] ?>" data-cart-id="<?php echo $clothings[$i]['cart_id'] ?>" data-color-id="<?php echo $clothings[$i]['color_id'] ?>" data-toggle="modal" data-target="#editCartModel" data-size-id=<?php echo $clothings[$i]['size_id'] ?>><span class="fas fa-pencil-alt"></span>Edit</button>
                                <button class="btn btn-danger btn-delete-cart" id="<?php echo $clothings[$i]['clothing_id'] ?>" data-toggle="modal" data-target="#deleteCartModel" data-cart-id="<?php echo $clothings[$i]['cart_id'] ?>" data-c-id="<?php echo $clothings[$i]['clothing_id'] ?>" data-size-id=<?php echo $clothings[$i]['size_id'] ?>><span class="fas fa-trash-alt"></span>Delete</button>
                            </div>
                        </div>
                    </div>
                <?php } ?>


                <div class='row mb-3'>
                    <input type='hidden' id='hidden_price'>
                    <div class='col-lg-6 col-md-4 col-sm-12' id="totalPrice"><p id="valueList"><span> total price: $0.00 </span></p></div>
                    <div class='col-lg-3 col-md-4 col-sm-12'><button class='btn btn-danger ml-auto btn-check-out'>Checkout</button></div>
                    <div class="col-lg-3 col-md-4 col-sm-12"><div id="paypal-button-container"></div></div>
                </div>
            </div>
        <?php } ?>

        <div class="modal fade" id="editCartModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Cart</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="ec_edit_c_id">
                        <input type="hidden" id="ec_edit_cart_id">
                        <input type="hidden" id="old_qty">
                        <input type="hidden" id="old_size">
                        <input type="hidden" id="old_color">
                        <input type="hidden" id="old_c_id">
                        <input type='hidden' id='old_price'>
                        <div class='form-group'>
                            <label for="edit_size_select">Select Size:</label>
                            <!-- base on database and add to size -->
                            <select id='edit_size_select'></select>
                        </div>
                        <div class='form-group'>
                            <label for="edit_size_select">Select Color:</label>
                            <!-- base on database and add to size -->
                            <select id='edit_color_select'></select>
                        </div>
                        <div class='form-group'>
                            <label for="qty">Select Quantity:</label>
                            <input type='number' value='1' id='qty'/>
                        </div>
                        <input type='hidden' id='cid'>
                        <input type='hidden' id='price'>
                        <input type='hidden' id='mid' value='<?php echo $_SESSION['member_id'] ?>'>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="edit_to_cart_ajax"><span class="fas fa-pencil-alt"></span>Edit Cart</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCartModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Cart</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="old_hidden_c_id">
                        <input type="hidden" id="old_hidden_m_id">
                        <input type="hidden" id="old_hidden_cart_id">
                        <p>Are you sure you want to delete?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" id="delete_cart_btn"><span class="fas fa-trash-alt"></span> Yes, Delete it</button>
                    </div>
                </div>
            </div>
        </div>




        <?php include'footer.inc.php' ?>
    </body>
</html>
<script src="https://www.paypal.com/sdk/js?client-id=AcitJIfE7GWc1w2cEgBWunEKRVNY4af3_cu2KW-JMEkJs8rrYF5aNTMHRUdk1YDC1bzs5gt9oWVWbD6A&currency=USD"></script>
<script>
    paypal.Buttons({
        onInit(data, actions) {

            // Disable the buttons
            actions.disable();

            // Listen for changes to the checkbox
            const checkboxes = document.querySelectorAll('.amtCheckBox');
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function (event) {
                    // Enable or disable the button when it is checked or unchecked
                    if ($('.amtCheckBox:checked').length === 0) {
                        actions.disable();
                        console.log("disable");
                    } else {
                        actions.enable();
                        console.log("enable");
                    }

                });
            });

        },
        onClick() {

            if ($('.amtCheckBox:checked').length === 0) {
                alert("There is nothing to check out");
            }


//            alert("stop");
//            
//            e.preventDefault();
            // do something else
        },
        createOrder: function (data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            var hidden_price = $('#hidden_price').val();
            var price = parseFloat(hidden_price).toFixed(2);
            return actions.order.create({
                purchase_units: [{
                        amount: {
                            value: price
                        }
                    }]
            });
        },
        onApprove: function (data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {
                const transaction = details.purchase_units[0].payments.captures[0];

                // This function shows a transaction success message to your buyer.
//        alert('Transaction completed by ' + details.payer.name.given_name);

                //ajax to database
                console.log(transaction);
                console.log(transaction['orderID']);
                console.log(transaction.id);
                console.log(transaction.create_time);

                var priceArr = [];
                var nameArr = [];
                var cIDArr = [];
                var qtyArr = [];
                var member_id = $('#mid').val();
                var id = $('#ce_id').val();
                var ceNameDivs = document.querySelectorAll('#ce_name');
                var ceID = document.querySelectorAll('#ce_id');
                var cbCheck = document.getElementsByClassName('amtCheckBox');
                var ceQty = document.querySelectorAll('#ce_qty');
                for (var i = 0; cbCheck[i]; ++i)
                {
                    if (cbCheck[i].checked) {
                        priceArr.push(cbCheck[i].value);
                        nameArr.push(ceNameDivs[i].getAttribute('value'));
                        cIDArr.push(ceID[i].value);
                        qtyArr.push(ceQty[i].getAttribute('data-qty'));
                    }
                }

                $.ajax({
                    url: 'ajax/updateOrder.php',
                    type: 'POST',
                    data: {
                        member_id: member_id,
                        cart_id: cIDArr,
                        amt: priceArr,
                        name: nameArr,
                        qty: qtyArr,
                        paypal_id: transaction.id,
                        paypal_time: transaction.create_time
                    },
                    success: function (response) {
                        alert(response);
                        window.location.reload();
                    }
                });

            });
        }
    }).render('#paypal-button-container');
    //This function displays Smart Payment Buttons on your web page.
</script>


            