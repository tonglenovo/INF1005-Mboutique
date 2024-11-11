<?php
//print_r($_POST);

$errorMsg = "";
$success = true;

if (empty($_POST['pwd'])) {
    $errorMsg .= "Password field is required."."<br>";
    $success = false;
}

if (empty($_POST['cfm_pwd'])) {
    $errorMsg .= "Confirm Password field is required."."<br>";
    $success = false;
} else {

    if ($_POST['pwd'] != $_POST['cfm_pwd']) {
        $errorMsg .= "Password do not match.";
        $success = false;
    } else {
        if (strlen($_POST['pwd']) < 8 || strlen($_POST['cfm_pwd']) < 8) {
            $errorMsg .= "Password must be a minimum of 8 characters.";
            $success = false;
        }
    }
}

if(!$success){
    echo $errorMsg;
} else {
    echo "ok";
}

// hash password
// update password
// display done
// displat error
?>


