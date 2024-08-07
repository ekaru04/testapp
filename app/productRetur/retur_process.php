<?php
session_start();
if (!isset($_SESSION["username"])) 
{
    $URL="/picapos/admin"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
} 
include("../../assets/config/db.php");

// print_r($_POST['requestID']);
$user      = $_SESSION["userID"];
$username = $_SESSION['username'];
if (isset($_POST['returID']))
{
	$returID = $_POST['returID'];
	$returDate = date('Y-m-d');
	$outletID = $_POST['outletID'];
	$categoryID = $_POST['categoryID'];
	$productID = $_POST['productID'];
	$stockRetur = $_POST['stockRetur'];
	$dateCreated = date("Y-m-d H:i:s");
    $lastChanged = date("Y-m-d H:i:s");

	$queryCategory = mysql_query("SELECT * FROM mCategory WHERE categoryID = '$categoryID'");
	$fetch = mysql_fetch_array($queryCategory);
	$categoryName = $fetch['categoryName'];

	$data = array();

	
	$checkID = mysql_query("SELECT * FROM tabreturproduct WHERE returID='$returID'");
	$rowCheck = mysql_fetch_array($checkID);
	
	$getProduct = mysql_query("SELECT * FROM mproduct WHERE productID = '$productID'");
	$rowProduct = mysql_fetch_array($getProduct);
	$productStock = $rowProduct['curStock'];
	$productName = $rowProduct['productName'];

	$data[returID] = $returID;
	$data[returDate] = $returDate;
	$data[categoryName] = $categoryName;
	$data[productName] = $productName;
	$data[stockRetur] = $stockRetur;
	$data[dateCreated] = $dateCreated;
	// echo $productStock;
	// echo "<br>";
	// echo $productStock - $stockRetur;
	// exit;

	/**
	 * Retur Status
	 * 1 = APPROVE
	 * 2 = WAITING APPROVAL
	 * 3 = CANCEL/REJECT
	 */

	if(mysql_num_rows($checkID)==null){
		$query = "INSERT INTO tabreturproduct(returID, returDate, outletID, categoryID, productID, returAmount, username, returStatus, approvedBy, approvedDate, approvedReason, dateCreated, lastChanged) 
                    VALUES('$returID', '$returDate', '$outletID', '$categoryID', '$productID', '$stockRetur', '$username', '2', '', '', '', '$dateCreated', '$lastChanged')";
		$res = mysql_query($query);

		$journalID = date("YmdHis");
		$act = "INSERT_RETUR_".$returID;

		$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$act','REQUEST_INGREDIENT','$username','$dateCreated','$lastChanged', 'SUCCESS')";
		$resJournal = mysql_query($queryJournal);

		echo "<script type='text/javascript'>alert('Data tersimpan!')</script>";
	}
		// else{
	// 	$query = "UPDATE mpaymentmethod SET
	// 			methodID='$methodID',
	// 			methodName='$methodName',
	// 			methodType='$methodType',
	// 			remarks='$remarks',
	// 			lastChanged='$lastChanged'
	// 			WHERE methodID='$methodID'
	// 			";
	// 			$res = mysql_query($query);

	// 	echo "<script type='text/javascript'>alert('Data Berhasil dirubah!')</script>";
	// }
	// $res = mysql_query($query);
	
}
	$data_enc = json_encode($data);
	$URL="/picaPOS/app/productRetur/retur_input.php?returID=$returID"; 
	$URL2="retur_print.php?data=$data_enc";
	
	echo "<script>window.open('$URL2');</script>";
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
?>