<?php
    include("../../assets/config/db.php");

    $categoryID = $_GET['categoryID'];
    // $outletID = $_GET['outletID'];
    $productID = $_GET['productID'];
    $query = "SELECT * FROM mproduct where categoryID = '$categoryID' AND status = 1 ORDER BY productID";
    $res = mysql_query($query);
    
    echo "<option value = ''>-PLEASE CHOOSE-</option>";
    while($row=mysql_fetch_array($res)){
		if($row[productID]==$productID){
			echo "<option value='$row[productID]' selected>$row[productName]</option>";
		}else{
			echo "<option value='$row[productID]'>$row[productName]</option>";
		}
        
    }
?>