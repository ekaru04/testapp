<?php
session_start();
include("../../assets/config/db.php");

$query = "SELECT * FROM mProduct WHERE productID = '$_GET[productID]' AND status = 1 AND outletID = '$_SESSION[outletID]' AND curStock !=0";
$res = mysql_query($query);

$data = array();
$row=mysql_fetch_array($res);
$data[productName] = $row['productName'];
$data[productPrice] = $row['productPrice'];
	
    echo json_encode($data);
?>