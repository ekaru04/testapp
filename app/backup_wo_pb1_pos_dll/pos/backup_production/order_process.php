<?php
include("../../assets/config/db.php");
session_start();

$user = $_SESSION['userID'];
$outletID = $_SESSION['outletID'];

if(isset($_POST['orderNo'])){

$orderID = $_POST['orderNo'];
$orderPeriode = date("Y-m");
$per = date("Ym");

$queryIDLoyal = mysql_query("SELECT * FROM mloyalty");
$fetchinLoyal = mysql_fetch_array($queryIDLoyal);
$require = $fetchinLoyal['requirement'];
$point = $fetchinLoyal['point'];
	
$query = "SELECT count(orderID)+1 as count FROM taborderheader WHERE orderPeriode ='$orderPeriode'";
$res = mysql_query($query);
$row = mysql_fetch_array($res);
$count = $row['count'];
	
$priceID = $_POST['priceID'];

$orderID = "PCA/RCP/$per/".str_pad($count,4,"0",STR_PAD_LEFT);
	
$orderNo = $_POST['orderNo'];
$orderDate = $_POST['orderDate'];
$orderAmount = $_POST['totalProduct'];
$dpp = $_POST['dpp'];
// $VAT = 0;
$discountPrice = $_POST['discount'];
$total = $_POST['totalPrice'];
$orderMethod = $_POST['orderMethod'];
$promoID = $_POST['promo'];
$voucherCode = $_POST['voucherCode'];

$isVoucher = 0;
if($voucherCode){
	$isVoucher = 1;
}
	
$paymentAmount = $_POST['payment'];
// $orderType = $_POST['orderType'];
$paymentMethod = $_POST['paymentMethod'];
$changeAmount = $_POST['changeAmount'];
$payerID = $_POST['customerID'];
$payerName = strtoupper($_POST['customerName']);
$payerPhone = $_POST['customerPhone'];
$payerEmail = $_POST['customerEmail'];
$status = $_POST['status'];
$remarks = $_POST['remarks'];
$dateCreated = date("Y-m-d");
$lastChanged = date("Y-m-d H:i:s");

$productID = $_POST['productID'];
$productAmount = $_POST['productQty'];
$productPrice = $_POST['productPrice'];
$productSubtotal = $_POST['productSubtotal'];
$servCharge5 = $_POST['servCharge5'];
$pb1 = $_POST['pb10'];

	
$data = array();
$datas = array();
	
$data[orderID] = $orderID;
$data[orderNo] = $orderNo;
$data[orderDate] = $orderDate;
$data[orderAmount] = $orderAmount;
$data[dpp] = $dpp;
$data[vat] = $VAT;
$data[discountPrice] = $discountPrice;
$data[total] = $total;
$data[promoID] = $promoID;
$data[voucherCode] = $voucherCode;
$data[isVoucher] = $isVoucher;
$data[paymentAmount] = $paymentAmount;
$data[paymentMethod] = $paymentMethod;
$data[changeAmount] = $changeAmount;
$data[payerName] = $payerName;
$data[payerPhone] = $payerPhone;
$data[payerEmail] = $payerEmail;
$data[remarks] = $remarks;

// print_r($productID);
// print_r($productAmount);
// print_r($productPrice);

$totalServCharge5 = 0;
$totalPb1 = 0;

$paymentOrderID = date('Ymd');

$countArr = count($productID);

	if($status == 0){
		//DRAFT
		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);

		if(mysql_num_rows($checkID)==0){

		/* Input ke OrderHeader status 0 */
		$query = mysql_query("INSERT INTO taborderheader(orderID,priceID,orderNo,orderDate,orderPeriode,orderAmount,orderMethod,outletID,payerName, payerPhone, payerEmail, remarks, status, userID, dateCreated,lastChanged) VALUES('$orderID', '$priceID', '$orderNo', '$orderDate', '$orderPeriode', '$orderAmount', '$orderMethod', '$outletID', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 0, '$user', '$dateCreated', '$lastChanged')");

		/* Input ke PaymerOrder */

		$queryPayment = mysql_query("INSERT INTO tabpaymentorder(id,orderID,orderMethod,paymentType,paymentMethod,paymentAmount,paymentDate,dpp,VAT,discountPrice,total,promoID,isVoucher,voucherID,remarks,status,dateCreated,lastChanged) VALUES ('$paymentOrderID','$orderID','$orderMethod','null','$paymentMethod','$paymentAmount','$orderDate','$dpp','$VAT','$discountPrice','$total','$promoID','$isVoucher','$voucherCode','$remarks',0,'$dateCreated','$lastChanged')");

		/* END */
		
		}
		/* END */

		

		if($countArr!=0){

			/* Loop produk */
			for($x=0;$x<$countArr;$x++) {

				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];
				$totalServCharge5 += $servCharge5[$x];
				$totalPb1 += $pb1[$x];

				/* Insert product ke DB OrderDetail */
				$id = $x+1;
				$queryD = mysql_query("INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status,dateCreated,lastChanged)VALUES('$id','$orderID', '$product', '$amount', '$price', '$totalServCharge5', '$totalPb1', '$subtotal', 0, '$dateCreated', '$lastChanged')");
			}
			
			/* Insert SystemJournal */
			$journalID = date("YmdHis");
			$act = "ORDER_".$orderNo."_".$orderID."_DRAFT";

			// $queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_DRAFT','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			
			echo "<script type='text/javascript'>alert('DRAFT TERSIMPAN!')</script>";
			$URL="/picaPOS/app/pos"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
		}
   
   } else {
   		//ORDER

   		/* Get Customer */
	   	$checkIDCust = mysql_query("SELECT * FROM mcustomer WHERE customerName ='$payerName'");
	   	$fetching = mysql_fetch_array($checkIDCust);
	   	/* Get Loyalty */
	   	$checkLoyalty = mysql_query("SELECT * FROM tabloyalty l
									INNER JOIN mcustomer c ON c.customerID = l.customerID
									WHERE c.customerName = '$payerName'");
	   	$fetchinLoyalty = mysql_fetch_array($checkLoyalty);
	   	$loyaltyP = $fetchinLoyalty['loyaltyPoint'];

	   	/* ---------- ADD CUSTOMER ------------ */
	   	if(mysql_num_rows($checkIDCust)==0)
	   	{
	   		if($payerName != null && $payerPhone != null):
	   		
		   		$q = mysql_query("INSERT INTO mcustomer(customerID, customerName, customerPhone, customerEmail, status, dateCreated, lastChanged) VALUES('$payerID', '$payerName', '$payerPhone', '$payerEmail', 1, '$dateCreated', '$lastChanged')");

		   		$actC = "NEW_CUSTOMER_".$payerName;

				$journalIDC = date("YmdHis");
				$queryJournalC = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalIDC','$actC','ADD_CUST_FROM_POS','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			endif;
	   	}
	   	/* ---------- END ------------ */
	   	
	   	/* Cek Loyalty jika tidak ada */
	   	if(mysql_num_rows($checkLoyalty)==0)
	   	{
	   		/* Jika pembelian lebih dari 50k, insert tabloyalty dengan point 10 */
	   		if($total > $require):
	   		
	   			$ql = mysql_query("INSERT INTO tabloyalty(loyaltyID, customerID, outletID, loyaltyPoint, status, dateCreated, lastChanged)VALUES('$fetchinLoyal[loyaltyID]', '$payerID', '$outletID', '$point', 1, '$dateCreated', '$lastChanged')");
	   		
	   		endif;
	   		
	   	}
	   	/* Jika Loyalty ada */
	   	else
	   	
	   	{
	   		/* Jika pembelian lebih dari 50k, update loyalty point */
	   		if($total > $require):

	   			$finalPoint = $loyaltyP + $point;
	   			$ql = mysql_query("UPDATE tabloyalty SET loyaltyPoint = '$finalPoint', lastChanged = '$lastChanged' WHERE customerID = '$fetching[customerID]'");
	   			echo "Ini id customernya :". $fetching['customerID'];

	   		endif;
	   	}	   	
		/* ---------- END ------------ */


   		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);

		/* Input ke OrderHeader status 1 */
		if(mysql_num_rows($checkID)==0){


		$query = mysql_query("INSERT INTO taborderheader(orderID,priceID,orderNo,orderDate,orderPeriode,orderAmount,orderMethod,outletID,payerName, payerPhone, payerEmail, remarks, status, userID, dateCreated,lastChanged) VALUES('$orderID', '$priceID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$orderMethod', '$outletID', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 1, '$user', '$dateCreated', '$lastChanged')");

		/* Input ke PaymerOrder */


		/* END */

		}

		

		// $query = mysql_query("INSERT INTO taborderheader(orderID,orderNo,orderDate,orderPeriode,orderAmount,outletID,dpp,VAT,discountPrice, total, promoID, isVoucher, voucherID, paymentAmount, paymentMethod, payerName, payerPhone, payerEmail, remarks, status, userID, dateCreated,lastChanged) VALUES('$orderID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$outletID', '$dpp','$VAT','$discountPrice','$total', '$promoID', '$isVoucher', '$voucherCode', '$paymentAmount', '$paymentMethod', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 1, '$user', '$dateCreated', '$lastChanged')");
		// }
		
		/* Update Status Voucher */
		$queryVo = mysql_query("UPDATE mvoucher SET status = 2 WHERE voucherCode='$voucherCode'");
		
		if($countArr!=0){
			/* Loop produk */
			for($x = 0;$x<$countArr;$x++) {
				$subdatas = array();
				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];

				$totalServCharge5 += $servCharge5[$x];
				$totalPb1 += $pb1[$x];
				
				$subdatas[productID] = $product;
				$subdatas[amount] = $amount;
				$subdatas[price] = $price;
				$subdatas[subtotal] = $subtotal;
				$datas[] = $subdatas;

				/* Insert product ke DB OrderDetail */
				$id = $x+1;
				$queryD = "INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID', '$product', '$amount', '$price', '$totalServCharge5', '$totalPb1', '$subtotal', 1, '$dateCreated', '$lastChanged')";
				$resD = mysql_query($queryD);

				/* Cek produk curStock dan Update curStock */
				$checkPro = mysql_query("SELECT * FROM mproduct WHERE productID = '$product' AND outletID = '$outletID'");
				$rowPro = mysql_fetch_array($checkPro);

				$measurement = $rowPro['measurementID'];
				$curStock = $rowPro['curStock'];

				/* Update Stok Produk */
				$stock = $curStock-$amount;
				$queryUpdPro = mysql_query("UPDATE mproduct SET curStock = '$stock'	WHERE productID = '$product' AND outletID = '$outletID'");

				/* Insert ProductHistory */
				$journalID = date("YmdHis");
				$queryProHistory = mysql_query("INSERT INTO tabproducthistory(id,transType,productID,amount,amountLeft,measurementID,userID,status,remarks,dateCreated,lastChanged)VALUES('$journalID','OUT','$product','$amount','$stock','$measurement','$user',1,'$orderID','$dateCreated','$lastChanged')");

			}
			$VAT = $totalServCharge5+$totalPb1;
			$queryPayment = mysql_query("INSERT INTO tabpaymentorder(id,orderID,orderMethod,paymentType,paymentMethod,paymentAmount,dpp,VAT,discountPrice,total,promoID,isVoucher,voucherID,remarks,status,dateCreated,lastChanged) VALUES ('$paymentOrderID','$orderID','$orderMethod','null','$paymentMethod','$paymentAmount','$dpp','$VAT','$discountPrice','$total','$promoID','$isVoucher','$voucherCode','$remarks',1,'$dateCreated','$lastChanged')");
			
					
	   	}
		/* Insert SystemJournal */
		$act = "ORDER_".$orderNo."_".$orderID."_COMPLETE";

		$queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_COMPLETE','$user','$dateCreated','$lastChanged', 'SUCCESS')");
		
		// printOrder($data,$datas);
		$data_enc = json_encode(array_merge($data, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1)));
		$datas_enc = json_encode(array_merge($datas, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1)));
		
		$URL="/picaPOS/app/pos"; 
		$URL2="order_print.php?data=$data_enc&datas=$datas_enc"; 

		echo "<script>window.open('$URL2');</script>";
		echo "<script type='text/javascript'>location.replace('$URL');</script>";

	}
}
?>