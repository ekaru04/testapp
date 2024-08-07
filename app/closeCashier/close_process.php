<?php
session_start();
if (!isset($_SESSION["username"])) 
{
    $URL="/picapos/admin"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
} 
include("../../assets/config/db.php");

$user = $_SESSION["userID"];
$outletID = $_SESSION["outletID"];
$username = $_SESSION['username'];
if (isset($_POST['closeID']))
{
	$closeID = $_POST['closeID'];
	$closeDate = $_POST['closeDate'];
	$closePeriode = $_POST['closePeriode'];
	$closeShift = $_POST['closeShift'];
	$dpp = $_POST['dppTotal'];
	$vat = $_POST['taxTotal'];
	$firstModal = $_POST['firstModal'];
	$newModal = $_POST['newModal'];
	$outlay = $_POST['outTotal'];
	$grandTotal = $_POST['grandTotal'];
	$finalOwn = $_POST['finalOwn'];
	// $totalReceived = $_POST['totalReceived'];
	$remarks = $_POST['remarks'];
	$dateCreated = date("Y-m-d");
    $lastChanged = date("Y-m-d H:i:s");


	$checkID = mysql_query("SELECT * FROM tabclosecashierheader WHERE closeID='$closeID'");
	$rowCheck = mysql_fetch_array($checkID);

	// $var = array($requestID, $requestDate, $categoryID, $outletID, $productID, $amount, $measurementID, $userID, $dateCreated, $lastChanged);

	// print_r($var);


	if(mysql_num_rows($checkID)==0){
		$query = "INSERT INTO tabclosecashierheader (closeID, closeDate, closePeriode, closeShift, outletID, firstModal, newModal, dpp, vat, grandTotal, totalReceived, remarks, username, status, dateCreated, lastChanged) 
					VALUES('$closeID', '$closeDate', '$closePeriode', '$closeShift', '$outletID', '$firstModal', '$newModal', '$dpp', '$vat', '$grandTotal', '$finalOwn', '$remark', '$username', '1', '$dateCreated', '$lastChanged')";
		$res = mysql_query($query);
		
	}


		$queryDetail = mysql_query("SELECT h.orderID, h.orderNo, h.orderDate, h.orderPeriode, h.orderAmount, p.total, p.dpp, p.vat, p.paymentAmount, p.paymentMethod, h.payerName, p.discountPrice, p.promoID, m.methodName, h.username, u.fullname, h.remarks FROM taborderheader h 
						INNER JOIN tabpaymentorder p ON h.orderID = p.orderID 
						INNER JOIN mpaymentmethod m ON p.paymentMethod = m.methodID
						INNER JOIN muser u ON h.username = u.username
						WHERE h.status = 1 AND h.username = '$username' AND h.outletID = '$outletID' AND h.orderDate = '$closeDate' ORDER BY h.orderNo");
		
		while($rows = mysql_fetch_array($queryDetail)){
			$id = date("YmdHis");
			$orderID = $rows['orderID'];
			$dpps = $rows['dpp'];
			$vats = $rows['vat'];
			$discountPrice = $rows['discountPrice'];
			$promoID = $rows['promoID'];
			$totals = $rows['total'];
			$paymentAmount = $rows['paymentAmount'];
			$paymentMethod = $rows['paymentMethod'];
			$payerName = $rows['payerName'];
			$remark = $rows['remarks'];

			$insertDetail = mysql_query("INSERT INTO tabclosecashierdetail (id,orderID,closeID,dpp,VAT,discountPrice,total,promoID,paymentAmount,paymentMethod,payerName,remarks,username,status,dateCreated,lastChanged) 
											VALUES ('$id','$orderID','$closeID','$dpps','$vats','$discountPrice','$totals','$promoID','$paymentAmount','$paymentMethod','$payerName','$remark','$username','1','$dateCreated','$lastChanged')");
		}

		$journalID = date("YmdHis");
		$act = "INSERT_CLOSE_CASHIER_".$closeID;

		$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$act','CLOSE_CASHIER','$username','$dateCreated','$lastChanged', 'SUCCESS')";
		$resJournal = mysql_query($queryJournal);

		echo "<script type='text/javascript'>alert('Data tersimpan!')</script>";
	
}
	 $URL="/picaPOS/app/closeCashier"; 
     echo "<script type='text/javascript'>location.replace('$URL');</script>";
?>