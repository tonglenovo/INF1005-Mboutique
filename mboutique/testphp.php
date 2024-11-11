<?php

//$errorMsg = "";
//// Split the sizes string into an array
////$sizesString = "S,M,L,XL,XXL";
//
//$sizesArray = $_POST['sizes'];
////$sizesArray = explode(",", $sizesString);
//
//// Define a new array to store the size IDs
//$sizeIDs = array();
//
//// Iterate over each size and retrieve its ID from the database
//// Replace "sizes_table" with the name of your database table
//include 'dbinfo.php';
//$conn = new mysqli($config['servername'], $config['username'],
//        $config['password'], $config['dbname']);
//if ($conn->connect_error) {
//    $errorMsg = "Connection failed: " . $conn->connect_error;
////    $success = false;
//} else {
//    foreach ($sizesArray as $size) {
//        $stmt = $conn->prepare("SELECT size_id FROM size WHERE size_name = ?");
//        // Bind & execute the query statement: 
//        $stmt->bind_param("s", $size);
//        $stmt->execute();
//        $result = $stmt->get_result();
//        $row = $result->fetch_assoc();
//        print_r($row);
//        $sizeID = $row["size_id"];
//        $sizeIDs[] = $sizeID;
//    }
//}
//
//
//// Iterate over the size IDs array and perform any operations needed
//foreach ($sizeIDs as $sizeID) {
//    // Code block to be executed for each size ID
//    echo $sizeID . "<br/>";
//}
$errorMsg = "";
$address = "";
if(isset($_GET['code'])){
    $code = $_GET['code'];
}
$postal_code = $code;
$address2 = "#10-677";
//echo getAddress($postal_code, $address2);
if(getAddress($postal_code, $address2)){
    echo 'true';
    echo $address;
} else {
    echo 'false';
}

function getAddress($postal_code, $address2) {
    global $errorMsg,$address;
    $url = "https://developers.onemap.sg/commonapi/search?searchVal=" . urlencode($postal_code) . "&returnGeom=Y&getAddrDetails=Y&pageNum=1";

    $data = file_get_contents($url);
    $results = json_decode($data);

    if ($results->found > 0) {
//    print_r($results);
        $address = "BLK ". $results->results[0]->BLK_NO . " " . $results->results[0]->ROAD_NAME . " ". $address2 . ", SINGAPORE " . $results->results[0]->POSTAL;
        return true;
//    echo gettype($address);
    } else {
        $errorMsg .= "Address not found for postal code " . $postal_code;
        return false;
    }
}

?>