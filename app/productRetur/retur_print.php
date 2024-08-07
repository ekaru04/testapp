<?php
namespace Dompdf;
session_start();  
include("../../assets/config/db.php");
require_once '../../assets/plugins/dompdf_0-7-0/dompdf/autoload.inc.php';

	$data = json_decode($_GET[data],true); 

	$returID = $data[returID];
    $returDate = $data[returDate];
    $categoryName = $data[categoryName];
    $productName = $data[productName];
    $stockRetur = $data[stockRetur];
    
	$date = date('Y-m-d h:i');
	
	$change = "Rp. ".number_format($changeAmount,0,",",".").",-";

	$file = str_replace("/","_","$returID");
	
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
	<p align='center'><span style='font-weight:bold;font-size:18px;'>RETUR STOK PICAROLL</span><br/>
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
			<td colspan='2' style='text-align:center;width:150px'>$returID</td>
		</tr>
		<tr>
			<td>
				&nbsp;Retur By: $_SESSION[username]
			</td>
			<td style='text-align:right'>
				$date&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;width:150px'>-----------------------------------------------------------</td>
		</tr>
	</table>
	
</div>
<div align='center'>
	<table style='font-size:12px;'>";

	// print_r($datas);
	// exit;

	$html.="
		<tr>
			<td colspan='2' style='vertical-align:top;width:180px'>
				<strong>&nbsp;Produk</strong>
			</td>
			<td style='text-align:right;'>
				<strong>Stok</strong>
			</td>
		</tr
		<tr>
			<td colspan='3' style='vertical-align:top;width:180px'>
				&nbsp;$productName
			</td>
			<td style='vertical-align:top;width:40px;'>
				&nbsp;$stockRetur x
			</td>
		</tr>";
	
	

		$html.="
			</table>
</div>
			<table  style='font-size:12px;'>
				<tr>
					<td colspan='2' style='text-align:center;width:150px'>&nbsp;-----------------------------------------------------------</td>
				</tr>
				<tr>
					<td>
						&nbsp;Approved By:
					</td>
				</tr>
				
				
				<tr>
					<td colspan='2' style='text-align:right;width:150px'>&nbsp;-------------------------</td>
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
				</tr>
			";
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