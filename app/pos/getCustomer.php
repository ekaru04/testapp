<?php
session_start();
include("../../assets/config/db.php");

$query = "SELECT customerName, customerPhone, customerEmail FROM mcustomer WHERE customerID = '$_GET[customerID]' AND status = 1 
            UNION ALL 
            SELECT employeeName, employeePhone, employeeEmail FROM mEmployee WHERE employeeID = '$_GET[customerID]' AND status =1";
$res = mysql_query($query);

$data = array();
$row=mysql_fetch_array($res);
$data[customerName] = $row['customerName'];
$data[customerPhone] = $row['customerPhone'];
$data[customerEmail] = $row['customerEmail'];
	
echo json_encode($data);

?>