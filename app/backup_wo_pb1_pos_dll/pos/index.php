<?php
session_start();
if (!isset($_SESSION["username"])) 
{
	$URL="/picapos/app"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
}
include("../../assets/config/db.php");		
include('../../assets/template/navbar_app.php');
date_default_timezone_set('Asia/Jakarta');



?>
<style>
	.leftist{
		    min-height: 20px;
			padding: 10px;
			margin-bottom: 20px;
			background-color: #f5f5f5;
			border: 1px solid #e3e3e3;
			border-radius: 4px;
			-webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%);
			box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%);
	}
	.rightist{
			padding: 10px;
			margin-bottom: 20px;
			margin-left: -20px;
			background-color: #f5f5f5;
			border: 1px solid #e3e3e3;
			border-radius: 4px;
			-webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%);
			box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%);
	}
	.table-header{
			background-color: rgba(168, 255, 254, 0.4);
	}
	.table-details{
			background-color: rgba(168, 216, 255, 0.4);
	}
	
	.card {
	  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
	  max-width: 300px;
	  max-height: 300px;
	  margin: auto;
	  text-align: center;
	}

	.card button {
	  border: none;
	  outline: 0;
	  padding: 5px;
	  background-color: rgba(173, 202, 195, 0.4);
	  text-align: center;
	  cursor: pointer;
	  width: 100%;
	  font-size: 12px;
	}

	.card button:hover {
	  opacity: 0.7;
	}
</style>
<?php
	$outletID = $_SESSION[outletID]; //ambil outlet dari session sesuai yang login

	$today = date('Y-m-d');
	$res = mysql_query("SELECT count(orderID) orderCount FROM tabOrderHeader WHERE orderDate = '$today' AND orderID LIKE '%RCP%'");
	$row = mysql_fetch_array($res);

	$orderNo = $row[orderCount]+1;

	if($_GET['customerID']==""){
    $queryItemID = "SELECT count(customerID)+1 as customerID FROM mcustomer";
    $resItemID = mysql_query($queryItemID);
    $row = mysql_fetch_array($resItemID);
    $customerID = $row['customerID'];
}else{
    $customerID = $_GET['customerID'];
    $query = "SELECT * FROM mcustomer WHERE customerID ='$customerID'";
    $res = mysql_query($query);
    $row = mysql_fetch_array($res);
    // $supplierID = $row['supplierID'];
    // $suppUCode = "SPL-".$date.-$supplierID;
}


$today = date('Y-m-d');
$getOpenCashier = mysql_query("SELECT * FROM tabopencashier WHERE openDate = '$today'");
$fetchOpenCashier = mysql_fetch_array($getOpenCashier);
$dateOpen = $fetchOpenCashier['openDate'];
$nominal = $fetchOpenCashier['nominalOpen'];

if($dateOpen == 0) {
	echo "<script type='text/javascript'>alert('Open Cashier belum terisi!')</script>";
	$urls="/picapos/app/openCashier";
	echo "<script type='text/javascript'>location.replace('$urls');</script>";
}

?>
<div>
		<div class='clear height-20 mt-3'></div>
		<div class="container-fluid">
			<form id='formOrder' method='POST' action='order_process.php'>
				<div class='row'>
					<div class='col-lg-4'>
						<div class='leftist'>							
							<h3><b>ORDER CASHIER</b></h3>
							<div class='row mb-2'>
								<div class='col-3'>
									<input type='text' readonly class="form-control-plaintext" value='Order No:'/>
									<!-- <input type='text' readonly class="form-control-plaintext" style='width:100%;' value='Order No:'/> -->
								</div>
								<div class='col-2'>
									<input type='text' readonly class="form-control-plaintext" id ='orderNo' name ='orderNo' style='font-weight:bold;' value='<?php echo $orderNo;?>'/>
									<!-- <input type='text' readonly class="form-control-plaintext" id ='orderNo' name ='orderNo' style='margin-left:-30px;width:100%;font-weight:bold;' value='<?php echo $orderNo;?>'/> -->
								</div>
								<div class='col-2'>
									<input type='text' readonly class="form-control-plaintext" value='Tgl:'/>
									<!-- <input type='text' readonly class="form-control-plaintext" style='margin-left:50px;width:50%;' value='Date:'/> -->
								</div>
								<div class='col-5'>
									<input type='date' readonly class="form-control-plaintext" id ='orderDate' name ='orderDate' style='float:right;text-align:right;font-weight:bold;' value='<?php echo date('Y-m-d');?>'/>
									<!-- <input type='date' readonly class="form-control-plaintext" id ='orderDate' name ='orderDate' style='float:right;text-align:right;width:180%;font-weight:bold;margin-left:-60px;margin-right:-20px;' value='<?php echo date('Y-m-d');?>'/> -->
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<select class="mb-2 form-control" id="methodList" name='priceID' required>
										<option value="">-List Tipe Order-</option>
										<?php
										$queryPay = mysql_query("SELECT * FROM tabpriceheader ORDER BY priceID ASC");
											while($rowMeth = mysql_fetch_array($queryPay)){
												echo "<option value='$rowMeth[priceID]'>$rowMeth[priceName]</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<input type='hidden' id='customerID' name='customerID' value="<?php echo $customerID; ?>" />

									<select class="mb-2 form-control" id="listCust" onchange="list()">
										<option value="">-List Pelanggan-</option>
										<?php
										$queryCust = mysql_query("SELECT * FROM mcustomer ORDER BY customerID ASC");
											while($rowCust = mysql_fetch_array($queryCust)){
												echo "<option value='$rowCust[customerID]'>$rowCust[customerName]</option>";
											}
										?>
									</select>
									
									<input type='text' class="form-control" id ='customerName' name ='customerName' style='width:100%;' placeholder='Insert Nama Pelanggan'/>
									<input type='hidden' id='itemCount' name='itemCount' value='0'/>
									<input type='hidden' id='status' name='status'/>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<input type='text' class="form-control" id ='customerPhone' name ='customerPhone'  style='width:100%;' placeholder='Insert No. Telp Pelanggan'/>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<input type='text' class="form-control" id ='customerEmail' name ='customerEmail'  style='width:100%;' placeholder='Insert Email Pelanggan'/>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<table class='table-responsive' id='tableProduct' style='font-size:13px;' >
										<thead>
											<tr>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'>Produk</th>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'></th>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'>Harga</th>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'>Qty</th>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'></th>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'>Subtotal</th>
												<th class = 'table-header' style='vertical-align:middle;text-align:center;'></th>
											</tr>
										</thead>
										<tbody>
											<tr class='deft'>
												<td class = 'table-details' style='text-align:left;width:35%;padding-left:5px;'>Nama Produk</td>
												<td class = 'table-details' style='text-align:left;width:1%;'>&nbsp;&nbsp;Rp. </td>
												<td class = 'table-details' style='text-align:right;width:29%;padding-right:5px;'>
													<input type="text" name="productPrice[]" class="inps" style="width:100px;" value="0">
												</td>
												<td class = 'table-details' style='text-align:center;width:5%;'>
													<input type="text" name="productQty[]" class="inps" style="width:40px;" value="0">
													<input type="hidden" name="productSubtotal[]" style="width:40px;" value="0">
												</td>
												<td class = 'table-details' style='text-align:left;width:1%;padding-left:5px;'>Rp. </td>
												<td class = 'table-details' style='text-align:right;width:24%;padding-right:5px;'>
													<input type="text" name="productSutot[]" readonly="" style="width:100px;" value="0">
												</td>
												<td class = 'table-details' style='text-align:left;width:5%;padding-left:5px;'>
													<button type='button' class='del' style='border:none;margin-left:-10px;background-color:rgba(255, 255, 255, 0);'>
														<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' class='bi bi-trash' viewBox='0 0 16 16'>
														<path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>
														<path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>
														</svg>
												   </button>
												</td>
											</tr>
										</tbody>
										
										
										
									</table>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<div class='row'>
										<div class='col-3'>
											<b>Total Produk</b>
										</div>
										<div class='col-3'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='totalProduct'  style='float:right;text-align:right;width:80px;font-weight:bold;vertical-align:top;' value='0'/>
											<input type='hidden' name='totalProduct' id='totalProd' value='0'/>
										</div>
										<div class='col-2'>
											<b>Total</b>
										</div>
										<div class='col-4'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='grossPrice' style='float:right;text-align:right;width:120px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='hidden' id='dpp' name='dpp' value='0'/>
										</div>
									</div>
									<div class='row'>
										<div class='col-4'>
											<b>&nbsp;</b>
										</div>
									</div>
									<div class='row'>
										<!-- <div class='col-2'>
											<b>Discount</b>
										</div> -->
										<div class='col-4'>
											<input type='hidden' readonly class="form-control-sm form-control-plaintext" id ='discount' style='float:right;text-align:right;width:100px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='hidden' id='disc' name='discount' value='0'/>
											<input type='hidden' id='discPer' name='discountPerc' value='0'/>
										</div>
										
									</div>
									<div class='row'>
										<div class='col-4'>
											<b>&nbsp;</b>
										</div>
									</div>
									<div class='row'>
										<div class='col-7'>
											<b>Diterima</b>
										</div>
										<div class='col-5'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='paymentAmount' style='float:right;text-align:right;width:150px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='text' id = 'payAmount' name='paymentAmount'/>
										</div>
									</div>
									<div class='row'>
										<div class='col-7'>
											<b>Kembalian</b>
										</div>
										<div class='col-5'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='changeAmount' style='float:right;text-align:right;width:150px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='hidden' id = 'change' name='changeAmount'/>
										</div>
									</div>
									<div class='row'>
										<div class='col-7'>
											<b>Serv. Charge (5%)</b>
										</div>
										<div class='col-5'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='serviceCharge' style='float:right;text-align:right;width:150px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='hidden' id = 'charge' name='serviceCharge'/>
										</div>
									</div>
									<div class='row'>
										<div class='col-7'>
											<b>PB1 (10%)</b>
										</div>
										<div class='col-5'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='pb1' style='float:right;text-align:right;width:150px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='hidden' id = 'pb' name='pb1'/>
										</div>
									</div>
									<div class="row">
									<div class='col-7'>
											<b>Grand Total</b>
										</div>
										<div class='col-5'>
											<input type='text' readonly class="form-control-sm form-control-plaintext" id ='totalPrice' style='float:right;text-align:right;width:120px;font-weight:bold;vertical-align:top;' value='Rp. 0,-'/>
											<input type='hidden' id='totPrice' name='totalPrice' />
										</div>
									</div>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<input type='text' readonly class="form-control-plaintext" id ='voucherDesc' style='width:100%;'/>
									<input type='hidden' id = 'voucherVal' name='voucherVal'/>
									<input type='hidden' id = 'voucherReq' name='voucherReq'/>
								</div>
							</div>
							<!-- <div class='row mb-2'>
								<div class='col'>
									<input class="form-control" type='text' id ='voucherCode' name ='voucherCode' style='width:100%;' placeholder='Insert Voucher Code'/>
								</div>
							</div> -->
							<div class='row mb-2'>
								<div class='col'>
									<select class="form-control" id ='orderMethod' name ='orderMethod' style='width:100%;'>
										<option value=''>-PILIH METODE PEMESANAN-</option>
										<option value='1'>DINE IN</option>
										<option value='2'>TAKE AWAY</option>
									</select>
								</div>
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<select class="form-control" id ='paymentMethod' name ='paymentMethod' style='width:100%;'>
										<option value=''>-PILIH METODE PEMBAYARAN-</option>
								<?php
									$queryMethod="SELECT * FROM mPaymentMethod WHERE status != 0 ORDER BY methodID";
									$resMethod=mysql_query($queryMethod);
									while($rowMethod=mysql_fetch_array($resMethod)){
										echo "<option value='$rowMethod[methodID]'>$rowMethod[methodName]</option>";
									}
								?>
									</select>
								</div>
							</div>
							<!-- <div class='row mb-2'>
								<div class='col'>
									<select class="form-control" id ='promo' name ='promo' style='width:100%;'>
										<option value=''>-CHOOSE PROMO-</option>
								<?php
									$queryPromo="SELECT * FROM mPromo WHERE status != 0 AND startDate <= '$today' AND endDate >= '$today' ORDER BY promoID";
									$resPromo=mysql_query($queryPromo);
									while($rowPromo=mysql_fetch_array($resPromo)){
										echo "<option value='$rowPromo[promoID]'>$rowPromo[promoName]</option>";
									}
								?>
									</select>
								</div>
							</div> -->
							<!-- <div class='row mb-2'>
								<div class='col'>
									<input type='text'class="form-control" id ='discounto' name ='discounto' style='width:100%;' placeholder='Discount' />
								</div>
							</div> -->
							<div class='row mb-2'>
								<div class='col'>
									<input type='text'class="form-control" id ='payment' name ='payment' style='width:100%;' placeholder='Insert Nominal Pembayaran' />
								</div>
							</div>
							<!-- <div class='row mb-2'>
								<div class='col'>
									<input type='text'class="form-control" id ='payment1' name ='payment1' style='width:100%;' placeholder='Insert Payment If Using Voucher Amount' />
								</div>
							</div> -->
							<div class='row mb-2'>
								<div class='col'>
									<textarea width='100' placeholder='Insert notes here' class="form-control" name='remarks'></textarea>
								</div>	
							</div>
							<div class='row mb-2'>
								<div class='col'>
									<div class='d-flex justify-content-center'>
										<button type='button' id='draft' class='btn btn-warning submit' style='width:50%;'>DRAFT</button>
                            			<button type='button' id='order' class='btn btn-success submit' style='width:50%;'>ORDER</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='col-lg-8'>
						<div class='container leftist' id='productList'>
							<div class='row'>
								<div class="dropdown ml-3">
									<select class='btn btn-secondary' name='category' id='categorySel'>
								  	<option class='form-control' value=''>SEMUA KATEGORI</option>
								<?php
									$resCat = mysql_query("SELECT DISTINCT c.categoryID,c.categoryName FROM mCategory c INNER JOIN mProduct P ON p.categoryID = c.categoryID WHERE p.outletID = '$outletID' AND c.status = 1 ORDER BY categoryID DESC");
									while($rowCat = mysql_fetch_array($resCat)){
								?>
								  	<option class='form-control' value='<?php echo $rowCat['categoryID'] ?>'><?php echo $rowCat['categoryName']; ?></option>
								<?php } ?>
									</select>
								</div>

								<div class='col input-group'>
								    <input type="text" class="form-control" id='searchMenu' placeholder="Cari Menu">
								 <div class="input-group-append">
								      <button class="btn btn-secondary" type="button" id="searchBtn">
								        <i class="fa fa-search"></i>
								      </button>
								 </div>
								</div>

							</div>

							
							<div class='row'>
							<?php
								$resMenu = mysql_query("SELECT * FROM mProduct WHERE outletID = '$outletID' AND status = 1");
								while($rowMenu = mysql_fetch_array($resMenu)){
									$productID = $rowMenu['productID'];
								echo"
									<div class='d-flex ml-1 productEntry'>
										<div class='pt-2'>
											<div class='col-12'>
											    <div class='card' id='products'>
									        		<button type='button' class='menu d-flex' id='$rowMenu[productID]'>
									            <div class=''>
									                <img src='../../productImages/$rowMenu[productImage]' alt='$rowMenu[productName]' width='50px' height='50px'>	
									            </div>
									            <div class='col-6'>
									        	    <span>$rowMenu[productName]</span><input type='hidden' id = 'curProd_$rowMenu[productID]' value='$rowMenu[curStock]'/> 
									            </div>
									            <div class='col-3'>
													<span>Stock : <span/><input type='text' readonly class='form-control-sm form-control-plaintext' id ='stock_$rowMenu[productID]' style='text-align:center;font-weight:bold;vertical-align:top;' value='$rowMenu[curStock]'/>
									            </div>
									        		</button>
									   			</div>
											</div>
										</div>
									</div>
									";
								}
							?>
							</div>							
						</div>
					</div>
				</div>
			</form>
		</div>
    </div>
	</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
	
	$(document).on('change', '#paymentMethod', function(){
		var paymentMethod = $('#paymentMethod option:selected').text();
		var totalPrice = $('#totPrice').val();

		// console.log(payment);
		
		if(paymentMethod != 'CASH'){
			$('#payment').val(totalPrice);
			$('#payAmount').val(totalPrice);

		} else {
			$('#payment').val(0);
		}
	});

    function searchProduct(){
   	const searchCategory = $("#categorySel").val();
   	const searchKeyword = $("#searchMenu").val();

   	$.ajax(`getProductSearch.php?categoryID=${searchCategory}&productName=${searchKeyword}`, {
   		success:function(data, textStatus, jqXHR){
   			const results = JSON.parse(data);
   			$(".productEntry").remove();
   			for(const result of results){
   				$("#productList").append(`
   					<div class='row d-flex ml-1 productEntry'>
						<div class='pt-2'>
							<div class='col-12'>
							    <div class='card' id='products'>
					        		<button type='button' class='menu d-flex' id='${result.productID}'>
					            <div class=''>
					                <img src='../../productImages/${result.productImage}' alt='${result.productImage}' width='50px' height='50px'>	
					            </div>
					            <div class='col-6'>
					        	    <span>${result.productName}</span><input type='hidden' id = 'curProd_${result.productID}' value='${result.curStock}'/> 
					            </div>
					            <div class='col-3'>
									<span>Stock : <span/><input type='text' readonly class='form-control-sm form-control-plaintext' id ='stock_${result.productID}' style='text-align:center;font-weight:bold;vertical-align:top;' value='${result.curStock}'/>
					            </div>
					        		</button>
					   			</div>
							</div>
						</div>
					</div>
				`);
   			}
   		}
   	});
   }

   $("#searchBtn").click(function(){
   		searchProduct();
   });

   $("#categorySel").change(function(){
   		searchProduct();
   });

    function list(){
    	var customerID = $("#listCust").val();
    	$.ajax({
    		url: 'getCustomer.php',
    		data:"customerID="+customerID,
    	}).success(function(data){
    		var json = data,
    		obj = JSON.parse(json);
    		$('#customerName').val(obj.customerName);
    		$('#customerPhone').val(obj.customerPhone);
    		$('#customerEmail').val(obj.customerEmail);
    	})
    }
	
    $(document).on("click", ".menu", function(){
		var btn = $(this).attr("id");
		var prodStockID = $('#stock_'+btn).attr("id");
		var curStock=$('#'+prodStockID).val();
		
		//01-02-2023
		var orderType = $('#methodList').val();
		
		var stockAfter=curStock-1;
		
		if(stockAfter<0){
			alert("PRODUCT OUT OF STOCK");
		}else{
			if(stockAfter==0){
			   $( "#"+prodStockID ).css({"color":"red"});
			}
			var new_chq_no = parseInt($('#itemCount').val()) + 1;
			$('#'+prodStockID).val(stockAfter);
			$('tr.deft').remove();

			var html = '';
			var price = "";
			var productName = "";
			var get = "getProduct.php?productID="+btn+"&priceID="+orderType;
			var servCharge5 = "";
			var pb1 = ""; 

			$.get(get, function( data ) {
				price = data.price;
				servCharge5 = data.servCharge5;
				pb1 = data.pb1;
				productName = data.productName;

				var numbering = price;

				var numbering = numbering.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

				html += "<tr id='additem"+new_chq_no+"'><td class = 'table-details' style='text-align:left;width:35%;padding-left:5px;'>"+productName+"</td>";
				html += "<td class = 'table-details' style='text-align:left;width:1%;'>&nbsp;&nbsp;Rp. </td>";
				html += "<td class = 'table-details' style='text-align:right;width:29%;padding-right:5px;'>";
				html += "<input type='hidden' name='productID[]' value='"+btn+"'/>";
				html += "<input type='hidden' name='servCharge5[]' value='"+servCharge5+"'/>";
				html += "<input type='hidden' name='pb10[]' value='"+pb1+"'/>";
				html += "<input type='hidden' name='ori_servCharge5[]' value='"+servCharge5+"'/>";
				html += "<input type='hidden' name='ori_pb1[]' value='"+pb1+"'/>";
				html += "<input type='text' name='productPrice[]' class = 'inps' style='width:100px;' value='"+price+"'/></td>";
				html += "<td class = 'table-details' style='text-align:center;width:5%;'>";
				html += "<input type='text' name='productQty[]' class='inps' style='width:40px;' value='1'/>";
				html += "<input type='hidden' name='productSubtotal[]' style='width:40px;' value='"+price+"'/></td>";
				html += "<td class = 'table-details' style='text-align:left;width:1%;padding-left:5px;'>Rp. </td>";
				html += "<td class = 'table-details' style='text-align:right;width:24%;padding-right:5px;'><input type='text' name='productSutot[]' readonly style='width:100px;' value='"+numbering+"'/></td>";
				html += "<td class = 'table-details' style='text-align:left;width:5%;padding-left:5px;'>";
				html += "<button type='button' class='del' style='border:none;margin-left:-10px;background-color:rgba(255, 255, 255, 0);'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' class='bi bi-trash' viewBox='0 0 16 16'><path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/><path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/></svg></button></td></tr>";

				$('#tableProduct tbody').append(html);
				$('#itemCount').val(new_chq_no);

				var totProd = parseInt($('#totalProd').val());
				var dpp = parseFloat($('#dpp').val());
				var grand = parseFloat($('#totPrice').val());
				var discount = parseFloat($('#discPer').val());
				var received = parseFloat($('#payAmount').val());
				var change = parseFloat($('#change').val());
				//var charge = parseFloat($('#charge').val());
				//var pb = parseFloat($('#pb').val());
				
				if ( isNaN( received ) ){
					received = 0;
				}
				
				var disc = 0;

				price = parseFloat(price);

				var sum = 0;
				var sumVal = 0;
				$( 'input[name^=productQty]' ).each( function( i , e ) {
					var v = parseInt( $( e ).val() );
					if ( !isNaN( v ) ){
						sum += v;
					}else{
						sum += 0;
					}

				} );

				$( 'input[name^=productSubtotal]' ).each( function( i , e ) {
					var v = parseInt( $( e ).val() );
					if ( !isNaN( v ) ){
						sumVal += v;
					}else{
						sum += 0;
					}

				} );

				var totalServCharge5 = 0;
				$( 'input[name^=servCharge5]' ).each( function( i , e ) {
					var v = parseInt( $( e ).val() );
					if ( !isNaN( v ) ){
						totalServCharge5 += v;
					}
				} );

				var totalPb1 = 0;
				$("#tableProduct").find( 'input[name^=pb10]' ).each( function( i , e ) {
					var v = parseInt( $( e ).val() );
					if ( !isNaN( v ) ){
						totalPb1 += v;
					}
				} );
				

				totProd = sum;
				dpp = sumVal;
				charge = totalServCharge5;
				pb = totalPb1;
				disc = dpp*(discount/100);
				grand = sumVal+charge+pb-disc;
				change = received-grand;

				var totalProduct = totProd;
				var grossPrice = dpp;
				var totalPrice = grand;
				var discounts = disc;
				var changeVal = change;
				var servCharge = charge;
				var pebe = pb;

				$('#totalProd').val(totProd);
				$('#dpp').val(dpp);
				$('#disc').val(disc);
				$('#totPrice').val(grand);
				$('#change').val(change);
				$('#charge').val(charge);
				$('#pb').val(pb);

				totalProduct = totalProduct.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
				grossPrice = "Rp. "+grossPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				discounts = "Rp. "+discounts.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				servCharge = "Rp. "+servCharge.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				pebe = "Rp. "+pebe.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

				$('#totalProduct').val(totalProduct);
				$('#grossPrice').val(grossPrice);
				$('#discount').val(discounts);
				$('#totalPrice').val(totalPrice);
				$('#changeAmount').val(changeVal);
				$('#serviceCharge').val(servCharge);
				$('#pb1').val(pebe);

			}, "json" );
		}
    });
	
	$(document).on('input', '.inps', function(event){
		var $row = $(this).closest("tr"); //this is the closest common root of the input elements
    	var amount = parseInt($row.find('input[name^=productQty]').val());
        var unitPrice = parseFloat($row.find('input[name^=productPrice]').val());
        var productID = $row.find('input[name^=productID]').val();
		var prodStockID = $('#stock_'+productID).attr("id");
		var curStock=$('#curProd_'+productID).val();
		
		var curSell = 0;
		var totalServCharge5 = 0;
		var totalPb1 = 0;
		$( 'input[name^=productQty]' ).each( function( i , e ) {
			var v = parseInt( $( e ).val() );
			if ( !isNaN( v ) ){
				var $rows = $(this).closest("tr");
				var prodID = $rows.find('input[name^=productID]').val();
				var _servCharge5 = $rows.find('input[name^=ori_servCharge5]').val();
				var _pb1 = $rows.find('input[name^=ori_pb1]').val();
				
				if(prodID==productID){
					curSell += v;
					if(v != 0){
						$rows.find('input[name^=servCharge5]').val(parseInt(_servCharge5) * v );
						$rows.find('input[name^=pb10]').val(parseInt(_pb1) * v );
						totalServCharge5 += parseInt(_servCharge5) * v;
						totalPb1 += parseInt(_pb1) * v;
					}
				}
				else{
					totalServCharge5 += parseInt(_servCharge5);
					totalPb1 += parseInt(_pb1);
				}
			}
		} );
		var stockAfter=curStock-curSell;
		
		if(stockAfter<0){
			alert("PRODUCT OUT OF STOCK");
			$('#'+prodStockID).val(0);
			$( "#"+prodStockID ).css({"color":"red"});
		}else{
			if(stockAfter==0){
			   $( "#"+prodStockID ).css({"color":"red"});
			}else{
			   $( "#"+prodStockID ).css({"color":"black"});
			}
			$('#'+prodStockID).val(stockAfter);
			
			var price = 0;
			price = amount*unitPrice;

			$row.find('input[name^=productSubtotal]').val(price);

			var numbering = amount*unitPrice;

			var numbering = numbering.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

			$row.find('input[name^=productSutot]').val(numbering);

			var prc = $row.find('input[name^=productSubtotal]').val();

			var totProd = parseInt($('#totalProd').val());
			var dpp = parseFloat($('#dpp').val());
			var grand = parseFloat($('#totPrice').val());
			var discount = parseFloat($('#discPer').val());
			var disc = 0;
			var received = parseFloat($('#payAmount').val());
			var change = parseFloat($('#change').val());
			var charge = totalServCharge5;
			var pb = totalPb1;

			if ( isNaN( received ) ){
				received = 0;
			}

			var sum = 0;
			var sumVal = 0;
			$( 'input[name^=productQty]' ).each( function( i , e ) {
				var v = parseInt( $( e ).val() );
				if ( !isNaN( v ) ){
					sum += v;
				}else{
					sum += 0;
				}

			} );

			$( 'input[name^=productSubtotal]' ).each( function( i , e ) {
				var v = parseInt( $( e ).val() );
				if ( !isNaN( v ) ){
					sumVal += v;
				}else{
					sum += 0;
				}

			});

			totProd = sum;
			dpp = sumVal;
			disc = dpp*(discount/100);
			grand = sumVal+charge+pb-disc;
			change = received-grand;

			var totalProduct = totProd;
			var grossPrice = dpp;
			var totalPrice = grand;
			var discounts = disc;
			var changeVal = change;
			var servCharge = charge;
			var pebe = pb;

			$('#totalProd').val(totProd);
			$('#dpp').val(dpp);
			$('#disc').val(disc);
			$('#totPrice').val(grand);
			$('#change').val(change);

			totalProduct = totalProduct.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
			grossPrice = "Rp. "+grossPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			discounts = "Rp. "+discounts.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			servCharge = "Rp. "+servCharge.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			pebe = "Rp. "+pebe.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

			$('#totalProduct').val(totalProduct);
			$('#grossPrice').val(grossPrice);
			$('#discount').val(discounts);
			$('#totalPrice').val(totalPrice);
			$('#changeAmount').val(changeVal);
			$('#charge').val(servCharge);
			$('#pb').val(pebe);
			$('#serviceCharge').val(servCharge);
			$('#pb1').val(pebe);
		}
	});		
	
	
	$('body').on('click', '.del', function(event){
		var $row = $(this).closest("tr"); //this is the closest common root of the input elements
		var id = $row.attr("id");
        var productID = $row.find('input[name^=productID]').val();
		var prodStockID = $('#stock_'+productID).attr("id");
		var curStock=$('#curProd_'+productID).val();
		
		$('#'+id).remove();
		$('tr.deft').remove();
		
		var curSell = 0;
		var totalServCharge5 = 0;
		var totalPb1 = 0;
		$( 'input[name^=productQty]' ).each( function( i , e ) {
			var v = parseInt( $( e ).val() );
			if ( !isNaN( v ) ){
				var $rows = $(this).closest("tr");
				var prodID = $rows.find('input[name^=productID]').val();
				var _servCharge5 = $rows.find('input[name^=ori_servCharge5]').val();
				var _pb1 = $rows.find('input[name^=ori_pb1]').val();
				
				if(prodID==productID){
					curSell += v;
					$rows.find('input[name^=servCharge5]').val(parseInt(_servCharge5) * v );
					$rows.find('input[name^=pb10]').val(parseInt(_pb1) * v );
					totalServCharge5 += parseInt(_servCharge5) * v;
					totalPb1 += parseInt(_pb1) * v;
				}
				else{
					totalServCharge5 += parseInt(_servCharge5) * v;
					totalPb1 += parseInt(_pb1) * v;
				}
			}
		} );
		var stockAfter=curStock-curSell;
		console.log([totalServCharge5, totalPb1]);
		if(stockAfter<0){
			alert("PRODUCT OUT OF STOCK");
			$('#'+prodStockID).val(0);
			$( "#"+prodStockID ).css({"color":"red"});
		}else{
			if(stockAfter==0){
			   $( "#"+prodStockID ).css({"color":"red"});
			}else{
			   $( "#"+prodStockID ).css({"color":"black"});
			}
			$('#'+prodStockID).val(stockAfter);
		
			var totProd = parseInt($('#totalProd').val());
			var dpp = parseFloat($('#dpp').val());
			var grand = parseFloat($('#totPrice').val());
			var discount = parseFloat($('#discPer').val());
			var disc = 0;
			var received = parseFloat($('#payAmount').val());
			var change = parseFloat($('#change').val());
			//var charge = parseFloat($('#charge').val());

			if ( isNaN( received ) ){
				received = 0;
			}

			var len = $('#tableProduct tbody tr').length;
			var sum = 0;
			var sumVal = 0;
			$( 'input[name^=productQty]' ).each( function( i , e ) {
				var v = parseInt( $( e ).val() );
				if ( !isNaN( v ) ){
					sum += v;
				}else{
					sum += 0;
				}

			} );

			$( 'input[name^=productSubtotal]' ).each( function( i , e ) {
				var v = parseInt( $( e ).val() );
				if ( !isNaN( v ) ){
					sumVal += v;
				}else{
					sum += 0;
				}

			} );

			if(len==0){
				var html="";
				html += "<tr class='deft'><td class = 'table-details' style='text-align:left;width:35%;padding-left:5px;'>Product Name</td>";
				html += "<td class = 'table-details' style='text-align:left;width:1%;'>&nbsp;&nbsp;Rp. </td>";
				html += "<td class = 'table-details' style='text-align:right;width:29%;padding-right:5px;'>";
				html += "<input type='hidden' name='productID[]' value='0'/>";
				html += "<input type='text' name='productPrice[]' class = 'inps' style='width:100px;' value='0'/></td>";
				html += "<td class = 'table-details' style='text-align:center;width:5%;'>";
				html += "<input type='text' name='productQty[]' class='inps' style='width:40px;' value='0'/>";
				html += "<input type='hidden' name='productSubtotal[]' style='width:40px;' value='0'/></td>";
				html += "<td class = 'table-details' style='text-align:left;width:1%;padding-left:5px;'>Rp. </td>";
				html += "<td class = 'table-details' style='text-align:right;width:24%;padding-right:5px;'><input type='text' name='productSutot[]' readonly style='width:100px;' value='0'/></td>";
				html += "<td class = 'table-details' style='text-align:left;width:5%;padding-left:5px;'>";
				html += "<button type='button' class='del' style='border:none;margin-left:-10px;background-color:rgba(255, 255, 255, 0);'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' class='bi bi-trash' viewBox='0 0 16 16'><path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/><path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/></svg></button></td></tr>";

				$('#tableProduct tbody').append(html);   
				totProd = 0;
				dpp = 0;
				grand = 0;
				disc = 0;
				change = 0;
				charge = 0;

			}else{
				
				totProd = sum;
				dpp = sumVal;
				disc = dpp*(discount/100);
				grand = sumVal-disc;
				change = received-grand;
				//charge = dpp*(5/100);
			}

			
			var totalProduct = totProd;
			var grossPrice = dpp;
			var totalPrice = grand+totalServCharge5+totalPb1;
			var discounts = disc;
			var changeVal = change;
			var servCharge = totalServCharge5;
			var pb = totalPb1

			$('#totalProd').val(totProd);
			$('#dpp').val(dpp);
			$('#disc').val(disc);
			$('#totPrice').val(grand);
			$('#change').val(change);
			$('#charge').val(servCharge);
			$('#pb').val(totalPb1);

			totalProduct = totalProduct.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
			grossPrice = "Rp. "+grossPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			discounts = "Rp. "+discounts.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			servCharge = "Rp. "+servCharge.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			pb = "Rp. "+pb.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

			$('#totalProduct').val(totalProduct);
			$('#grossPrice').val(grossPrice);
			$('#discount').val(discounts);
			$('#totalPrice').val(totalPrice);
			$('#changeAmount').val(changeVal);
			$('#serviceCharge').val(servCharge);
			$('#pb1').val(pb);
		}
    });	

	$(document).on('change', '#paymentMethod', function(){
		var paymentMethod = $('#paymentMethod option:selected').text();
		var totalPrice = $('#totPrice').val();

		var pay = $('#payment').val();
		var grand = parseFloat($('#totPrice').val());
		var change = parseFloat($('#change').val());
		
		if(paymentMethod != 'CASH'){
			$('#payment').val(totalPrice);
			$('#payAmount').val(totalPrice);

			totalPrices = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";			
			
			$('#paymentAmount').val(totalPrices);

			change = pay-grand;
			var changeVal = change;
			$('#change').val(change);
			changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			$('#changeAmount').val(changeVal);

		} else {
			$('#payment').val(0);
		}
	});
	
	$('body').on('keyup', '#payment', function(event){
		var pay = $('#payment').val();
		var grand = parseFloat($('#totPrice').val());
		var change = parseFloat($('#change').val());

		console.log(pay);

		if (pay==""){
			pay = 0;
		}
		
		var pays = pay;
		pays = "Rp. "+pays.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
		$('#paymentAmount').val(pays);
		$('#payAmount').val(pay);
		
		
		change = pay-grand;
		var changeVal = change;
		$('#change').val(change);
		changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
		$('#changeAmount').val(changeVal);
		
    });	
	
	$('body').on('keyup', '#voucherCode', function(event){

		var voucherCode = $('#voucherCode').val();
		var get = "getVoucher.php?voucherCode="+voucherCode;
		var total = parseInt($('#totPrice').val());
		var change = 0;
		
		$.get(get, function( data ) {
			var voucherName = data.voucherName;
			var voucherType = data.voucherType;
			var voucherSaldo = parseInt(data.voucherSaldo);
			var voucherRequirement = parseInt(data.voucherRequirement);
			var expDate = data.expDate;
			var desc = data.desc;
			var stat = data.status;
			
			if(voucherName.length==0){
				$('#voucherDesc').val("");
				$('#payment').attr('readonly', false);
				$('#voucherVal').val(0); 
				$('#payment').val(0);
				$('#payAmount').val(0);
				var payAmo = "Rp. 0,-";
				$('#paymentAmount').val(payAmo);
				change = 0-total;
				var changeVal = change;
				$('#change').val(change);
				changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				$('#changeAmount').val(changeVal);
			}else{
				if(stat=="EXPIRED"){
					$('#voucherDesc').val(desc);
					$('#voucherDesc').css("color", "red");
					$('#voucherDesc').css("font-weight", "bold");
					$('#payment').attr('readonly', false);
					$('#voucherVal').val(0); 
					$('#payment').val(0);
					$('#payAmount').val(0);
					var payAmo = "Rp. 0,-";
					$('#paymentAmount').val(payAmo);
					
					change = 0-total;
					var changeVal = change;
					$('#change').val(change);
					changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
					$('#changeAmount').val(changeVal);
					
				}else{
					if(total >= voucherRequirement){
						$('#voucherDesc').val(desc); 
						$('#voucherDesc').css("color", "green");
						$('#voucherDesc').css("font-weight", "bold");
						$('#voucherVal').val(voucherSaldo); 
						
						change = voucherSaldo-total;
						var changeVal = change;
						$('#change').val(change);
						changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
						$('#changeAmount').val(changeVal);

						var payAmount = $('#payAmount').val(); 

						$('#payAmount').val(voucherSaldo);
						$('#payment').val(voucherSaldo);
						$('#payment').attr('readonly', true);

						var payAmo = voucherSaldo;
						payAmo = "Rp. "+payAmo.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

						$('#paymentAmount').val(payAmo);
					}else{
						$('#voucherDesc').val("REQUIREMENT NOT MET");
						$('#voucherDesc').css("color", "red");
						$('#voucherDesc').css("font-weight", "bold");
						$('#payment').attr('readonly', false);
						$('#voucherVal').val(0); 
						$('#payment').val(0);
						$('#payAmount').val(0);
						change = 0-total;
						var changeVal = change;
						$('#change').val(change);
						changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
						$('#changeAmount').val(changeVal);
						
						var payAmo = "Rp. 0,-";
						$('#paymentAmount').val(payAmo);
					}
				}
			}
			
		}, "json" );
	});	
    
	$('body').on('change', '#promo', function(event){
		var promoID = $('#promo').val();
		var len = promoID.length;
		
		if(len==0){
			var dpp = $('#dpp').val();
			$('#discPer').val(0);
			$('#disc').val(0);
			var disc = 0;
			
			disc = "Rp. "+disc.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			$('#discount').val(disc);
			var totalPrice = dpp;
			
			var received = parseFloat($('#payment').val());
			var change = 0;

			if (isNaN(received)){
				received = 0;
			}

			change = received-totalPrice;
			var changeVal = change;
			$('#change').val(change);
			changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			$('#changeAmount').val(changeVal);

			totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

			$('#totalPrice').val(totalPrice);

			$('#totPrice').val(dpp);   
		}else{
			var get = "getPromo.php?promoID="+promoID;
			$.get(get, function( data ) {
				var promoName = data.promoName;
				var promoType = data.promoType;
				var promoRequirement = parseFloat(data.promoRequirement);
				var startDate = data.startDate;
				var endDate = data.endDate;
				var isDiscount = data.isDiscount;
				var discount = parseInt(data.discount);
				var isMonday = data.isMonday;
				var isTuesday = data.isTuesday;
				var isWednesday = data.isWednesday;
				var isThursday = data.isThursday;
				var isFriday = data.isFriday;
				var isSaturday = data.isSaturday;
				var isSunday = data.isSunday;

				var now = new Date();
				var day = now.getDay();
				var promoStatus = "";
				var dpp = $('#dpp').val();

				console.log(now);

				switch(day){
					case 0:
						if(isSunday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
					case 1:
						if(isMonday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
					case 2:
						if(isTuesday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
					case 3:
						if(isWednesday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
					case 4:
						if(isThursday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
					case 5:
						if(isFriday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
					case 6:
						if(isSaturday!=1){
							promoStatus = "AVAILABLE";
						}else{
							promoStatus = "UNAVAILABLE";
						}
						break;
				}
				if(dpp>=promoRequirement){
					if(promoType=="DISCOUNT"){
						$('#discPer').val(0);

						var discVal = dpp*(discount/100);
						$('#disc').val(discVal);

						var disc = discVal;
						disc = "Rp. "+disc.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

						$('#discount').val(disc);

						var totalPrice = dpp-discVal;
						var totPrice = totalPrice;
						
						var received = parseFloat($('#payment').val());
						var change = 0;
						
						if (isNaN(received)){
							received = 0;
						}

						
						change = received-totalPrice;
						var changeVal = change;
						$('#change').val(change);
						changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
						$('#changeAmount').val(changeVal);

						totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

						$('#totalPrice').val(totalPrice);

						$('#totPrice').val(totPrice);
					}else{
						$('#discPer').val(0);
						$('#disc').val(0);
						var disc = 0;

						disc = "Rp. "+disc.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
						$('#discount').val(disc);
						var totalPrice = dpp;
						
						var received = parseFloat($('#payment').val());
						var change = 0;
						
						if (isNaN(received)){
							received = 0;
						}

						
						change = received-totalPrice;
						var changeVal = change;
						$('#change').val(change);
						changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
						$('#changeAmount').val(changeVal);

						totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

						$('#totalPrice').val(totalPrice);

						$('#totPrice').val(dpp);   

					}
				}else{
					alert("REQUIREMENT NOMINAL NOT MET!");
					$('#discPer').val(0);
					$('#disc').val(0);
					var disc = 0;

					disc = "Rp. "+disc.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
					$('#discount').val(disc);
					var totalPrice = dpp;
					
					var received = parseFloat($('#payment').val());
					var change = 0;

					if (isNaN(received)){
						received = 0;
					}


					change = received-totalPrice;
					var changeVal = change;
					$('#change').val(change);
					changeVal = "Rp. "+changeVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
					$('#changeAmount').val(changeVal);

					totalPrice = "Rp. "+totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

					$('#totalPrice').val(totalPrice);

					$('#totPrice').val(dpp);  
				}
			}, "json" );
		}
		
	});	
	
	
	
    $('body').on('click', '.submit', function(event){
		var btn = $(this).attr("id");
		var payment = parseFloat($('#payment').val());
		var paymentMethod = $('#paymentMethod').val();
		var grand = parseFloat($('#totPrice').val());
		var voucherVal = parseFloat($('#voucherVal').val());
		
		if(voucherVal==0){
			$('#voucherCode').val("");
		}
		
		switch(btn){
			case "order":
				var lenP = payment.length;
				var lenPM = paymentMethod.length;
		
				if(lenP==0 || lenPM==0){
					alert("PLEASE INPUT PAYMENT INFORMATION");
					$("#payment").prop('required',true);
					$("#paymentMethod").prop('required',true);
				}else{
					
					if(payment>=grand){
						$('#status').val('1');
						$('form#formOrder').submit();   
					}else{
						alert("ERROR: INSUFFICIENT PAYMENT");
					}   
				}
				break;
			case "draft":
				$("#payment").prop('required',false);
				$("#paymentMethod").prop('required',false);
				
				$('#status').val('0');
				$('form#formOrder').submit();
				break;
		}
		
	});
	
</script>

<script>
  $(window).load(function() { $(".se-pre-con").fadeOut("slow");	});  
</script>
<script src="../../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../../assets/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../assets/dist/js/demo.js"></script>