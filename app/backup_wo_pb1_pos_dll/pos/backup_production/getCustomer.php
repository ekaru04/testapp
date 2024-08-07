<?php
session_start();
include("../../assets/config/db.php");

$query = "SELECT * FROM mcustomer WHERE customerID = '$_GET[customerID]' AND status = 1";
$res = mysql_query($query);

$data = array();
$row=mysql_fetch_array($res);
$data[customerName] = $row['customerName'];
$data[customerPhone] = $row['customerPhone'];
$data[customerEmail] = $row['customerEmail'];
	
echo json_encode($data);

?>