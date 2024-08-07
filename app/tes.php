<?php
session_start();
if (!isset($_SESSION["username"])) 
{
    $URL="/picapos/app"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
}
include("../../assets/config/db.php");		
include('../assets/template/navbar_app.php');
date_default_timezone_set('Asia/Jakarta');

?>
<div class='p-2'>
	<div class='card'>
		<button type='button' class='menu' style='display:flex;' id='$rowMenu[productID]'>
			<img src='../../productImages/$rowMenu[productImage]' alt='$rowMenu[productName]' width='50px' height='50px'>
		<div>
			<br/><b>$rowMenu[productName]</b><input type='hidden' id = 'curProd_$rowMenu[productID]' value='$rowMenu[curStock]'/>
			<br/><b>Stock : <b/><input type='text' readonly class='form-control-sm form-control-plaintext' id ='stock_$rowMenu[productID]' style='text-align:center;width:100px;font-weight:bold;vertical-align:top;' value='$rowMenu[curStock]'/>
		</div>									
		</button>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="../../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../../assets/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../assets/dist/js/demo.js"></script>