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
        <?php include 'nav.inc.php' ?>

        <div class="container">
            <div class="row text-center">
                <input type="hidden" id="total_price" value="202.6">
                <div class="col-12 mt-3 mb-3"><button class="btn btn-danger">Checkout</button></div>
                <div id="paypal-button-container"></div>


                <input type="hidden" id="abc" value="0.01">

                <label><input class="textBox" type="checkbox" value="1"> Click here to continue</label>
                <label><input class="textBox" type="checkbox" value="2"> Click here to continue</label>
                <label><input class="textBox" type="checkbox" value="3"> Click here to continue</label>
            </div>
        </div>

        <?php include'footer.inc.php' ?>
    </body>
</html>

<!-- Replace "test" with your own sandbox Business account app client ID -->
<script src="https://www.paypal.com/sdk/js?client-id=AcitJIfE7GWc1w2cEgBWunEKRVNY4af3_cu2KW-JMEkJs8rrYF5aNTMHRUdk1YDC1bzs5gt9oWVWbD6A&currency=USD"></script>
<script>
    paypal.Buttons({
        onInit(data, actions) {

            // Disable the buttons
            actions.disable();

            // Listen for changes to the checkbox
            const checkboxes = document.querySelectorAll('.textBox');
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function (event) {
                    // Enable or disable the button when it is checked or unchecked
                    if ($('.textBox:checked').length === 0) {
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

            if ($('.textBox:checked').length === 0) {
                alert("There is nothing to check out");
            }


//            alert("stop");
//            
//            e.preventDefault();
            // do something else
        },
        createOrder: function (data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            var abc = $('#abc').val();
            console.log("ABC: " + abc);
            return actions.order.create({
                purchase_units: [{
                        amount: {
                            value: abc
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

//                id: '55N43183684315504', status: 'COMPLETED'
//                amount: {currency_code: 'USD', value: '0.01'}
//create_time: "2023-03-19T16:54:48Z"
//final_capture: true
//id: "55N43183684315504"
//seller_protection: {status: 'ELIGIBLE', dispute_categories: Array(2)}
//status: "COMPLETED"
//update_time: "2023-03-19T16:54:48Z"
//[[Prototype]]: Object
            });
        }
    }).render('#paypal-button-container');
    //This function displays Smart Payment Buttons on your web page.
</script>