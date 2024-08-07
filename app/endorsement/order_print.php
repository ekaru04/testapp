<?php
namespace Dompdf;
session_start();  
include("../../assets/config/db.php");
require_once '../../assets/plugins/dompdf_0-7-0/dompdf/autoload.inc.php';

	$data = json_decode($_GET[data],true); 
	$datas = json_decode($_GET[datas],true); 

	$orderID = $data[orderID];
	$orderNo = $data[orderNo];
	$orderDate = $data[orderDate];
	$orderAmount = $data[orderAmount];
	$dpp = $data[dpp];
	$VAT = $data[vat];
	$discountPrice = $data[discountPrice];
	$total = $data[total];
	$promoID = $data[promoID];
	$voucherCode = $data[voucherCode];
	$isVoucher = $data[isVoucher];
	$paymentAmount = $data[paymentAmount];
	$payMethod = $data[paymentMethod];
	$changeAmount = $data[changeAmount];
	$payerName = $data[payerName];
	$payerPhone = $data[payerPhone];
	$payerEmail = $data[payerEmail];
	$remarks = $data[remarks];
	
	$date = date("d/m/Y H:i");
	$subtotal = "Rp. ".number_format($dpp,0,",",".").",-";
	$discount = "Rp. ".number_format($discountPrice,0,",",".").",-";
	$tot = "Rp. ".number_format($total,0,",",".").",-";
	$payment = "Rp. ".number_format($paymentAmount,0,",",".").",-";

	$resC = mysql_query("SELECT * FROM mPaymentMethod WHERE methodID = '$payMethod' AND status = 1");
	$rowC = mysql_fetch_array($resC);
	$paymentMethod = $rowC[methodName];
	
	$change = "Rp. ".number_format($changeAmount,0,",",".").",-";

	$file = str_replace("/","_","$orderID");
	
	$options = new Options();
	$options->set('isJavascriptEnabled', true);
	$dompdf = new Dompdf($options); 
	$html="
	<style>
	@page {
		margin: 0px 0px 10px 0px !important;
		padding: 3px 3px 3px 3px !important;
	}
</style>
<div>
	<p align='center'><span style='font-weight:bold;font-size:23px;'>pic.a.roll</span><br/>
		<span style='font-weight:normal;font-size:11px;'>Premium Ingredients, <br/>Freshly Made With Love<br/>
		Jl Ngagel Jaya Utara No. 21 Surabaya</span>
	</p>
</div>
<div align='center'>
	<table style='font-size:12px;'>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>-----------------------------------------------------------</td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>$orderID</td>
		</tr>
		<tr>
			<td>
				&nbsp;Order No: $orderNo
			</td>
			<td style='text-align:right'>
				$date&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;Cashier: $_SESSION[fullname]
			</td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>-----------------------------------------------------------</td>
		</tr>
	</table>
	
</div>
	<table style='font-size:12px'>";
	foreach($datas as $row){
		$productID = $row[productID];
		$amount = $row[amount];
		$unitPrice = $row[price];
		$unitSubtotal = $row[subtotal];
		
		$resProd = mysql_query("SELECT * FROM mProduct WHERE productID = '$productID' AND status = 1");
		$rowProd = mysql_fetch_array($resProd);
		
		$prodName = $rowProd[productName];
		$unitSubtot = "Rp. ".number_format($unitSubtotal,0,",",".").",-";
		$unit = number_format($unitPrice,0,",",".");
		$amo = number_format($amount,0,",",".");
$html.="<tr>
			<td colspan='3' style='vertical-align:top;width:180px'>
				&nbsp;$prodName
			</td>
		</tr>
		<tr>
			<td style='vertical-align:top;width:40px;'>
				&nbsp;$amo x
			</td>
			<td style='vertical-align:top;width:80px;'>
				&nbsp;&nbsp;&nbsp;$unit
			</td>
			<td style='vertical-align:top;text-align:right;width:80px;'>
				$unitSubtot
			</td>
		</tr>";
	}
	
$html.="
	</table>
	<table  style='font-size:12px;'>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>&nbsp;-----------------------------------------------------------</td>
		</tr>
		<tr>
			<td>
				<strong>&nbsp;SUBTOTAL</strong>
			</td>
			<td style='text-align:right;width:50px'>
				$subtotal&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<strong>&nbsp;DISCOUNT</strong>
			</td>
			<td style='text-align:right'>
				($discount)&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<strong>&nbsp;TOTAL</strong>
			</td>
			<td style='text-align:right'>
				$tot&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<strong>&nbsp;PAYMENT</strong>
			</td>
			<td style='text-align:right'>
				$payment&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<strong>&nbsp;PAYMENT BY</strong>
			</td>
			<td style='text-align:right'>
				$paymentMethod&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<strong>&nbsp;CHANGE</strong>
			</td>
			<td style='text-align:right;'>
				$change&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='2' style='width:150px'>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='2' style='width:150px' >
				<strong>&nbsp;REMARKS:</strong>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='width:150px'>
				&nbsp;$remarks
			</td>
		</tr>
		<tr>
			<td colspan='2' style='width:150px'>
				&nbsp;
			</td>
		</tr>";
	 if($promoID){
		$resP = mysql_query("SELECT * FROM mPromo WHERE promoID = '$promoID' AND status = 1");
		$rowP = mysql_fetch_array($resP);
		$promo = $rowP[promoName];
$html.="<tr>
			<td colspan='2' style='text-align:center;width:150px'>
				<strong>PROMO:</strong>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>
				<strong>$promo</strong>
			</td>
		</tr>";	 
	 }
$html.="<tr>
			<td colspan='2' style='text-align:center;width:150px'>-----------------------------------------------------------</td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>
					Thank you for your order $payerName~!
			</td>
		</tr>
	</table>
	";
	//to put other html file
	$dompdf->loadHtml($html);

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper(array(0, 0, 600, 180), 'landscape');

	// Render the HTML as PDF
	$dompdf->render();

	// Output the generated PDF (1 = download and 0 = preview)
	$dompdf->stream("$file",array("Attachment"=>0));
?>