<?php
include("../../assets/config/db.php");

$query = "SELECT * FROM mVoucher WHERE voucherCode = '$_GET[voucherCode]' AND status = 1";
$res = mysql_query($query);

$data = array();
$today = date("Y-m-d");
if(mysql_num_rows($res)==0){
	$data[voucherName] = "";
	$data[voucherType] = "";
	$data[voucherSaldo] = "";
	$data[expDate] = "";
	$data[desc] = "";
	$data[status] = "NO DATA";
}else{
	$row=mysql_fetch_array($res);
	$data[voucherName] = $row['voucherName'];
	$data[voucherType] = $row['voucherType'];
	$data[voucherSaldo] = $row['voucherSaldo'];
	$data[voucherRequirement] = $row['voucherRequirement'];

	$expDate = date("Y-m-d",strtotime($row['expDate']));

	$data[expDate] = $expDate;


	if($expDate<$today){
		$data[desc] = "VOUCHER EXPIRED!";
		$data[status] = "EXPIRED";
	}else{
		$data[desc] = $row['voucherName'];
		$data[status] = "AVAILABLE";
	}
}


	
echo json_encode($data);
?>