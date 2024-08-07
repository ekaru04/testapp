<?php
session_start();
include("../../assets/config/db.php");

$productID = $_GET[productID];
// $closeShift = $_GET[closeShift];

// if($closeShift == 1) {
//     $dateShift = date('Y-m-d 00:00:00');
// } else {
//     $dateShift = date('Y-m-d 12:00:01');
// }

$query = "SELECT curStock FROM mproduct WHERE productID = '$productID'";
$res = mysql_query($query);

$x=0;
$data = array();
$row=mysql_fetch_array($res);

$data[curStock] = $row['curStock'];


echo json_encode($data);
?>