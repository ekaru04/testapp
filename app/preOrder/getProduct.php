<?php
session_start();
include("../../assets/config/db.php");

// $query = "SELECT * FROM mProduct p WHERE productID = '$_GET[productID]' AND status = 1 AND outletID = '$_SESSION[outletID]' AND curStock !=0";
// $res = mysql_query($query);

$queryPrice = mysql_query("SELECT p.productID, p.productName, d.price FROM mproduct p
INNER JOIN tabpricedetail d ON p.productID = d.productID AND d.priceID = '$_GET[priceID]'
WHERE p.productID = '$_GET[productID]' AND p.outletID = '$_SESSION[outletID]' AND p.status = 1 AND p.curStock != 0");

// $query = mysql_query("SELECT p.productID, p.productName, p.curStock, p.status, p.outletID d.price FROM tabpricedetail d
// INNER JOIN tabpriceheader h ON h.priceID = d.priceID
// INNER JOIN mproduct p ON p.productID = d.productID
// WHERE p.productID = '$_GET[productID]' AND status = 1 AND outletID = '$_SESSION[outletID]' AND curStock !=0 d.priceID = 2");

$data = array();
$row=mysql_fetch_array($queryPrice);
$data[productName] = $row['productName'];
$data[price] = $row['price'];
	
echo json_encode($data);
?>