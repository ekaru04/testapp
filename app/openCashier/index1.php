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
$openDate = date("Y-m-d");
$per = date("Ym");

if($_GET['openID']==""){
    $queryOpenNew = mysql_query("SELECT count(openID)+1 as count FROM tabopencashier WHERE outletID = '$_SESSION[outletID]'");
    $row = mysql_fetch_array($queryOpenNew);
    $count = $row['count'];
	
	$openID = "PCA/OPEN/$per/".str_pad($count,4,"0",STR_PAD_LEFT);
	$openDate = date("Y-m-d");
	$fullName = $_SESSION['fullname'];
	
	$resOutlet = mysql_query("SELECT * FROM mOutlet WHERE outletID = '$_SESSION[outletID]'");
	$rowO = mysql_fetch_array($resOutlet);
	$outletName = $rowO[outletName];
	$grandTotal = 0;
	$totalReceived = 0;
	
	
}else{
    $openID = $_GET['openID'];
    $queryGet = mysql_query("SELECT o.*,u.fullName FROM tabopenheader o INNER JOIN mUser u ON u.userID = o.userID WHERE o.openID ='$openID'");
    $row = mysql_fetch_array($queryGet);
	// $closeDate = $row['closeDate'];
	// $periode = $row['closePeriode'];
	// $fullName = $row['fullname'];
	// $grandTotal = $row['grandTotal'];
	// $totalReceived = $row['totalReceived'];
	// $grandTot = "Rp. ".number_format($grandTotal,0,",",".").",-";
	// $received= "Rp. ".number_format($totalReceived,0,",",".").",-";
}			
?>

<div class="">
		<div class='clear height-20 mt-3'></div>
		<div class="container-fluid">
			<div class='entry-box-basic'>
                <h3>OPEN CASHIER</h3>
                <form id='formOpen' method='POST' action='open_process.php'>
					
					<div class='row  mt-3 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='OPEN ID' />
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='openID' id='openID' style='width:300px' value='<?php echo $openID; ?>' required readonly/>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='TANGGAL OPEN' />
                        </div>
                        <div class='col-4'>
                            <input type='date' class='form-control' name='openDate' id='openDate' style='width:300px' value='<?php echo $openDate; ?>' readonly required />
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='OUTLET' />
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='outletName' id='outletName' style='width:300px' value='<?php echo $outletName; ?>' disabled />
							<input type='hidden' name='outletID' id = 'outletID' value='<?php echo $_SESSION[outletID]; ?>'>
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='PERIODE' />
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='periode' id='periode' style='width:300px' value='<?php echo $periode; ?>' readonly required />
                        </div>
					</div>
					
					<div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='KASIR' />
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' name='fullName' id='fullName' style='width:300px' value='<?php echo $fullName; ?>'readonly required />
                            <input type='hidden' name='userID' id='userID' value='<?php echo $userID; ?>'/>
                        </div>
					</div>
					
					<!-- <div class='row  mt-1 '>
                        <div class='col-2 label'>
                            <input type='text' class='form-control-plaintext' style='width:150px' disabled value='MODAL' />
                        </div>
                        <div class='col-4'>
                            <input type='text' class='form-control' id='modalCash' style='width:300px' name='modalCash' value='<?php echo $grandTot; ?>' required />
							<input type='hidden' name='modalTotCash' id = 'modalTotCash' value='<?php echo $grandTotal; ?>'>
                        </div>
					</div> -->
                    <div class="row mt-1">
                        <div class="col-2 label">
                            <input type="text" class="form-control-plaintext" style='width:150px' disabled value='CASH'>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" id='modalCash' style='width:300px' name='modalCash' required>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-2 label">
                            <input type="text" class="form-control-plaintext" style='width:150px' disabled value='BCA'>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" id='modalBca' style='width:300px' name='modalBca' required>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-2 label">
                            <input type="text" class="form-control-plaintext" style='width:150px' disabled value='MANDIRI'>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" id='modalMandiri' style='width:300px' name='modalMandiri' required>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-2 label">
                            <input type="text" class="form-control-plaintext" style='width:150px' disabled value='MAS'>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" id='modalMas' style='width:300px' name='modalMas' required>
                        </div>
                    </div>
					<div class='row  mt-1 '>
						<button class = "btn" id = "add"><span class = "glyphicon glyphicon-plus"></span> Add new Entry</button>
					</div>
					
					<div class='row  mt-3 '>
                        <div class='col-5' style='text-align:center;margin-left:-50px;'>
							<!-- <button type='button' id='generate' class='btn btn-info' >GENERATE</button>   -->
							<button type='submit' value='submit' id='save' name='submit' class='btn btn-success' >SIMPAN</button>  
                        </div>
					</div>
                </form>
            </div>
		</div>
        </div>
	</body>
</html>
<script type="text/javascript">

// $(document).ready(function(){
//         $('#add').click(function(){
//                 input = $('<div class = "row mt-1">'   
//                 + '<div class = "col-2 label">'
//                 + '<label>Tipe Modal</label>'
//                 + '<select class="form-control" name="modalTypeID_1" id="modalTypeID[]">'
//                 + '<option value="">-</option>'
//                 + '<option value="1">CASH/TUNAI</option>'
//                 + '<option value="2">BCA</option>'
//                 + '<option value="3">MANDIRI</option>'
//                 + '</select>'
//                 + '</div>'
//                 + '<div class = "form-group">'
//                 + '<label>Nominal</label>'
//                 + '<input class = "form-control" type="text" name="modalNominal_1" required = "required"/>'
//                 + '</div>');
//        		$('#formOpen').append(input);
//         });
//     });

</script>

<script>
  $(window).load(function() { $(".se-pre-con").fadeOut("slow");	});  
</script>
<script src="../../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../../assets/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../assets/dist/js/demo.js"></script>