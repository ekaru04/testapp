<?php
session_start();
if (!isset($_SESSION["username"])) 
{
    $URL="/picapos/app"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
} 
include("../../assets/config/db.php");		
include('../../assets/template/navbar_app.php');

?>
<style>
	.label{
			margin-right: 20px;
	}
</style>
<?php
$periode = date("Y-m");
$per = date("Ym");

if($_GET['closeID']==""){
    $query = "SELECT count(closeID)+1 as count FROM tabCloseCashierHeader WHERE outletID = '$_SESSION[outletID]'";
    //$query = "SELECT count(closeID)+1 as count FROM tabCloseCashierHeader WHERE closeID ='$closeID' AND outletID = '$_SESSION[outletID]'";
    $res = mysql_query($query);
    $row = mysql_fetch_array($res);
    $count = $row['count'];
	
	$closeID = "PCA/CLOSE/$per/".str_pad($count,4,"0",STR_PAD_LEFT);
	$closeDate = date("Y-m-d");
	$fullName = $_SESSION['fullname'];
	
	$resO = mysql_query("SELECT * FROM mOutlet WHERE outletID = '$_SESSION[outletID]'");
	$rowO = mysql_fetch_array($resO);
	$outletName = $rowO[outletName];
	$grandTotal = 0;
	$totalReceived = 0;
	
	
}else{
    $closeID = $_GET['closeID'];
    $query = mysql_query("SELECT c.*,u.fullName FROM tabCloseCashierHeader c INNER JOIN mUser u ON u.userID = c.userID WHERE c.closeID ='$closeID'");
	$row = mysql_fetch_array($query);
	$closeDate = $row['closeDate'];
	$periode = $row['closePeriode'];
	$fullName = $row['fullname'];
	$grandTotal = $row['grandTotal'];
	$totalReceived = $row['totalReceived'];
	$grandTot = "Rp. ".number_format($grandTotal,0,",",".").",-";
	$received= "Rp. ".number_format($totalReceived,0,",",".").",-";
}			
?>

<div class="">
		<div class='clear height-20 mt-3'></div>
		<div class="container-fluid">
			<div class='entry-box-basic'>
                <h3>
                    <a href='/picaPOS/app/closeCashier' style='text-decoration:none;color:black;'><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
</svg>
					</a> 
					CLOSE CASHIER
				</h3>
                <form id='formClose' method='POST' action='close_process.php'>
					
					<div class='row  mt-3 '>
                        <div class='col-2 label'>
                            <h6><b>CLOSE ID</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='closeID' id='closeID' style='width:300px' value='<?php echo $closeID; ?>' required readonly/>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
							<h6><b>TANGGAL CLOSE</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='date' class='form-control' name='closeDate' id='closeDate' style='width:300px' value='<?php echo date('Y-m-d'); ?>' required />
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>OUTLET</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='outletName' id='outletName' style='width:300px' value='<?php echo $outletName; ?>' disabled />
							<input type='hidden' name='outletID' id = 'outletID' value='<?php echo $_SESSION[outletID]; ?>'>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>PERIODE</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='closePeriode' id='closePeriode' style='width:300px' value='<?php echo $periode; ?>' readonly required />
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>KASIR</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='fullName' id='fullName' style='width:300px' value='<?php echo $fullName; ?>'readonly required />
                            <input type='hidden' name='userID' id='userID' value='<?php echo $userID; ?>'/>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='SESSION' />
                        </div>
                        <div class='col-4'>
                            <select class='form-control' name='closeShift' id='closeShift' style='width:300px;' required>
                                <option value=''>-CHOOSE ONE-</option>
                                <option value='1' $session1 >1</option>
                                <option value='2' $session2 >2</option>
							</select>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>TOTAL (TANPA PAJAK)</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='dppTotal' style='width:300px' value='<?php echo $grandTot; ?>'readonly required />
							<input type='hidden' name='dppTotal' id = 'dppTot' value='<?php echo $grandTotal; ?>'>
                        </div>
					</div>
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>PAJAK</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='taxTotal' style='width:300px' value='<?php echo $grandTot; ?>'readonly required />
							<input type='hidden' name='taxTotal' id = 'taxTot' value='<?php echo $grandTotal; ?>'>
                        </div>
					</div>
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>MODAL AWAL</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='modalTotal' style='width:300px' value='<?php echo $grandTot; ?>' readonly required />
							<input type='hidden' name='firstModal' id = 'modalTot' value='<?php echo $grandTotal; ?>'>
                        </div>
					</div>
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>MODAL TAMBAHAN</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='newModal' style='width:300px' value='<?php echo $grandTot; ?>' required />
							<input type='hidden' name='newModal' id = 'newMod' value='<?php echo $grandTotal; ?>'>
                        </div>
					</div>
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>PENGELUARAN</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='outTotal' style='width:300px' value='' readonly required />
							<input type='hidden' name='outTotal' id = 'outTot' value=''>
                        </div>
					</div>
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>GRANDTOTAL PENJUALAN</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='grandTotal' style='width:300px' value='<?php echo $grandTot; ?>'readonly required />
							<input type='hidden' name='grandTotal' id = 'grandTot' value='<?php echo $grandTotal; ?>'>
                        </div>
					</div>
<?php
					
					$resPayMethod = mysql_query("SELECT * FROM mpaymentmethod WHERE status = 1");
					$closeDate = date('Y-m-d');
					while($rowP = mysql_fetch_array($resPayMethod)){
						$payName = $rowP[methodName];
						$payID = "Method_$rowP[methodID]";
						$payMetID = "Meth_$rowP[methodID]";
					$querySplit = mysql_query("SELECT SUM(p.total) AS splitTotal FROM tabpaymentorder p
									INNER JOIN taborderheader o ON p.orderID = o.orderID
									WHERE paymentMethod = '$rowP[methodID]' AND orderDate = '$closeDate'");
						
						
echo			   "<div class='row mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='$payName' />
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='payID_".$payID."' style='width:300px' readonly required />
							<input type='hidden' name='grandTotals' id = 'Meth_".$payID."'>
                        </div>
					</div>";
					}
					
?>
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>DITERIMA</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='totalReceived' style='width:300px' value='<?php echo $received; ?>'readonly required />
							<input type='hidden' name='totalReceived' id = 'received' value='<?php echo $totalReceived; ?>'>
                        </div>
					</div>

					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <h6><b>FINAL</b></h6>
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='finalOwned' style='width:300px' value='<?php echo $received; ?>'readonly required />
							<input type='hidden' name='finalOwned' id = 'finalOwn' value='<?php echo $totalReceived; ?>'>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            REMARKS
                        </div>
                        <div class='col-4'>
                            <textarea class='form-control' name='remarks' placeholder='Insert notes here' style='width:300px' rows='4' cols='25'><?php echo $row[remarks]; ?></textarea>
                        </div>
					</div>
					
					<div class='row  mt-3 '>
                        <div class='col-5' style='text-align:center;margin-left:-50px;'>
							<button type='button' id='generate' class='btn btn-info' >GENERATE</button>  
							<button type='button' id='calculate' class='btn btn-info' >calculate</button>  
							<button type='button' id='save' class='btn btn-success' >SAVE</button>  
                        </div>
					</div>
                    
					<div class='row  mt-3 '>
                        <div class='col-20'>
							<table class='table' id='detailData' style='font-size:13px;'>
								  <thead>
									<tr>
									  <th style='vertical-align:middle;'>NO</th>
									  <th style='vertical-align:middle;'>ORDER ID</th>
									  <th style='vertical-align:middle;'>ORDER DATE</th>
									  <th style='vertical-align:middle;'>TOTAL</th>
									  <th style='vertical-align:middle;'>TAX</th>
									  <th style='vertical-align:middle;'>GRAND TOTAL</th>
									  <th style='vertical-align:middle;'>RECEIVED</th>
									  <th style='vertical-align:middle;'>PAYMENT METHOD</th>
									  <th style='vertical-align:middle;'>PAYER</th>
									  <th style='vertical-align:middle;'>REMARKS</th>
									  <th style='vertical-align:middle;'>USER</th>
									  <th style='vertical-align:middle;'>-</th>
									</tr>
								  </thead>
								  <tbody>

								  </tbody>
							</table>
						</div>
					</div>
                </form>
            </div>
		</div>
        </div>
	</body>
</html>
<script type="text/javascript">
    
	$(document).ready(function(){  
        $('#detailData').hide();
        $('#save').hide();
        $('#calculate').hide();
    });

	
	
	$("#generate").click(function(){
		var closeID = $('#closeID').val();
		var closeDate = $('#closeDate').val();
		var outletID = $('#outletID').val();
		var closeShift = $('#closeShift').val();
		var newModal = $('#newModal').val(0);
		
		var grand = 0;
		var received = 0;
		var method = $('#payID').val();
		
		// console.log(closeShift);

		var detailData = $('#detailData').DataTable(
		{

			processing : false,
			responsive : true,
			ajax: {
				url: "detail_data.php?closeShift="+closeShift,
				data: { closeID: closeID, closeDate: closeDate, outletID: outletID, closeShift: closeShift }
			},  
			columns: [
				{ data: 'no' },
				{ data: 'orderID' },
				{ data: 'orderDate' },
				{ data: 'dpp' },
				{ data: 'vat' },
				{ data: 'total'},
				{ data: 'paymentAmount'},
				{ data: 'methodName' },
				{ data: 'payerName' },
				{ data: 'remarks' },
				{ data: 'fullname' }
			],
    		"columnDefs": [
				{"className": "dt-center", "targets": "_all"}
			]
		});


		var getIngre = "detail_ingre.php?transDate="+closeDate+"&closeShift="+closeShift;
		var getStock = "detail_stock.php?transItemDate="+closeDate+"&closeShift="+closeShift;
		var getModal = "detail_modal.php?openDate="+closeDate+"&outletID="+outletID;
		// var totalStock = 0;
		// var totalIngre = 0;

		$.get(getIngre, function(dataIngre) {
			var totalIngres = parseFloat(dataIngre.total);
			var totalIngre = ~~totalIngres; // set totalIngre to 0 if totalIngres is NaN or Null
			console.log("Total Ingredients:", totalIngre);

			// Perform AJAX request for total stock
			$.get(getStock, function(dataStock) {
				var totalStocks = parseFloat(dataStock.total);
				var totalStock = ~~totalStocks; // set totalStock to 0 if totalStocks is NaN or Null
				console.log("Total Stock:", totalStock);

				// Calculate totalAll
				var totalAll = totalStock + totalIngre;
				console.log("Total All:", totalAll);
				$('#outTot').val(totalAll);
				totalAll = "Rp. "+totalAll.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				$('#outTotal').val(totalAll);

				// Proceed with other actions that depend on the completion of both requests
				// For example:
				// updateUI(totalAll);
			}, "json").fail(function(jqXHR, textStatus, errorThrown) {
				console.error("Error fetching total stock:", errorThrown);
			});

			$.get(getModal, function( dataModal ) {
				var modalTotal = dataModal.nominalOpen;

				// console.log(data);
				$('#modalTot').val(modalTotal);
				modalTotal = "Rp. "+modalTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
				// console.log(modalTotal);
				$('#modalTotal').val(modalTotal);

			}, "json" );

		}, "json").fail(function(jqXHR, textStatus, errorThrown) {
			console.error("Error fetching total ingredients:", errorThrown);
		});		
		
		var get = "detail_sum.php?closeID="+closeID+"&closeDate="+closeDate+"&outletID="+outletID+"&closeShift="+closeShift;
		$.get(get, function( data ) {
			var dpp = data.dpp;
			var VAT = data.VAT;
			var total = data.total;
			var paymentAmount = data.paymentAmount;

			// var totalA = dataA.total;
			// var paymentAmountA = dataA.paymentAmount;
			$('#dppTot').val(dpp);
			$('#taxTot').val(VAT);
			$('#grandTot').val(total);
			$('#received').val(paymentAmount);
			
			// $('#payID').val(paymentAmountA);
			var dppTotal = dpp;
			var taxTotal = VAT;
			var grandTotal = total;
			var totalReceived = paymentAmount;
			// var splitA = paymentAmountA;
			// var totalReceived = paymentAmount;
			dppTotal = "Rp. "+dppTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			taxTotal = "Rp. "+taxTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			grandTotal = "Rp. "+grandTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			totalReceived = "Rp. "+totalReceived.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
			// splitA = "Rp. "+splitA.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";

			$('#dppTotal').val(dppTotal);
			$('#taxTotal').val(taxTotal);
			$('#grandTotal').val(grandTotal);
			$('#totalReceived').val(totalReceived);
			// $('#totalReceived').val(totalReceived);
			
			console.log(data.arrayNew);
			for(var payment of data.arrayNew){
				$(payment.id).val(payment.total);
				$(payment.id2).val(payment.total);
			}
			
		}, "json" );
		
        $('#detailData').show();
		$('#generate').hide();
        $('#calculate').show();
		
    });

	$("#calculate").click(function(){
		var modalTot = parseFloat($('#modalTot').val());
		var out = parseFloat($('#outTot').val());
		var newMod = parseFloat($('#newModal').val());
		var grandTot = parseFloat($('#grandTot').val());
		var received = parseFloat($('#received').val());

		// var change = received-grandTot;

		// console.log(change);

		var finalTotals = (modalTot+newMod+grandTot)-out;
		var finalTotal = ~~finalTotals;

		console.log("Modal Total", modalTot);
		console.log("Modal New", newMod);
		console.log("grand Total", grandTot);
		console.log("Out Total", out);
		console.log("All Totla", finalTotal);

		$('#finalOwn').val(finalTotal);
		finalTotal = "Rp. "+finalTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")+",-";
		$('#finalOwned').val(finalTotal);

        $('#save').show();

		// console.log("Modal Total", modalTot);
		// console.log("Out Total", out);
		// console.log("New Modal Total", newMod);
		// console.log("Penjualan Total", grandTot);
	});
	
	$("#closeDate").change(function(){
		var closeDate = $('#closeDate').val();
		var d = new Date(closeDate);
	
		var mo = parseInt(d.getMonth())+1;
		mo = mo.toString();
		mo = mo.padStart(2,"0");
		var y = d.getFullYear();
		
		var period = y+"-"+mo;
		
		$('#closePeriode').val(period);
    });
	
    $("#save").click(function(){
		
		$('form#formClose').submit();   
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