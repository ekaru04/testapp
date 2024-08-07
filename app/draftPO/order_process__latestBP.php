<?php
include("../../assets/config/db.php");
session_start();

$user = $_SESSION['userID'];
$outletID = $_SESSION['outletID'];

if(isset($_POST['preorderID'])){


$preorderID = $_POST['preorderID'];
$preorderNo = $_POST['preorderNo'];
$preorderDate = $_POST['preorderDate'];
$preorderAmount = $_POST['totalProduct'];
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
$paymentType = $_POST['paymentType'];
$paymentDate = date('Y-m-d', strtotime($_POST['paymentDate']));
$downPayment = $_POST['downPayment'];
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

$paymentOrderID = date('Ymd');

$queryIDLoyal = mysql_query("SELECT COUNT(loyaltyID)+1 as loyaltyID FROM tabloyalty");
$fetchinLoyal = mysql_fetch_array($queryIDLoyal);
$point = $fetchinLoyal['point'];

$countArr = count($productID);

	if($status == 3){
		//DP
		// $checkID = mysql_query("SELECT * FROM tabpreorderheader WHERE preorderID='$preorderID' AND outletID='$outletID' AND paymentType = 'DOWNPAYMENT'");
		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$preorderID' AND outletID='$outletID' AND paymentType = 'DOWNPAYMENT'");
			$rowCheck = mysql_fetch_array($checkID);
		if(mysql_num_rows($checkID)!=0){

		/* Update OrderHeader status 3 */

		// $query = mysql_query("UPDATE tabpreorderheader SET preorderAmount='$preorderAmount', status='0', remarks='$remarks', lastChanged='$lastChanged'	WHERE preorderID = '$preorderID' AND outletID = '$outletID'");
		$query = mysql_query("UPDATE taborderheader SET orderAmount='$preorderAmount', status='3', remarks='$remarks', lastChanged='$lastChanged'	WHERE orderID = '$preorderID' AND outletID = '$outletID'");

		$queryPayment = mysql_query("UPDATE tabpaymentorder SET paymentMethod = '$paymentMethod', paymentAmount = '$paymentAmount', dpp = '$dpp', VAT = '$VAT', discountPrice = '$discountPrice', total = '$total', promoID = '$promoID', isVoucher = '$isVoucher',  
									voucherID = '$voucherCode', status = '3', lastChanged = '$lastChanged' WHERE orderID = '$preorderID'");
		// $query = "UPDATE tabpreorderheader SET 
		// 		orderAmount='$orderAmount',
		// 		dpp='$dpp',
		// 		discountPrice='$discountPrice',
		// 		total='$total',
		// 		promoID = '$promoID',
		// 		paymentAmount='$paymentAmount',
		// 		paymentMethod='$paymentMethod',
		// 		status='0',
		// 		remarks='$remarks',
		// 		lastChanged='$lastChanged'
		// 		WHERE orderID = '$orderID' AND outletID = '$outletID'";
		}
		/* END */	

		if($countArr!=0){

			/* Loop produk */
			for($x = 0;$x<$countArr;$x++) {

				$detailID= $id[$x];
				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];

				// echo "id ".$product;
				// echo "jumlah ".$amount;
				// echo "harga ".$price;
				// echo "total ".$subtotal;

				/* update product ke DB OrderDetail */
				$id = $x+1;
				// $queryD = "INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID', '$product', '$amount', '$price','$subtotal', 0, '$dateCreated', '$lastChanged')";

				#check apakah order detailnya ada, kalau gaada insert.
				// $resChk = mysql_query("SELECT * FROM tabpreorderdetail WHERE preorderID = '$preorderID' AND id = '$detailID'");
				$resChk = mysql_query("SELECT * FROM taborderdetail WHERE orderID = '$preorderID' AND id = '$detailID'");

				if(mysql_num_rows($resChk)==0){
					// $queryD = mysql_query("INSERT INTO tabpreorderdetail(id,preorderID,productID,productAmount,productPrice,productSubtotal,status,dateCreated,lastChanged)VALUES('$detailID','$preorderID', '$product', '$amount', '$price','$subtotal', 2, '$dateCreated', '$lastChanged')");
					$queryD = mysql_query("INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,productSubtotal,status,dateCreated,lastChanged)VALUES('$detailID','$preorderID', '$product', '$amount', '$price','$subtotal', 3, '$dateCreated', '$lastChanged')");
				}else{
					// $queryD = mysql_query("UPDATE tabpreorderdetail SET productID='$product',productAmount='$amount',
					// 						productPrice='$price',productSubtotal='$subtotal',status='2',lastChanged='$lastChanged' WHERE preorderID = '$preorderID' AND id='$detailID'");
					$queryD = mysql_query("UPDATE taborderdetail SET productID='$product',productAmount='$amount',
											productPrice='$price',productSubtotal='$subtotal',status='2',lastChanged='$lastChanged' WHERE orderID = '$preorderID' AND id='$detailID'");
				}
				/* END */
			}
			/* Ambil status 3 dan hapus */
			// $delOrder = mysql_query("DELETE FROM tabpreorderdetail WHERE preorderID='$preorderID' AND status='0'");
			$delOrder = mysql_query("DELETE FROM taborderdetail WHERE orderID='$preorderID' AND status='3'");
			/* END */

			/* Ubah status 2 ke 0 */
			// $queryDetail = mysql_query("UPDATE tabpreorderdetail SET status='0', lastChanged='$lastChanged' 
			// 							WHERE preorderID = '$preorderID' AND status ='2'");
			$queryDetail = mysql_query("UPDATE tabpreorderdetail SET status='0', lastChanged='$lastChanged' 
										WHERE preorderID = '$preorderID' AND status ='2'");
			/* END */	
			
			/* Insert SystemJournal */
			$journalID = date("YmdHis");
			$act = "ORDER_".$orderNo."_".$orderID."_DRAFT";

			$queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_DRAFT','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			
			echo "<script type='text/javascript'>alert('DRAFT TERSIMPAN!')</script>";
			$URL="/picaPOS/app/order"; 
			// echo "<script type='text/javascript'>location.replace('$URL');</script>";
		}
   
   }else{
   		//PELUNASAN

   		$checkID = mysql_query("SELECT * FROM tabpreorderheader WHERE preorderID='$preorderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);

		/* Input ke OrderHeader status 1 */
		if(mysql_num_rows($checkID)!=0){

		// $query = "INSERT INTO taborderheader(orderID,orderNo,orderDate,orderPeriode,orderAmount,outletID,dpp,VAT,discountPrice, total, promoID, isVoucher, voucherID, paymentAmount, paymentMethod, payerName, payerPhone, payerEmail, remarks, status, userID, dateCreated,lastChanged) VALUES('$orderID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$outletID', '$dpp','$VAT','$discountPrice','$total', '$promoID', '$isVoucher', '$voucherCode', '$paymentAmount', '$paymentMethod', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 1, '$user', '$dateCreated', '$lastChanged')";
		// $res = mysql_query($query);

		/* Update OrderHeader status 2 */

		$query = mysql_query("UPDATE tabpreorderheader SET 
						preorderAmount = '$preorderAmount', status = '1', remarks = '$remarks', lastChanged = '$lastChanged'
						WHERE preorderID = '$preorderID' AND outletID = '$outletID'");

		$updatePayment = mysql_query("UPDATE tabpaymentorder SET paymentMethod = '$paymentMethod', status = '1', remarks = '$remarks', lastChanged = '$lastChanged' WHERE orderID = '$preorderID'");

		$insertPayment = mysql_query("INSERT INTO tabpaymentorder(id,orderID,orderType,paymentType,paymentMethod,paymentAmount,paymentDate,dpp,VAT,discountPrice,total,promoID,isVoucher,voucherID,remarks,status,dateCreated,lastChanged) VALUES ('$paymentOrderID','$preorderID','PRE-ORDER','$paymentType','$paymentMethod','$paymentAmount','$paymentDate','$dpp','$VAT','$discountPrice','$total','$promoID','$isVoucher','$voucherID','$remarks',1,'$dateCreated','$lastChanged')");

		// $query = "UPDATE tabpreorderheader SET 
		// 		orderAmount='$orderAmount',
		// 		dpp='$dpp',
		// 		discountPrice='$discountPrice',
		// 		total='$total',
		// 		promoID = '$promoID',
		// 		paymentAmount='$paymentAmount',
		// 		paymentMethod='$paymentMethod',
		// 		status='1',
		// 		remarks='$remarks',
		// 		lastChanged='$lastChanged'
		// 		WHERE orderID = '$orderID' AND outletID = '$outletID'";
		}
		/* END */	

		/* Get Customer */
	   	$checkIDCust = mysql_query("SELECT * FROM mcustomer WHERE customerName = '$payerName'");
	   	$fetching = mysql_fetch_array($checkIDCust);
	   	/* Get Loyalty */
	   	$checkLoyalty = mysql_query("SELECT * FROM tabloyalty l
									INNER JOIN mcustomer c ON c.customerID = l.customerID
									WHERE c.customerName = '$payerName'");
	   	$fetchinLoyalty = mysql_fetch_array($checkLoyalty);
	   	$loyaltyP = $fetchinLoyalty['loyaltyPoint'];

	   	if(mysql_num_rows($checkIDCust)==0){
	   		if($payerName != null && $payerPhone != null){
		   		$q = mysql_query("INSERT INTO mcustomer(customerID, customerName, customerPhone, customerEmail, status, dateCreated, lastChanged) VALUES('$payerID', '$payerName', '$payerPhone', '$payerEmail', 1, '$dateCreated', '$lastChanged')");

		   		$actC = "NEW_CUSTOMER_".$payerName;

				$journalIDC = date("YmdHis");
				$queryJournalC = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalIDC','$actC','ADD_CUST_FROM_POS','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			}
	   	}

	   	/* Cek Loyalty jika tidak ada */
	   	if(mysql_num_rows($checkLoyalty)==0)
	   	{
	   		/* Jika pembelian lebih dari 50k, insert tabloyalty dengan point 10 */
	   		if($total > 0)
	   		{
	   			$point = 10;
	   			$ql = mysql_query("INSERT INTO tabloyalty(loyaltyID, customerID, outletID, loyaltyPoint, status, dateCreated, lastChanged)VALUES('$fetchinLoyal[loyaltyID]', '$payerID', '$outletID', '$point', 1, '$dateCreated', '$lastChanged')");
	   		}
	   		
	   	}
	   	/* Jika Loyalty ada */
	   	else
	   	
	   	{
	   		/* Jika pembelian lebih dari 50k, update loyalty point */
	   		if($total > 0):	   		
	   			$point = 10;
	   			$finalPoint = $loyaltyP + $point;
	   			$ql = mysql_query("UPDATE tabloyalty SET loyaltyPoint = '$finalPoint', lastChanged = '$lastChanged' WHERE customerID = '$fetching[customerID]'");
	   			echo "Ini id customernya :". $fetching['customerID'];
	   		endif;
	   	}	   	
		/* ---------- END ------------ */
		
		/* Update Status Voucher */
		if($voucherCode!=""){
			$queryVo = mysql_query("UPDATE mvoucher SET status = 2 WHERE voucherCode='$voucherCode'");
		}
		
		if($countArr!=0){
			/* Loop produk */
			for($x = 0;$x<$countArr;$x++) {

				$detailID= $id[$x];
				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];

				$subdatas = array();
				$subdatas[id] = $detailID;
				$subdatas[productID] = $product;
				$subdatas[amount] = $amount;
				$subdatas[price] = $price;
				$subdatas[subtotal] = $subtotal;
				$datas[] = $subdatas;

				// echo "id ".$product;
				// echo "jumlah ".$amount;
				// echo "harga ".$price;
				// echo "total ".$subtotal;
				
				#check apakah order detailnya ada, kalau gaada insert.
				$resChk = mysql_query("SELECT * FROM tabpreorderDetail WHERE preorderID = '$preorderID' AND id = '$detailID'");
				
				if(mysql_num_rows($resChk)==0){
					/* Insert product ke DB OrderDetail */
					$queryD = mysql_query("INSERT INTO tabpreorderdetail(id,preorderID,productID,productAmount,productPrice,productSubtotal,status, dateCreated,lastChanged)VALUES('$detailID','$preorderID', '$product', '$amount', '$price','$subtotal', 1, '$dateCreated', '$lastChanged')");
				}else{
					$queryD = mysql_query("UPDATE tabpreorderdetail SET productID='$product', productAmount='$amount', productPrice='$price',productSubtotal='$subtotal',status='1',lastChanged='$lastChanged' WHERE preorderID = '$preorderID' AND id='$detailID'");
				}
				
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
			
			/* Insert SystemJournal */
			$act = "ORDER_".$orderNo."_".$orderID."_COMPLETE";

			$queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_COMPLETE','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			
			//printOrder($data,$datas);
			$data_enc = json_encode($data);
			$datas_enc = json_encode($datas);

			$URL="/picaPOS/app/draftPO"; 
			$URL2="order_print.php?data=$data_enc&datas=$datas_enc"; 

			echo "<script>window.open('$URL2');</script>";
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
			
			echo "<script type='text/javascript'>alert('ORDER SUCCESS!')</script>";
			$URL="/picaPOS/app/draftPO"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";

   		}

	}
}
?>