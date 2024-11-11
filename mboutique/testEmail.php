<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$testBody = "<p>Hello <username></p>"
        . "<p>Thanks so much for your business<p>"
        . "<p></p>"
        . "<p>Cheers,<br>"
        . "M Boutique</p>"
        . "<p>--------------------------------------<br>"
        . "Invoice Summary<br>"
        . "--------------------------------------<br>"
        . "PayPal ID: <br>"
        . "Issue date: <br>"
        . "Client: M Boutique<br>"
        . "Item: <br>"
        . "Qty: <br>"
        . "Amt: <br>"
        . "Due: </p>"
        . "<p>Thanks you for your business.</p>";

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

for ($i = 0; $i < 2; $i++) {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tong95gaming@gmail.com'; // Gmail
    $mail->Password = 'ifapfmhxgpjmkccu'; // app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('tong95gaming@gmail.com', "no-reply-admin@MBoutique.com"); // Gmail

    $mail->addAddress('sianxiao@hotmail.com');

    $mail->Subject = 'Test Mail';
    $mail->Body = $testBody;
    $mail->isHTML(true);
    $mail->send();
    $mail->ClearAllRecipients();
}

echo "<script>alert('sentsuccessfully'); document.location.href = 'index.php';</script>"
?>