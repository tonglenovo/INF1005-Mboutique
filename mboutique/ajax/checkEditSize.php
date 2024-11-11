<?php
$errorMsg = "";
$success = true;

if (isset($_POST['e_size'])) {
    $e_size = $_POST['e_size'];
}

if (isset($_POST['e_id'])) {
    $e_id = $_POST['e_id'];
}

if (isset($_POST['e_size_hidden'])) {
    $e_size_hidden = $_POST['e_size_hidden'];
}

//print_r($_POST);

$differences = array_diff($e_size_hidden, $e_size);
$differences1 = array_diff($e_size, $e_size_hidden);
//print_r($e_size);
//print_r($e_size_hidden);
//print_r($differences);
//print_r(count($differences));
//print_r($differences1);

if (count($differences) == 0) {
    echo count($differences);
} else if (count($differences) > 0) {
    echo -1;
} else {
    echo "No differences found.";
}
?>
