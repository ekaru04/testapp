<?php 
include("../../assets/config/db.php");
session_start();

$user = $_SESSION['userID'];
$outletID = $_SESSION['outletID'];

if(isset($_POST['preorderID']))
{


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
if($voucherCode)
{
	$isVoucher = 1;
}
	
$paymentAmount = $_POST['payment'];
$paymentType = $_POST['paymentType'];
$paymentDate = date('Y-m-d', strtotime($_POST['paymentDate']));
$paymentDateFP = date('Y-m-d', strtotime($_POST['paymentFP']));
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


// echo $preorderID;
// echo $outletID;

  /* Jika Status 4 atau Pelunasan */
  if($status == 4)
  { 

	/* Ambil data berdasarkan orderID */
	$checkPO = mysql_query("SELECT * FROM taborderheader WHERE orderID = '$preorderID' AND outletID = '$outletID'"); 
	$rowPO = mysql_fetch_array($checkPO); // <-- Buat ngecek Data $checkPO, gak terlalu dipake

	/* Cek jika datanya ada maka update data taborderheader TERUTAMA STATUS nya harus berubah jadi LUNAS */
	if($checkData = mysql_num_rows($checkPO) != 0)
	{

		/* Update Data taborderheader berdasarkan orderID sebelumnya, perhatikan STATUS nya harus 4(pelunasan) */
		$updateStatus = mysql_query("UPDATE taborderheader SET 
						orderAmount = '$preorderAmount', status = '4', remarks = '$remarks', lastChanged = '$lastChanged'
						WHERE orderID = '$preorderID' AND outletID = '$outletID'");

		/* Update data tabpaymentorder berdasarkan orderID, STATUS harus 4(pelunasan) */
		$updateStatusPayment = mysql_query("UPDATE tabpaymentorder SET paymentType = '$paymentType', paymentAmount = '$paymentAmount', paymentDateFP = '$paymentDateFP', dpp='$dpp', paymentMethod = '$paymentMethod', total='$total', status = '4', remarks = '$remarks', lastChanged = '$lastChanged' WHERE orderID = '$preorderID'");

		/* Mengambil data customer untuk dicek apakah customer yang PO datanya ada atau tidak berdasarkan customerNamenya */
		$checkCustomer = mysql_query("SELECT * FROM mcustomer WHERE customerName = '$payerName'");
		$rowCustomer = mysql_fetch_array($checkCustomer); // <-- Cek data
		$customerID = $rowCustomer['customerID']; // <-- variabel customerID
		// echo $rowCustomer['customerName']; // <-- Ngecek saja, gak terlalu dipake

		/* Mengambil data loyalty customer bedasarkan customerNamenya */
		$checkLoyalty = mysql_query("SELECT * FROM tabloyalty l	INNER JOIN mcustomer c ON c.customerID = l.customerID
									WHERE c.customerName = '$payerName'");
	   	$fetchinLoyalty = mysql_fetch_array($checkLoyalty); // <-- cek data loyalty customer
	   	$currentLoyalty = $fetchinLoyalty['loyaltyPoint']; // <-- Disimpan dalam variabel loyaltyPoint customer tadi
	   	$loyaltyIDCust = $fetchinLoyal['loyaltyID']; // <-- menyimpan loyaltyID milik customer

	   	// echo $loyaltyP; // <-- Buat ngecek beneran ada atau tidak loyaltynya, berapa loyalty yang dimiliki

	   	/* Mengambil point loyalty yang ada di tabel mloyalty */
	   	$masterLoyalty = mysql_query("SELECT * FROM mloyalty");
	   	$getPoint = mysql_fetch_array($masterLoyalty);
	   	$point = $getPoint['point'];


	   	/* Melakukan cek data customer, apabila data customer tidak ada sama sekali didatabase maka data customer tersebut akan ditambahkan ke dalam database */
	   	if(mysql_num_rows($checkCustomer)==0)
	   	{

	   		/* Melakukan validasi, apabila field nama dan telepon terisi maka data customer akan ditambahkan kedalam tabel mcustomer */
	   		if($payerName != null && $payerPhone != null)
	   		{

	   			/* Menambahkan data customer baru kedalam tabel mcustomer */
		   		$queryAddCust = mysql_query("INSERT INTO mcustomer(customerID, customerName, customerPhone, customerEmail, status, dateCreated, lastChanged) VALUES('$payerID', '$payerName', '$payerPhone', '$payerEmail', 1, '$dateCreated', '$lastChanged')");

		   		/* Menambahkan variabel act untuk diinput ke systemJournal */
		   		$actAddCust = "NEW_CUSTOMER_".$payerName;

		   		/* Menambahkan data ke systemJournal sebagai activity log */
				$journalID = date("YmdHis");
				$queryJournal = mysql_query("INSERT INTO systemJournal(journalID, activity, menu, userID, dateCreated, logCreated, status) VALUES('$journalIDC','$actAddCust','ADD_CUST_FROM_PO_FORM','$user','$dateCreated','$lastChanged', 'SUCCESS')");
			}
	   	}

	   	/* Melakukan cek loyalty apakah ada atau tidak, apabila tidak ada */
	   	if(mysql_num_rows($checkLoyalty)==0)
	   	{
	   		/* Apabila total pembelian lebih dari sekian, maka mendapatkan point loyalty kedalam database untuk customer tersebut */
	   		if($total > 0)
	   		{

	   			/* Menambahkan point loyalty kepada customer ke tabel tabloyalty untuk customer tersebut */
	   			$insertNewLoyalty = mysql_query("INSERT INTO tabloyalty(loyaltyID, customerID, outletID, loyaltyPoint, status, dateCreated, lastChanged)VALUES('$loyaltyIDCust', '$payerID', '$outletID', '$point', 1, '$dateCreated', '$lastChanged')");

	   		}
	   	}
	   	/* Apabila data loyalty customer ada */
	   	else
	   	{
	   		/* Apabila total pembelian lebih dari sekian, maka mendapatkan point loyalty kedalam database untuk customer tersebut */
	   		if($total > 0)
	   		{
	   			$increaseLoyalty = $currentLoyalty+$point;
	   			$updateCurrentLoyalty = mysql_query("UPDATE tabloyalty SET loyaltyPoint = '$increaseLoyalty', lastChanged = '$lastChanged' WHERE customerID = '$customerID'");
	   		}
	   	}

	   	/* Update status voucher apabila menggunakan voucher */
	   	if($voucherCode!="")
	   	{
			$updateVoucher = mysql_query("UPDATE mvoucher SET status = 2 WHERE voucherCode='$voucherCode'");
		}

		if($countArr!=0)
		{
			for($x=0; $x<$countArr;$x++)
			{
				$id = $x+1;
				$product = $productID[$x];
				$amount = $productAmount[$x];
				$price = $productPrice[$x];
				$subTotal = $productSubtotal[$x];

				$subdatas = array();
				// $subdatas[id] = $detailID;
				$subdatas[productID] = $product;
				$subdatas[amount] = $amount;
				$subdatas[price] = $price;
				$subdatas[subtotal] = $subTotal;
				$datas[] = $subdatas;

				$checkDetailOrder = mysql_query("SELECT * FROM taborderdetail WHERE orderID = '$preorderID'");
				$rowCheck = mysql_fetch_array($checkDetailOrder);

				/* Cek produk yang dibeli di taborderdetail */
				if(mysql_num_rows($checkDetailOrder)!=0)
				{
					/* Update Data produk terutama STATUS harus 4 (Pelunasan) */
					$queryD = mysql_query("UPDATE taborderdetail SET productID='$product', productAmount='$amount', productPrice='$price', productSubtotal='$subTotal', status='4', lastChanged='$lastChanged' WHERE orderID = '$preorderID' AND productID='$product'");
				}

				/* Cek data produk */
				$masterProduct = mysql_query("SELECT * FROM mproduct WHERE productID = '$product' AND outletID = '$outletID' AND status != 0");
				$dataProduct = mysql_fetch_array($masterProduct);

				$measurementProduct = $dataProduct['measurementID']; // <-- Megambil data measurement untuk di insert ke product history
				$stockNow = $dataProduct['curStock']; // <-- Mengambil stok saat ini dan disimpan dalam variabel

				$decreaseStock = $stockNow-$amount; // <-- Stok saat ini dikurang jumlah item yang dibeli pada produk tertentu

				/* Mengupdate stok saat ini pada tabel master data produk */
				$updateStock = mysql_query("UPDATE mproduct SET curStock = '$decreaseStock' WHERE productID = '$product' AND outletID = '$outletID'");

				/* Update ke product history untuk merecord pengeluaran suatu produk */
				$journalID = date('YmdHis'); // <-- Sebagai ID saja
				$productHistory = mysql_query("INSERT INTO tabproducthistory(id, transType, productID, amount, itemAmount, measurementID, userID, status, remarks, dateCreated, lastChanged)VALUES()");
			}

			/* Tambah activity ke systemJournal */
			$activity = "PREORDER_".$orderNo."_".$orderID."_COMPLETE";
			$queryJournal = mysql_query("INSERT INTO systemjournal(journalID, activity, menu, userID, dateCreated, logCreated, status) VALUES('$journalID', '$acttivity', 'PELUNASAN_PO_COMPLETE', '$user', '$dateCreated', '$lastChanged', 'SUCCESS')");
		}	   	
	}
	else
	{
		echo "<script type='text/javascript'>alert('DATA TIDAK TERSEDIA!!!')</script>";
		$URL="/picaPOS/app/draftPO"; 
		echo "<script type='text/javascript'>location.replace('$URL');</script>";
	}

		echo "<script type='text/javascript'>alert('DRAFT TERSIMPAN!')</script>";
		$URL="/picaPOS/app/draftPO"; 
		echo "<script type='text/javascript'>location.replace('$URL');</script>";
  }

  /* Jika status 3 atau draft */
  elseif($status == 3)
  {
  	/* Ambil data berdasarkan orderID */
  	$checkPO = mysql_query("SELECT * FROM taborderheader WHERE orderID = '$preorderID' AND outletID = '$outletID'");
  	$rowPO = mysql_fetch_array($checkPO);

  	if($checkData = mysql_num_rows($checkPO) !=0)
  	{
  		/* Seharusnya tidak ada data spesifik yang di update, melainkan hanya remarks saja jadi samakan saja seperti query jika status 4 */
  		$updateStatus = mysql_query("UPDATE taborderheader SET 
						orderAmount = '$preorderAmount', status = '3', remarks = '$remarks', lastChanged = '$lastChanged'
						WHERE orderID = '$preorderID' AND outletID = '$outletID'");

  		/* Seharusnya hanya mengupdate paymentDP apabila pada form sebelumnya lupa mengisi form paymentDP */
  		$updatePaymentData = mysql_query("UPDATE tabpaymentorder SET paymentType = '$paymentType', paymentAmount = '$paymentAmount', paymentDate = '$paymentDate', dpp='$dpp', paymentMethod = '$paymentMethod', total='$total', status = '3', remarks = '$remarks', lastChanged = '$lastChanged' WHERE orderID = '$preorderID'");
  	}

			echo "<script type='text/javascript'>alert('DRAFT PO TERSIMPAN!')</script>";
			$URL="/picaPOS/app/draftPO"; 
			echo "<script type='text/javascript'>location.replace('$URL');</script>";

  }

}





?>