<?php
session_start();
include("../../assets/config/db.php");

//$outletID = $_SESSION[outletID];
$outletID = "1";

$query = "SELECT * FROM mPromo WHERE promoID = '$_GET[promoID]' AND status = 1 AND outletID = '$outletID'";
$res = mysql_query($query);

$data = array();
$today = date("Y-m-d");
$row=mysql_fetch_array($res);

$data[promoName] = $row['promoName'];
$data[promoType] = $row['promoType'];
$data[promoRequirement] = $row['promoRequirement'];

$startDate = date("Y-m-d",strtotime($row['startDate']));
$endDate = date("Y-m-d",strtotime($row['endDate']));

$data[startDate] = $startDate;
$data[endDate] = $endDate;

$data[isDiscount] = $row['isDiscount'];
if($row['isDiscount']){
	$data[discount] = $row['discount'];
}else{
	$data[discount] = 0;
}

$data[isMonday] = $row['isMonday'];
$data[isTuesday] = $row['isTuesday'];
$data[isWednesday] = $row['isWednesday'];
$data[isThursday] = $row['isThursday'];
$data[isFriday] = $row['isFriday'];
$data[isSaturday] = $row['isSaturday'];
$data[isSunday] = $row['isSunday'];
	
echo json_encode($data);
?>