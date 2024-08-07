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
	$modalCash = $_POST['modalCash'];
	$modalBca = $_POST['modalBca'];
	$modalMandiri = $_POST['modalMandiri'];
	$modalMas = $_POST['modalMas'];
	$dateCreated = date("Y-m-d H:i:s");
    $lastChanged = date("Y-m-d H:i:s");

	$checkOpenID = mysql_query("SELECT * FROM tabopencashier WHERE openID='$openID'");
	$rowCheck = mysql_fetch_array($checkOpenID);

	/**
	 * Modal Type code
	 * 1 = CASH
	 * 2 = BCA
	 * 3 = MANDIRI
	 * 4 = MAS
	 */

	if($modalCash != 0) {
		$insertCash = mysql_query("INSERT INTO tabopencashierdetail (openDetailID, openID, modalType, nominalModal, dateCreated, lastChanged)
									VALUES ('', '$openID', '1', '$modalCash', '$dateCreated', '$lastChanged')");
	} else {

	}

	if($modalBca != 0) {
		$insertBca = mysql_query("INSERT INTO tabopencashierdetail (openDetailID, openID, modalType, nominalModal, dateCreated, lastChanged)
									VALUES ('', '$openID', '2', '$modalBca', '$dateCreated', '$lastChanged')");
	} else {

	}

	if($modalMandiri != 0) {
		$insertMandiri = mysql_query("INSERT INTO tabopencashierdetail (openDetailID, openID, modalType, nominalModal, dateCreated, lastChanged)
										VALUES ('', '$openID', '3', '$modalMandiri', '$dateCreated', '$lastChanged')");
	} else {

	}

	if($modalMas != 0) {
		$insertMas = mysql_query("INSERT INTO tabopencashierdetail (openDetailID, openID, modalType, nominalModal, dateCreated, lastChanged)
									VALUES ('', '$openID', '4', '$modalMas', '$dateCreated', '$lastChanged')");
	} else {

	}

	$sumNominal = $modalCash+$modalBca+$modalMandiri+$modalMas;

	if(mysql_num_rows($checkOpenID) == 0){
		$insertOpen = mysql_query("INSERT INTO tabopencashier (openID, openDate, openPeriode, outletID, nominalOpen, username, dateCreated, lastChanged)
									VALUES ('$openID', '$openDate', '$period', '$outlet', '$sumNominal', '$username', '$dateCreated', '$lastChanged')");

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