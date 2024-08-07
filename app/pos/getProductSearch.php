<?php
session_start();
include('../../assets/config/db.php');

$categoryID = $_GET['categoryID'];
$productName = $_GET['productName'];
$query = null;
if(strlen($categoryID) > 0 && strlen($productName) > 0){
	$query = "SELECT * FROM mProduct WHERE (categoryID = '$_GET[categoryID]' AND productName LIKE '%$_GET[productName]%') AND status = 1 AND outletID = '$_SESSION[outletID]'";
}
else if(strlen($categoryID) > 0){
	$query = "SELECT * FROM mProduct WHERE categoryID = '$_GET[categoryID]' AND status = 1 AND outletID = '$_SESSION[outletID]'";
}
else{
	$query = "SELECT * FROM mProduct WHERE productName LIKE '%$_GET[productName]%' AND status = 1 AND outletID = '$_SESSION[outletID]'";
}

$bundle = mysql_query("SELECT bundleID productID,bundleName productName,curStock, bundleImage productImage, bundlePrice productPrice FROM tabbundleheader WHERE status = 1 AND outletID = '$_SESSION[outletID]'");

$res = mysql_query($query);

$data = array();
while($row = mysql_fetch_array($res)){
	$_productName = $row['productName'];
	$_productPrice = $row['productPrice'];
	$_productStock = $row['curStock'];
	$_productImage = $row['productImage'];
	$_productID = $row['productID'];
	array_push($data, array('productName' => $_productName, 'productPrice' => $_productPrice, 'curStock' => $_productStock, 'productImage' => $_productImage, 'productID' => $_productID));
}

while($row = mysql_fetch_array($bundle)){
	$_productName = $row['productName'];
	$_productPrice = $row['productPrice'];
	$_productStock = $row['curStock'];
	$_productImage = $row['productImage'];
	$_productID = $row['productID'];
	array_push($data, array('productName' => $_productName, 'productPrice' => $_productPrice, 'curStock' => $_productStock, 'productImage' => $_productImage, 'productID' => $_productID));
}
	echo json_encode($data);
?>