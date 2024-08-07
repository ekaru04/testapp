<?php
session_start();
include("../../assets/config/db.php");

$openDate = $_GET[openDate];
$outletID = $_GET[outletID];

echo $closeDate;

$query = "SELECT nominalOpen FROM tabopencashier WHERE openDate = '$openDate' AND outletID = '$outletID'";
// print_r($query);
$res = mysql_query($query);

$x=0;
$dataModal = array();
$row=mysql_fetch_array($res);

$dataModal[nominalOpen] = $row['nominalOpen'];

echo json_encode($dataModal);
?>