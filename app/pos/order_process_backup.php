<?php
include("../../assets/config/db.php");
session_start();

$user = $_SESSION['username'];
$outletID = $_SESSION['outletID'];
$username = $_SESSION['username'];
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
$data[orderMethod] = $orderMethod;

$totalServCharge5 = 0;
$totalPb1 = 0;

$paymentOrderID = date('Ymd');

$countArr = count($productID);

	if($status == 0){
		/** DRAFT */
		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);

		if(mysql_num_rows($checkID)==0){

		/* Input ke OrderHeader status 0 */
		$query = mysql_query("INSERT INTO taborderheader(orderID,priceID,orderNo,orderDate,orderPeriode,orderAmount,orderMethod,outletID,payerName, payerPhone, payerEmail, remarks, status, username, dateCreated,lastChanged, payerMemberID) 
									VALUES('$orderID', '$priceID', '$orderNo', '$orderDate', '$orderPeriode', '$orderAmount', '$orderMethod', '$outletID', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 0, '$username', '$dateCreated', '$lastChanged', '$payerID')");
		
		}

		if($countArr!=0){

			/* Loop produk */
			for($x=0;$x<$countArr;$x++) {
				$productType = 0;

				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subtotal = $productSubtotal[$x];
				$totalServCharge5 += $servCharge5[$x];
				$totalPb1 += $pb1[$x];

				/** Kalau Bundle */
				if(substr($product,0,3)=="BDN"){
					$productType=2;
					echo "ini bundle";
				}else{
					$productType=1;
					echo "ini bukan bundle";
				}

				/* Insert product ke DB OrderDetail */
				$id = $x+1;
				$queryD = mysql_query("INSERT INTO taborderdetail(id,orderID,productType,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status,dateCreated,lastChanged)VALUES('$id','$orderID', '$productType', '$product', '$amount', '$price', '$servCharge5[$x]', '$pb1[$x]', '$subtotal', 0, '$dateCreated', '$lastChanged')");
			}
			$VAT = $totalServCharge5+$totalPb1;
			
			$queryPayment = mysql_query("INSERT INTO tabpaymentorder(id,orderID,orderMethod,paymentType,paymentMethod,paymentAmount,paymentDate,dpp,VAT,discountPrice,total,promoID,isVoucher,voucherID,remarks,status,dateCreated,lastChanged) VALUES ('$paymentOrderID','$orderID','$orderMethod','null','$paymentMethod','$paymentAmount','$orderDate','$dpp','$VAT','$discountPrice','$total','$promoID','$isVoucher','$voucherCode','$remarks',0,'$dateCreated','$lastChanged')");

			/* Insert SystemJournal */
			$journalID = date("YmdHis");
			$act = "ORDER_".$orderNo."_".$orderID."_DRAFT";

			$queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_DRAFT','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			
			echo "<script type='text/javascript'>alert('DRAFT TERSIMPAN!')</script>";
			$URL="/picaPOS/app/pos"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
		}
   
   } else {
   		/** ORDER */
		// echo $_POST['customerName'];
		// exit;
		$periode = date('Y-m');
		$queryPayment = mysql_query("SELECT * FROM mPaymentMethod WHERE methodID = '$paymentMethod'");
		$rowPayment = mysql_fetch_array($queryPayment);
		$namePayment = $rowPayment['methodName'];

		//Clearance check untuk bundle & produk biasa
		$unavailableMessages = array();
		if($countArr!=0){
			/* Loop produk */
			for($x = 0;$x<$countArr;$x++) {
				$productType = 0;
				$product = $productID[$x];
				$amount = $productAmount[$x];

				$checkProduct = mysql_query("SELECT * FROM mProduct WHERE productID = '$product'");
				$resultP = mysql_fetch_array($checkProduct);
				$fastOrder = $resultP['fastOrder'];

				// if($fastOrder == 1) {
				// 	echo "produk ini fastOrder";
				// 	exit;
				// }else{
				// 	echo "produk ini bukan fastOrder";
				// 	exit;
				// }

				if(substr($product,0,3)=="BDN"){
					$checkPro = mysql_query("SELECT * FROM tabBundleHeader WHERE bundleID = '$product' AND outletID = '$outletID'");
					$rowPro = mysql_fetch_array($checkPro);
				
					$getBundleID = $rowPro['bundleID'];
					$measurement = $rowPro['measurementID'];
					$curStockBundle = $rowPro['curStock'];

					/* Update Stok Bundle */
					$stock = $curStockBundle - $amount;
					//Kalau secara minimal bundle tidak mencukupi, error
					if($stock <= 0){
						$unavailableMessages[] = "Stok produk bundel: " . $rowPro['bundleName'] . " tidak cukup";
						continue;
					}

					/* Loop through each product in the bundle */
					$checkBundle = mysql_query("SELECT * FROM tabBundleDetail WHERE bundleID = '$getBundleID' AND outletID = '$outletID'");
					while ($rowBundle = mysql_fetch_array($checkBundle)) {
						$productFromBundle = $rowBundle['productID'];
						$stockProdFromBundle = $rowBundle['amount'];

						$totStockProdFromBundle = $stockProdFromBundle * $amount;
				
						$getProduct = mysql_query("SELECT * FROM mProduct WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
						$rowGetProduct = mysql_fetch_array($getProduct);
				
						$stockFromProduct = $rowGetProduct['curStock'];
						$updateFinalStock = $stockFromProduct - $totStockProdFromBundle;
						
						//Jika salah satu produk yang stoknya kurang, error
						if($updateFinalStock <= 0){
							$unavailableMessages[] = "Stok barang untuk: " . $rowPro['bundleName'] . " (bahan: " . $rowGetProduct['productName'] . ") tidak cukup";
						}
					}
				}
				else{
					$checkPro = mysql_query("SELECT * FROM mproduct WHERE productID = '$product' AND outletID = '$outletID'");
					$rowPro = mysql_fetch_array($checkPro);

					$curStock = $rowPro['curStock'];
					$stock = $curStock-$amount;

					if($stock <= 0){
						$unavailableMessages[] = "Stok produk: " . $rowPro['productName'] . " tidak mencukupi";
					}
				}
			}
		}

		if(count($unavailableMessages) > 0){
			echo "<script>var messages; </script>";
			echo "<script>messages = `".implode(";", $unavailableMessages)."` </script>";
			echo "<script type='text/javascript'>alert(messages.split(';').join('\\r\\n'))</script>";
			$URL="/picaPOS/app/pos"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";
			return;
		}

		/** Jika Metode Bayar JATAH */
		if($namePayment == 'JATAH'){
			$queryJatah = mysql_query("SELECT * FROM tabEmployeeQuota WHERE employeeName = '$payerName'");
			$rowJatah = mysql_fetch_array($queryJatah);
			$employID = $rowJatah['employeeID'];
			$employName = $rowJatah['employeeName'];
			$employQuota = $rowJatah['quotaNominal'];
			// echo $employName;
			// exit;
			if($employName == null){
				echo "<script type='text/javascript'>alert('Nama Karyawan tidak ada')</script>";
				$URL="/picaPOS/app/pos"; 
				echo "<script type='text/javascript'>location.replace('$URL');</script>";
			}

			/** Jika Jatahnya masih banyak */
			if($total <= $employQuota){
				// echo "Masuk";
				// exit;
				$updQuota = $employQuota - $total;
				// echo $updQuota;
				// exit;
				$queryUpdQuota = mysql_query("UPDATE tabEmployeeQuota SET quotaNominal = '$updQuota', lastChanged = '$lastChanged' WHERE employeeID = '$employID' AND employeeName = '$employName' AND periode = '$periode'");
				
				$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
				$rowCheck = mysql_fetch_array($checkID);
			
				 /* Input ke OrderHeader status 1 */
				 if(mysql_num_rows($checkID)==0){
			
					 $query = mysql_query("INSERT INTO taborderheader(orderID,priceID,orderNo,orderDate,orderPeriode,orderAmount,orderMethod,outletID,payerName,payerPhone,payerEmail,remarks,status,username,dateCreated,lastChanged,payerMemberID) 
					 							VALUES('$orderID', '$priceID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$orderMethod', '$outletID', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 1, '$username', '$dateCreated', '$lastChanged', '$payerID')");
			
				 }
				 
				 /* Update Status Voucher */
				 $queryVo = mysql_query("UPDATE mvoucher SET status = 2 WHERE voucherCode='$voucherCode'");
				 
				 if($countArr!=0){
					 /* Loop produk */
					 for($x = 0;$x<$countArr;$x++) {
						 $subdatas = array();
						 $product = $productID[$x];
						 $productType = 0;
			
						 /** Kalau Bundle */
						 if(substr($product,0,3)=="BDN"){
							 $productType=2;
							 echo "ini bundle";
						 }else{
							 $productType=1;
							 echo "ini bukan bundle";
						 }
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
						 $queryD = "INSERT INTO taborderdetail(id,orderID,productType,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID','$productType','$product', '$amount', '$price', '$servCharge5[$x]', '$pb1[$x]', '$subtotal', 1, '$dateCreated', '$lastChanged')";
						 $resD = mysql_query($queryD);
			
						 if($productType==1){
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
			
			
						 if ($productType == 2) {
							 /* Cek bundle curStock dan Update curStock */
							 $checkPro = mysql_query("SELECT * FROM tabBundleHeader WHERE bundleID = '$product' AND outletID = '$outletID'");
							 $rowPro = mysql_fetch_array($checkPro);
						 
							 $getBundleID = $rowPro['bundleID'];
							 $measurement = $rowPro['measurementID'];
							 $curStockBundle = $rowPro['curStock'];
						 
							 /* Update Stok Bundle */
							 $stock = $curStockBundle - $amount;
							 $queryUpdBundle = mysql_query("UPDATE tabBundleHeader SET curStock = '$stock' WHERE bundleID = '$product' AND outletID = '$outletID'");
						 
							 /* Loop through each product in the bundle */
							 $checkBundle = mysql_query("SELECT * FROM tabBundleDetail WHERE bundleID = '$getBundleID' AND outletID = '$outletID'");
							 while ($rowBundle = mysql_fetch_array($checkBundle)) {
								 $productFromBundle = $rowBundle['productID'];
								 $stockProdFromBundle = $rowBundle['amount'];
						 
								 $totStockProdFromBundle = $stockProdFromBundle * $amount;
						 
								 $getProduct = mysql_query("SELECT * FROM mProduct WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
								 $rowGetProduct = mysql_fetch_array($getProduct);
						 
								 $stockFromProduct = $rowGetProduct['curStock'];
								 $updateFinalStock = $stockFromProduct - $totStockProdFromBundle;
						 
								 /* Update stock for each product in the bundle */
								 $queryUpdProStock = mysql_query("UPDATE mProduct SET curStock = '$updateFinalStock' WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
								 
								 /* Insert product history for each product in the bundle */
								 $journalID = date("YmdHis");
								 $queryProHistory = mysql_query("INSERT INTO tabproducthistory(id, transType, productID, amount, amountLeft, measurementID, username, status, remarks, dateCreated, lastChanged) 
								 VALUES('$journalID', 'OUT', '$productFromBundle', '$totStockProdFromBundle', '$updateFinalStock', '$measurement', '$user', 1, '$orderID', '$dateCreated', '$lastChanged')");
							 }
						 }
					 }
					 $VAT = $totalServCharge5+$totalPb1;
					 $queryPayment = mysql_query("INSERT INTO tabpaymentorder(id,orderID,orderMethod,paymentType,paymentMethod,paymentAmount,paymentDate,dpp,VAT,discountPrice,total,promoID,isVoucher,voucherID,remarks,status,dateCreated,lastChanged) VALUES ('$paymentOrderID','$orderID','$orderMethod','null','$paymentMethod','$paymentAmount', '$orderDate', '$dpp','$VAT','$discountPrice','$total','$promoID','$isVoucher','$voucherCode','$remarks',1,'$dateCreated','$lastChanged')");
					 
							 
					}
				 /* Insert SystemJournal */
				 $act = "ORDER_".$orderNo."_".$orderID."_COMPLETE";
			
				 $queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_COMPLETE','$user','$dateCreated','$lastChanged', 'SUCCESS')");
				 
				 $data_enc = json_encode(array_merge($data, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1)));
				 $datas_enc = json_encode(array_merge($datas, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1)));
				 
				 $URL="/picaPOS/app/pos"; 
				 $URL2="order_print.php?data=$data_enc&datas=$datas_enc"; 
			
				 echo "<script type='text/javascript'>alert('Transaksi Jatah Karyawan Berhasil!')</script>";
				 echo "<script>window.open('$URL2');</script>";
				 echo "<script type='text/javascript'>location.replace('$URL');</script>";

			}else{
				// echo "kurang saldonya";
				// exit;
				echo "<script type='text/javascript'>alert('Jatah Karyawan habis/tidak cukup!')</script>";
				$URL="/picaPOS/app/pos"; 
				echo "<script type='text/javascript'>location.replace('$URL');</script>";
			}
			
		} else {
		if(strlen($payerName) > 0){
			/* ---------- ADD CUSTOMER ------------ */
			//Buat ID kalau belum ada, karena namanya bisa sama tapi idnya beda
			if($payerID == null){
				$getNewID = mysql_query("SELECT COUNT(customerID)+1 as count FROM mcustomer");
				$rowNewID = mysql_fetch_array($getNewID);
				$newID = $rowNewID['count'];
				$cusUniqID = "CUS".str_pad($newID, 4, "0", STR_PAD_LEFT);
				$q = mysql_query("INSERT INTO mcustomer(customerID, customerName, customerPhone, customerEmail, status, dateCreated, lastChanged) 
				VALUES('$cusUniqID', '$payerName', '$payerPhone', '$payerEmail', 1, '$dateCreated', '$lastChanged')");
				 
				/** If new Customer, insert new loyalty */
				$idl = date('Ymdhis');
				$insertLoyaltyNew = mysql_query("INSERT INTO tabLoyalty(loyaltyID, customerID, outletID, loyaltyPoint, status, dateCreated, lastChanged)
														VALUES('$idl', '$cusUniqID', '$outletID', 0, 1, '$dateCreated', '$lastChanged')");
				  
				$queryNewCust = mysql_query("SELECT * FROM mcustomer WHERE customerName = '$payerName'");
				$fetchNewCustomer = mysql_fetch_array($queryNewCust);
				$newCustID = $fetchNewCustomer['customerID'];
				$payerID = $fetchNewCustomer['customerID'];

				$actC = "NEW_CUSTOMER_".$payerName;
	
				$journalIDC = date("YmdHis");
				$queryJournalC = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalIDC','$actC','ADD_CUST_FROM_POS','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			}
			
			/* Get Customer */
			$checkIDCust = mysql_query("SELECT * FROM mcustomer WHERE customerID ='$payerID'");
			$fetching = mysql_fetch_array($checkIDCust);
			/* ---------- END ------------ */
			
			/* Cek Loyalty jika tidak ada */
			$checkLoyalty = mysql_query("SELECT * FROM tabLoyalty WHERE customerID = '$payerID'");
			$fetch = mysql_fetch_array($checkLoyalty);
			$loyaltyP = $fetch['loyaltyPoint'];
			if(mysql_num_rows($checkLoyalty)==0)
			{
				/* Jika pembelian lebih dari requirement, insert tabloyalty dengan point tertera */
				if($total > $require) {
	
					$ql = mysql_query("INSERT INTO tabloyalty(loyaltyID, customerID, outletID, loyaltyPoint, status, dateCreated, lastChanged)VALUES('$fetchinLoyal[loyaltyID]', '$newCustID', '$outletID', '$point', 1, '$dateCreated', '$lastChanged')");
			 
			 }
	
			/* Jika Loyalty ada */	
			}else{
				/* Jika pembelian lebih dari 50k, update loyalty point */

				// echo $newCustID;
				// exit;
				if($total > $require) {
	
					$finalPoint = $loyaltyP + $point;
					$ql = mysql_query("UPDATE tabloyalty SET loyaltyPoint = '$finalPoint', lastChanged = '$lastChanged' WHERE customerID = '$fetching[customerID]'");
					echo "Ini id customernya :". $fetching['customerID'];
			 
			 }
	
			}	   	
		 /* ---------- END ------------ */
		}
	
		$checkID = mysql_query("SELECT * FROM taborderheader WHERE orderID='$orderID' AND outletID='$outletID'");
		$rowCheck = mysql_fetch_array($checkID);
	
		 /* Input ke OrderHeader status 1 */
		 if(mysql_num_rows($checkID)==0){
	
			 $query = mysql_query("INSERT INTO taborderheader(orderID,priceID,orderNo,orderDate,orderPeriode,orderAmount,orderMethod,outletID,payerName, payerPhone, payerEmail, remarks, status, username, dateCreated,lastChanged,payerMemberID) VALUES('$orderID', '$priceID', '$orderNo', '$orderDate', '$orderPeriode','$orderAmount', '$orderMethod', '$outletID', '$payerName', '$payerPhone', '$payerEmail', '$remarks', 1, '$username', '$dateCreated', '$lastChanged', '$payerID')");
	
		 }
		 
		 /* Update Status Voucher */
		 $queryVo = mysql_query("UPDATE mvoucher SET status = 2 WHERE voucherCode='$voucherCode'");
		 
		 if($countArr!=0){
			 /* Loop produk */
			 for($x = 0;$x<$countArr;$x++) {
				 $subdatas = array();
				 $product = $productID[$x];
				 $productType = 0;
	
				 /** Kalau Bundle */
				 if(substr($product,0,3)=="BDN"){
					 $productType=2;
					 echo "ini bundle";
				 }else{
					 $productType=1;
					 echo "ini bukan bundle";
				 }
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
				 $queryD = "INSERT INTO taborderdetail(id,orderID,productType,productID,productAmount,productPrice,servCharge5,pb1,productSubtotal,status, dateCreated,lastChanged)VALUES('$id','$orderID','$productType','$product', '$amount', '$price', '$servCharge5[$x]', '$pb1[$x]', '$subtotal', 1, '$dateCreated', '$lastChanged')";
				 $resD = mysql_query($queryD);
	
				 if($productType==1){
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
	
	
				 if ($productType == 2) {
					 /* Cek bundle curStock dan Update curStock */
					 $checkPro = mysql_query("SELECT * FROM tabBundleHeader WHERE bundleID = '$product' AND outletID = '$outletID'");
					 $rowPro = mysql_fetch_array($checkPro);
				 
					 $getBundleID = $rowPro['bundleID'];
					 $measurement = $rowPro['measurementID'];
					 $curStockBundle = $rowPro['curStock'];
				 
					 /* Update Stok Bundle */
					 $stock = $curStockBundle - $amount;
					 $queryUpdBundle = mysql_query("UPDATE tabBundleHeader SET curStock = '$stock' WHERE bundleID = '$product' AND outletID = '$outletID'");
				 
					 /* Loop through each product in the bundle */
					 $checkBundle = mysql_query("SELECT * FROM tabBundleDetail WHERE bundleID = '$getBundleID' AND outletID = '$outletID'");
					 while ($rowBundle = mysql_fetch_array($checkBundle)) {
						 $productFromBundle = $rowBundle['productID'];
						 $stockProdFromBundle = $rowBundle['amount'];
				 
						 $totStockProdFromBundle = $stockProdFromBundle * $amount;
				 
						 $getProduct = mysql_query("SELECT * FROM mProduct WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
						 $rowGetProduct = mysql_fetch_array($getProduct);
				 
						 $stockFromProduct = $rowGetProduct['curStock'];
						 $updateFinalStock = $stockFromProduct - $totStockProdFromBundle;
				 
						 /* Update stock for each product in the bundle */
						 $queryUpdProStock = mysql_query("UPDATE mProduct SET curStock = '$updateFinalStock' WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
						 
						 /* Insert product history for each product in the bundle */
						 $journalID = date("YmdHis");
						 $queryProHistory = mysql_query("INSERT INTO tabproducthistory(id, transType, productID, amount, amountLeft, measurementID, username, status, remarks, dateCreated, lastChanged) 
						 VALUES('$journalID', 'OUT', '$productFromBundle', '$totStockProdFromBundle', '$updateFinalStock', '$measurement', '$user', 1, '$orderID', '$dateCreated', '$lastChanged')");
					 }
				 }
				 
	
				 // if($productType==2){
				 // 	/* Cek bundle curStock dan Update curStock */
				 // 	$checkPro = mysql_query("SELECT * FROM tabBundleHeader WHERE bundleID = '$product' AND outletID = '$outletID'");
				 // 	$rowPro = mysql_fetch_array($checkPro);
	
				 // 	$getBundleID = $rowPro['bundleID'];
				 // 	$measurement = $rowPro['measurementID'];
				 // 	$curStockBundle = $rowPro['curStock'];
	
				 // 	$checkBundle = mysql_query("SELECT * FROM tabBundleDetail WHERE bundleID = '$getBundleID' AND outletID = '$outletID'");
				 // 	$rowBundle = mysql_fetch_array($checkBundle);
	
				 // 	$productFromBundle = $rowBundle['productID'];
				 // 	$stockProdFromBundle = $rowBundle['amount'];
	
				 // 	$totStockProdFromBundle = $stockProdFromBundle * $amount;
	
				 // 	$getProduct = mysql_query("SELECT * FROM mProduct WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
				 // 	$rowGetProduct = mysql_fetch_array($getProduct);
	
				 // 	$stockFromProduct = $rowGetProduct['curStock'];
				 // 	$updateFinalStock = $stockFromProduct - $totStockProdFromBundle;
	
				 // 	/* Update Stok Bundle */
				 // 	$stock = $curStock-$amount;
				 // 	$queryUpdBundle = mysql_query("UPDATE tabBundleHeader SET curStock = '$stock' WHERE bundleID = '$product' AND outletID = '$outletID'");
				 // 	$queryUpdProStock = mysql_query("UPDATE mProduct SET curStock = '$updateFinalStock' WHERE productID = '$productFromBundle' AND outletID = '$outletID'");
	
				 // 	/* Insert bundleHistory */
				 // 	$journalID = date("YmdHis");
				 // 	$queryProHistory = mysql_query("INSERT INTO tabproducthistory(id,transType,productID,amount,amountLeft,measurementID,userID,status,remarks,dateCreated,lastChanged)VALUES('$journalID','OUT','$product','$amount','$stock','$measurement','$user',1,'$orderID','$dateCreated','$lastChanged')");
				 // }
				 
	
			 }
			 $VAT = $totalServCharge5+$totalPb1;
			 $queryPayment = mysql_query("INSERT INTO tabpaymentorder(id,orderID,orderMethod,paymentType,paymentMethod,paymentAmount,paymentDate,dpp,VAT,discountPrice,total,promoID,isVoucher,voucherID,remarks,status,dateCreated,lastChanged) VALUES ('$paymentOrderID','$orderID','$orderMethod','null','$paymentMethod','$paymentAmount', '$orderDate', '$dpp','$VAT','$discountPrice','$total','$promoID','$isVoucher','$voucherCode','$remarks',1,'$dateCreated','$lastChanged')");
			 
					 
			}
		 /* Insert SystemJournal */
		 $act = "ORDER_".$orderNo."_".$orderID."_COMPLETE";
	
		 $queryJournal = mysql_query("INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_COMPLETE','$user','$dateCreated','$lastChanged', 'SUCCESS')");
		 
		 $data_enc = json_encode(array_merge($data, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1, "orderMethod" => $orderMethod)));
		 $datas_enc = json_encode(array_merge($datas, array("totalServChar5" => $totalServCharge5, "totalPb1" => $totalPb1, "orderMethod" => $orderMethod)));
		 
		 $URL="/picaPOS/app/pos"; 
		 $URL2="order_print.php?data=$data_enc&datas=$datas_enc"; 
	
		 echo "<script>window.open('$URL2');</script>";
		 echo "<script type='text/javascript'>location.replace('$URL');</script>";
		}


	}
}
?>