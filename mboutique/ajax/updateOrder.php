<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = "";
$address = "";
$success = true;
$errorMsg = "";
$delivery_status = "Pending";
$payment_statue = "done";
$payment_id = [];
$name = "";

if (empty($_POST['member_id'])) {
    $success = false;
} else {
    $member_id = $_POST['member_id'];
}

if (empty($_POST['cart_id'])) {
    $success = false;
} else {
    $cart_id = $_POST['cart_id'];
}

if (empty($_POST['amt'])) {
    $success = false;
} else {
    $amt = $_POST['amt'];
}

if (empty($_POST['name'])) {
    $success = false;
} else {
    $clothingName = $_POST['name'];
}

if (empty($_POST['qty'])) {
    $success = false;
} else {
    $qty = $_POST['qty'];
}

if (empty($_POST['paypal_id'])) {
    $success = false;
} else {
    $paypal_id = $_POST['paypal_id'];
}

if (empty($_POST['paypal_time'])) {
    $success = false;
} else {
    $paypal_time = $_POST['paypal_time'];
}

//invoice number get from database.
$sizeOfCart = count($cart_id);
$invoice_number = 0000;
$formatNumber = sprintf('%04d', $invoice_number);

$invoice_letter = "MB";
$invoice = $invoice_letter . $formatNumber;

//Array
//(
//    [member_id] => 9
//    [cart_id] => Array
//        (
//            [0] => 62
//            [1] => 63
//        )
//
//    [amt] => Array
//        (
//            [0] => 47.25
//            [1] => 49.09
//        )
//
//    [paypal_id] => 2UD32125LH642191M
//    [paypal_time] => 2023-03-20T16:38:12Z
//)

if ($success) {
    getEmailAndAddress();
    createPayment();
    updatePaymentStatusInCart();
    sentInvoiceMail();
} else {
    echo $errorMsg;
}

function getEmailAndAddress() {
    global $address, $email, $success, $errorMsg, $member_id, $name;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT * FROM member WHERE member_id=?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $address = $row['address'];
            $name = $row['fname'] . " " . $row['lname'];
        } else {
            $success = false;
            $errorMsg = "Member not found";
        }
        $stmt->close();
    }
    $conn->close();
}

function createPayment() {
    global $errorMsg, $success, $sizeOfCart, $cart_id, $member_id, $paypal_id, $paypal_time, $amt, $invoice, $address, $email, $invoice_number, $payment_id, $delivery_status;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        for ($i = 0; $i < $sizeOfCart; $i++) {
            $stmt = $conn->prepare("INSERT INTO payment (cart_id,member_id,payment_paypal_id,payment_date, payment_amt,invoice_number,address,email,delivery_status) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssss", $cart_id[$i], $member_id, $paypal_id, $paypal_time, $amt[$i], $invoice, $address, $email, $delivery_status);
            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
            } else {
                $invoice_number++;
                $payment_id[$i] = $stmt->insert_id; // Get the ID of the newly inserted record
            }
        }

        $stmt->close();
    }
    $conn->close();
}

function updatePaymentStatusInCart() {
    global $errorMsg, $success, $payment_statue, $payment_id, $cart_id, $sizeOfCart;
    $config = parse_ini_file('../../../private/db-config.ini');
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        for ($i = 0; $i < count($payment_id); $i++) {
            $stmt = $conn->prepare("UPDATE cart SET payment_id = ?, payment_status = ? WHERE cart_id = ?");
            $stmt->bind_param("sss", $payment_id[$i], $payment_statue, $cart_id[$i]);
            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
            }
        }
        $stmt->close();
    }
    $conn->close();
}

function sentInvoiceMail() {
    global $name, $paypal_id, $sizeOfCart, $email, $paypal_time, $clothingName, $qty, $amt;

    require '../phpmailer/src/Exception.php';
    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    for ($i = 0; $i < $sizeOfCart; $i++) {
        $testBody = "<p>Hello " . $name . ",</p>"
            . "<p>Your payment has been verified and we will proceed to ship your package soon.<p>"
            . "<p></p>"
            . "<p>Cheers,<br>"
            . "M Boutique</p>"
            . "<p>--------------------------------------<br>"
            . "Invoice Summary<br>"
            . "--------------------------------------<br>"
            . "PayPal ID: " . $paypal_id . "<br>"
            . "Issue date: " . $paypal_time . "<br>"
            . "Client: M Boutique<br>"
            . "Item: " . $clothingName[$i] . "<br>"
            . "Qty: " . $qty[$i] . "<br>"
            . "Amt: $" . $amt[$i] . "<br>"
            . "Due: " . $paypal_time . "</p>"
            . "<p>Thank you for your order.</p>";
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tong95gaming@gmail.com'; // Gmail
        $mail->Password = 'ifapfmhxgpjmkccu'; // app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('tong95gaming@gmail.com', "no-reply-admin@MBoutique.com"); // Gmail
        $mail->addAddress($email);
        $mail->Subject = 'Invoice Mail';
        $mail->Body = $testBody;
        $mail->isHTML(true);
        $mail->send();
        $mail->ClearAllRecipients();
    }
    echo "Payment is successful";
}
?>