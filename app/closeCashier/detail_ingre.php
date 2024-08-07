<?php
session_start();
include("../../assets/config/db.php");

$date = $_GET[transDate];
$closeShift = $_GET[closeShift];

if($closeShift == 1) {
    $dateShift = date('Y-m-d 00:00:00');
} else {
    $dateShift = date('Y-m-d 12:00:01');
}

$query = "SELECT SUM(afterDiscount) AS total FROM tabWarehouseTransaction WHERE transDate = '$date'";
$res = mysql_query($query);

$x=0;
$dataIngre = array();
$row=mysql_fetch_array($res);


$dataIngre[total] = $row['total'];


echo json_encode($dataIngre);
?>