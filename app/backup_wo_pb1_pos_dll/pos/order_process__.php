<?php
include("../../assets/config/db.php");
session_start();

$user = $_SESSION['userID'];
$outletID = $_SESSION['outletID'];

if(isset($_POST['orderNo'])){

$orderID = $_POST['orderNo'];
$orderPeriode = date("Y-m");
$per = date("Ym");
	
$query = "SELECT count(orderID)+1 as count FROM taborderheader WHERE orderPeriode ='$orderPeriode'";
$res = mysql_query($query);
$row = mysql_fetch_array($res);
$count = $row['count'];
	
$orderID = "PCA/RCP/$per/".str_pad($count,4,"0",STR_PAD_LEFT);
	
$orderNo = $_POST['orderNo'];
$orderDate = $_POST['orderDate'];
$orderAmount = $_POST['totalProduct'];
$dpp = $_POST['dpp'];
$VAT = 0;
$discountPrice = $_POST['discount'];
$total = $_POST['totalPrice'];
$promoID = $_POST['promo'];
$voucherCode = $_POST['voucherCode'];

$isVoucher = 0;
if($voucherCode){
	$isVoucher = 1;
}
	
$paymentAmount = $_POST['payment'];
$paymentMethod = $_POST['paymentMethod'];
$changeAmount = $_POST['changeAmount'];
$payerID = $_POST['customerID'];
$payerName = $_POST['customerName'];
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



$countArr = count($productID);

	if($status == 0){
		//DRAFT
		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
			$rowCheck = mysql_fetch_array($checkID);
		if(mysql_num_rows($checkID)==0){

		/* Input ke OrderHeader status 0 */
		$query = "INSERT INTO taborderheader(orderID,orderNo,orderDate,orderPeriode,orderAmount,outletID,dpp,VAT,discountPrice, total, promoID, isVoucher, voucherID, paymentAmount, paymentMethod, payerName, payerPhone, payerEmail, remarks, status, userID, dateCreated,lastChanged) VALUES('$orderID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$outletID', '$dpp','$VAT','$discountPrice','$total', '$promoID', '$isVoucher', '$voucherCode', '$paymentAmount', '$paymentMethod', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 0, '$user', '$dateCreated', '$lastChanged')";
		$res = mysql_query($query);
		}
		/* END */	

		if($countArr!=0){

			/* Loop produk */
			for($x = 0;$x<$countArr;$x++) {

				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];

				// echo "id ".$product;
				// echo "jumlah ".$amount;
				// echo "harga ".$price;
				// echo "total ".$subtotal;

				/* Insert product ke DB OrderDetail */
				$id = $x+1;
				$queryD = "INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID', '$product', '$amount', '$price','$subtotal', 0, '$dateCreated', '$lastChanged')";
				$resD = mysql_query($queryD);
			}
			
			/* Insert SystemJournal */
			$journalID = date("YmdHis");
			$act = "ORDER_".$orderNo."_".$orderID."_DRAFT";

			$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_DRAFT','$user','$dateCreated','$lastChanged', 'SUCCESS')";
			$resJournal = mysql_query($queryJournal);
			
			echo "<script type='text/javascript'>alert('DRAFT TERSIMPAN!')</script>";
			$URL="/picaPOS/app/pos"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
		}
   
   }else{
   		//ORDER

   		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);

		/* Input ke OrderHeader status 1 */
		if(mysql_num_rows($checkID)==0){

		$query = "INSERT INTO taborderheader(orderID,orderNo,orderDate,orderPeriode,orderAmount,outletID,dpp,VAT,discountPrice, total, promoID, isVoucher, voucherID, paymentAmount, paymentMethod, payerName, payerPhone, payerEmail, remarks, status, userID, dateCreated,lastChanged) VALUES('$orderID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$outletID', '$dpp','$VAT','$discountPrice','$total', '$promoID', '$isVoucher', '$voucherCode', '$paymentAmount', '$paymentMethod', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 1, '$user', '$dateCreated', '$lastChanged')";
		$res = mysql_query($query);

		}

		/* ---------- ADD CUSTOMER ------------ */
	   	$checkIDCust = mysql_query("SELECT * FROM mcustomer WHERE customerID='$payerID'");
	   	$fetching = mysql_fetch_array($checkIDCust);

	   	if(mysql_num_rows($checkIDCust)==0){
	   		if($payerName != null && $payerPhone != null){
		   		$q = "INSERT INTO mcustomer(customerID, customerName, customerPhone, customerEmail, status, dateCreated, lastChanged) VALUES('$payerID', '$payerName', '$payerPhone', '$payerEmail', 1, '$dateCreated', '$lastChanged')";
		   		$r = mysql_query($q);

		   		$actC = "NEW_CUSTOMER_".$payerName;

				$journalIDC = date("YmdHis");
				$queryJournalC = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalIDC','$actC','ADD_CUST_FROM_POS','$user','$dateCreated','$lastChanged', 'SUCCESS')";
				$resJournalC = mysql_query($queryJournalC);
			}
	   	}

	   	
		/* ---------- END ------------ */
		
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

				// echo "id ".$product;
				// echo "jumlah ".$amount;
				// echo "harga ".$price;
				// echo "total ".$subtotal;
				
				$subdatas[productID] = $product;
				$subdatas[amount] = $amount;
				$subdatas[price] = $price;
				$subdatas[subtotal] = $subtotal;
				$datas[] = $subdatas;

				/* Insert product ke DB OrderDetail */
				$id = $x+1;
				$queryD = "INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID', '$product', '$amount', '$price','$subtotal', 1, '$dateCreated', '$lastChanged')";
				$resD = mysql_query($queryD);

				/* Cek produk curStock dan Update curStock */
				$checkPro = mysql_query("SELECT * FROM mproduct WHERE productID = '$product' AND outletID = '$outletID'");
				$rowPro = mysql_fetch_array($checkPro);

				$measurement = $rowPro['measurementID'];
				$curStock = $rowPro['curStock'];

				/* Update Stok Produk */
				$stock = $curStock-$amount;
				$queryUpdPro = mysql_query("UPDATE mproduct SET 
												curStock = '$stock'
											WHERE productID = '$product' AND outletID = '$outletID'");

				/* Insert ProductHistory */
				$journalID = date("YmdHis");
				$queryProHistory = mysql_query("INSERT INTO tabproducthistory(id,transType,productID,amount,itemAmount,measurementID,userID,status,remarks,dateCreated,lastChanged)VALUES('$journalID','OUT','$product','$amount','$stock','$measurement','$user',1,'$orderID','$dateCreated','$lastChanged')");

			}
			
					
	   	}
		/* Insert SystemJournal */
		$act = "ORDER_".$orderNo."_".$orderID."_COMPLETE";

		$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_COMPLETE','$user','$dateCreated','$lastChanged', 'SUCCESS')";
		$resJournal = mysql_query($queryJournal);
		
		//printOrder($data,$datas);
		$data_enc = json_encode($data);
		$datas_enc = json_encode($datas);
		
		$URL="/picaPOS/app/pos"; 
		$URL2="order_print.php?data=$data_enc&datas=$datas_enc"; 

		echo "<script>window.open('$URL2');</script>";
		echo "<script type='text/javascript'>location.replace('$URL');</script>";

	}
}
?>