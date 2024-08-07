<?php
session_start();
include("../../assets/config/db.php");

// $closeID = $_GET[closeID];
$closeDate = $_GET[closeDate];
$outletID = $_GET[outletID];
// $closeShift = $_GET[closeShift];

// if($closeShift == 1) {
//     $date = date('Y-m-d 00:00:00');
// } else {
//     $date = date('Y-m-d 12:00:01');
// }

$query = "SELECT h.orderID, sum(p.total) AS total, sum(p.dpp) AS dpp, sum(p.VAT) AS VAT, sum(p.paymentAmount) AS paymentAmount, h.lastChanged FROM taborderheader h 
INNER JOIN tabpaymentorder p ON h.orderID = p.orderID
INNER JOIN mpaymentmethod m ON p.paymentMethod = m.methodID 
INNER JOIN muser u ON h.username = u.username WHERE h.status = 1 AND h.outletID = '$outletID' AND h.orderDate = '$closeDate' ORDER BY h.orderNo";
$res = mysql_query($query);

$x=0;
$data = array();
$row=mysql_fetch_array($res);

// $dpp = number_format($row['dpp']);
// $VAT = number_format($row['VAT']);
// $total = number_format($row['total']);
// $paymentAmount = number_format($row['paymentAmount']);

$data[dpp] = $row['dpp'];
$data[VAT] = $row['VAT'];
$data[total] = $row['total'];
$data[paymentAmount] = $row['paymentAmount'];

$array = array();
$queryM = mysql_query("SELECT * FROM mpaymentmethod");
$rowM = array();
while($q = mysql_fetch_array($queryM))
{
	$rowM[] = $q;
}


$arrayNew = array();

foreach($rowM as $resM)
{
	$querySplit = mysql_query("SELECT SUM(p.total) AS splitTotal FROM tabpaymentorder p
									INNER JOIN taborderheader o ON p.orderID = o.orderID
									WHERE p.status = 1 AND p.paymentMethod = '$resM[methodID]' AND o.orderDate = '$closeDate'");
	$split = mysql_fetch_array($querySplit);
	$temp = array();
	$temp['id'] = '#payID_Method_'.$resM[methodID];
	$temp['id2'] = '#Meth_Method_'.$resM[methodID];
	$temp['total'] = $split[splitTotal];
	array_push($arrayNew, $temp);
}
$data['arrayNew'] = $arrayNew;	

echo json_encode($data);
?>