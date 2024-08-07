<?php
session_start();
include("../../assets/config/db.php");

$date = $_GET[transItemDate];
// $closeShift = $_GET[closeShift];

// if($closeShift == 1) {
//     $dateShift = date('Y-m-d 00:00:00');
// } else {
//     $dateShift = date('Y-m-d 12:00:01');
// }

$query = "SELECT SUM(afterDiscount) AS total FROM tabstocktransaction WHERE transItemDate = '$date'";
$res = mysql_query($query);

$x=0;
$dataStock = array();
$row=mysql_fetch_array($res);


$dataStock[total] = $row['total'];


echo json_encode($dataStock);
?>