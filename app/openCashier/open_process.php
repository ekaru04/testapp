<?php
session_start();  
include("../../assets/config/db.php");

$user = $_SESSION["userID"];
$outlet = $_SESSION["outletID"];
$username = $_SESSION['username'];
if (isset($_POST['submit']))
{
	$openID = $_POST['openID'];
	$openDate = $_POST['openDate'];
	$period = $_POST['periode'];
	$modal = $_POST['modal'];
	$dateCreated = date("Y-m-d H:i:s");
    $lastChanged = date("Y-m-d H:i:s");

	$checkOpenID = mysql_query("SELECT * FROM tabopencashier WHERE openID='$openID'");
	$rowCheck = mysql_fetch_array($checkOpenID);


	$sumNominal = $modalCash+$modalBca+$modalMandiri+$modalMas;

	if(mysql_num_rows($checkOpenID) == 0){
		$insertOpen = mysql_query("INSERT INTO tabopencashier (openID, openDate, openPeriode, outletID, nominalOpen, username, dateCreated, lastChanged)
									VALUES ('$openID', '$openDate', '$period', '$outlet', '$modal', '$username', '$dateCreated', '$lastChanged')");

		$journalID = date("YmdHis");
		$act = "OPEN_CASHIER_".$openID;
		$queryJournal = mysql_query("INSERT INTO systemJournal(journalID, activity, menu, username, dateCreated, logCreated, status) 
										VALUES('$journalID', '$act', 'OPEN_CASHIER', '$username', '$dateCreated', '$lastChanged', 'SUCCESS')");

		echo "<script type='text/javascript'>alert('Data tersimpan!')</script>";
	}
}
	$URL="/picaPOS/app/pos"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
?>