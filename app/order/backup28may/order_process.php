<?php
	include("../../assets/config/db.php");
	session_start();

$user = $_SESSION['userID'];
$outletID = $_SESSION['outletID'];

if(isset($_POST['orderID'])){

$orderID = $_POST['orderID'];
$orderNo = $_POST['orderNo'];
$orderDate = $_POST['orderDate'];
$orderPeriode = date("Y-m");
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
$payerID = $_POST['customerID'];
$payerName = strtoupper($_POST['customerName']);
$payerPhone = $_POST['customerPhone'];
$payerEmail = $_POST['customerEmail'];
$status = $_POST['status'];
$remarks = $_POST['remarks'];
$dateCreated = date("Y-m-d");
$lastChanged = date("Y-m-d H:i:s");

$productID = $_POST['productID'];
$id = $_POST['id'];
$productAmount = $_POST['productQty'];
$productPrice = $_POST['productPrice'];
$productSubtotal = $_POST['productSubtotal'];
$servCharge = $_POST['serviceCharge'];
$servCharge5 = $_POST['servCharge5'];
$pb1 = $_POST['pb10'];
$oriPb1 = $_POST['ori_pb1'];
$changeAmount = $_POST['changeAmount'];


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

$totalServCharge5 = 0;
$totalPb1 = 0;

$queryIDLoyal = mysql_query("SELECT COUNT(loyaltyID)+1 as loyaltyID FROM tabloyalty");
$fetchinLoyal = mysql_fetch_array($queryIDLoyal);
$point = $fetchinLoyal['point'];

$countArr = count($productID);

/**
 * Status = 0 => Draft
 * Status = 1 => ORDER
 */

	if($status == 0){
		//DRAFT
		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);
		if(mysql_num_rows($checkID)!=0){

		/* Update OrderHeader status 2 */

		$query = mysql_query("UPDATE taborderheader SET orderAmount='$orderAmount', status='0', remarks='$remarks',	lastChanged='$lastChanged'
								WHERE orderID = '$orderID' AND outletID = '$outletID'");
		
		}
		/* END */	

		// print_r($countArr);
		// exit;

		if($countArr!=0){

			/* Loop produk */
			for($x = 0;$x<$countArr;$x++) {

				// $detailID= $id[$x];
				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];
				$_servCharge5 = $servCharge5[$x];
				$totalServCharge5 += $servCharge5[$x];
				$totalPb1 += $oriPb1[$x];

				/* update product ke DB OrderDetail */
				$id = $x+1;

				#check apakah order detailnya ada, kalau gaada insert.
				$resChk = mysql_query("SELECT COUNT(id) AS id FROM taborderdetail WHERE orderID = '$orderID' AND productID = '$product'");
				$countID = mysql_fetch_array($resChk);
				$rowIDetail = $countID['id'];
				
				// if($x == 1) {
				// 	print_r($rowIDetail);
				// 	exit;
				// }


				if($rowIDetail == 0){
					$queryD = mysql_query("INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status, dateCreated,lastChanged)
					VALUES('$rowIDetail','$orderID', '$product', '$amount', '$price', '$_servCharge5', '$totalPb1', '$subtotal', 2, '$dateCreated', '$lastChanged')");
					// print_r("INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,productSubtotal,status, dateCreated,lastChanged)
					// VALUES('$rowIDetail','$orderID', '$product', '$amount', '$price','$subtotal', 0, '$dateCreated', '$lastChanged')");
					// exit;	
			}else{
					$queryD = mysql_query("UPDATE taborderdetail SET productID='$product',productAmount='$amount',
											productPrice='$price',servCharge5='$_servCharge5',pb1='$totalPb1',productSubtotal='$subtotal',status='2',lastChanged='$lastChanged' WHERE orderID = '$orderID' AND id = '$id'");
				}
				/* END */
			}
			/* Ambil status 0 dan hapus */
			$delOrder = mysql_query("DELETE FROM taborderdetail WHERE orderID='$orderID' AND status='0'");
			/* END */

			/* Ubah status 2 ke 0 */
			$queryDetail = mysql_query("UPDATE taborderdetail SET 
									status='0',lastChanged='$lastChanged' WHERE orderID = '$orderID' AND status ='2'");
			/* END */	
			$VAT = $totalServCharge5+$totalPb1;
			$queryPayment = mysql_query("UPDATE tabpaymentorder SET paymentMethod = '$paymentMethod', paymentAmount = '$paymentAmount', dpp = '$dpp', 
									VAT = '$VAT', discountPrice = '$discountPrice', total = '$total', promoID = '$promoID', isVoucher = '$isVoucher',voucherID = '$voucherCode', status = '0', lastChanged = '$lastChanged' WHERE orderID = '$orderID'");	


			/* Insert SystemJournal */
			$journalID = date("YmdHis");
			$act = "ORDER_".$orderNo."_".$orderID."_DRAFT";

			$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_DRAFT','$user','$dateCreated','$lastChanged', 'SUCCESS')";
			$resJournal = mysql_query($queryJournal);
			
			echo "<script type='text/javascript'>alert('DRAFT TERSIMPAN!')</script>";
			$URL="/picaPOS/app/order"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
		}
   
   }else{
   		//ORDER

   		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);

		/* Input ke OrderHeader status 1 */
		if(mysql_num_rows($checkID)!=0){

		/* Update OrderHeader status 2 */

		$query = mysql_query("UPDATE taborderheader SET orderAmount='$orderAmount', status='1', remarks='$remarks',
						lastChanged='$lastChanged'
						WHERE orderID = '$orderID' AND outletID = '$outletID'");

		

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
		   		$q = "INSERT INTO mcustomer(customerID, customerName, customerPhone, customerEmail, status, dateCreated, lastChanged) VALUES('$payerID', '$payerName', '$payerPhone', '$payerEmail', 1, '$dateCreated', '$lastChanged')";
		   		$r = mysql_query($q);

		   		$actC = "NEW_CUSTOMER_".$payerName;

				$journalIDC = date("YmdHis");
				$queryJournalC = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalIDC','$actC','ADD_CUST_FROM_POS','$user','$dateCreated','$lastChanged', 'SUCCESS')";
				$resJournalC = mysql_query($queryJournalC);
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
				
				$totalServCharge5 += $servCharge5[$x];
				$totalPb1 += $oriPb1[$x];

				$subdatas = array();
				$subdatas[id] = $detailID;
				$subdatas[productID] = $product;
				$subdatas[amount] = $amount;
				$subdatas[price] = $price;
				$subdatas[subtotal] = $subtotal;
				$datas[] = $subdatas;
				
				#check apakah order detailnya ada, kalau gaada insert.
				$resChk = mysql_query("SELECT * FROM tabOrderDetail WHERE orderID = '$orderID' AND id = '$detailID'");
				
				if(mysql_num_rows($resChk)==0){
					/* Insert product ke DB OrderDetail */
					$queryD = "INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,productSubtotal,status, dateCreated,lastChanged)VALUES('$detailID','$orderID', '$product', '$amount', '$price','$subtotal', 1, '$dateCreated', '$lastChanged')";
					// $queryD = "INSERT INTO taborderdetail(id,orderID,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID', '$product', '$amount', '$price', '$totalServCharge5', '$totalPb1', '$subtotal', 1, '$dateCreated', '$lastChanged')";
				}else{
					$queryD = "UPDATE taborderdetail SET productID='$product',productAmount='$amount',
							productPrice='$price',productSubtotal='$subtotal',status='1',lastChanged='$lastChanged' WHERE orderID = '$orderID' AND id='$detailID'";
				}
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

			$VAT = $totalServCharge5+$totalPb1;
			$queryPayment = mysql_query("UPDATE tabpaymentorder SET paymentMethod = '$paymentMethod', paymentAmount = '$paymentAmount', dpp = '$dpp', VAT = '$VAT', discountPrice = '$discountPrice', total = '$total', promoID = '$promoID', isVoucher = '$isVoucher', voucherID = '$voucherCode', status = '1', lastChanged = '$lastChanged'
			WHERE orderID = '$orderID'");
			
			/* Insert SystemJournal */
			$act = "ORDER_".$orderNo."_".$orderID."_COMPLETE";

			$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_COMPLETE','$user','$dateCreated','$lastChanged', 'SUCCESS')";
			$resJournal = mysql_query($queryJournal);	
			
			//printOrder($data,$datas);
			$data_enc = json_encode(array_merge($data, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1)));
			$datas_enc = json_encode(array_merge($datas, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1)));

			$URL="/picaPOS/app/order"; 
			$URL2="order_print.php?data=$data_enc&datas=$datas_enc"; 

			echo "<script>window.open('$URL2');</script>";
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
			
			echo "<script type='text/javascript'>alert('ORDER SUCCESS!')</script>";
			$URL="/picaPOS/app/order"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";

   		}

	}
}
?>