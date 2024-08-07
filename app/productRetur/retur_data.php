<?php
session_start();
if (!isset($_SESSION["username"])) 
{
    $URL="/picapos/admin"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
}
include("../../assets/config/db.php");

$requestData = $_REQUEST;

$query = "SELECT r.returID, r.returDate, c.categoryName, p.productName, r.returAmount, r.returStatus, u.username, r.approvedBy 
            FROM tabreturproduct r
            INNER JOIN mcategory c ON c.categoryID = r.categoryID
            INNER JOIN mproduct p ON p.productID = r.productID
            INNER JOIN muser u ON u.username = r.username";
$res = mysql_query($query);

$x=0;
$data = array();
while ($row=mysql_fetch_array($res)){
    $x+=1;
    $nestedData = array();
    
    $nestedData[no] = $x;
    $nestedData[returID] = $row['returID'];
    $nestedData[returDate] = $row["returDate"];
    $nestedData[categoryName] = $row["categoryName"];
    $nestedData[productName] = $row["productName"];
    $nestedData[returAmount] = $row["returAmount"];

	switch($row["returStatus"]){
		case 1:
			$nestedData[returStatus] = "APPROVED";
            $nestedData[action] = "";
			break;
		case 2:
			$nestedData[returStatus] = "WAITING APPROVAL";
            $nestedData[action] = "";
			break;
		case 3:
			$nestedData[returStatus] = "REJECTED";
            $nestedData[action] = "";
			break;
	}

    $nestedData[username] = $row["username"];
    $nestedData[approvedBy] = $row["approvedBy"];
	

    $nestedData[lastChanged] = $row["lastChanged"];
    $nestedData[action] = "<a href='method_input.php?methodID=$row[methodID]'>EDIT</a>";
    
    $data[] = $nestedData;
}

$json_data = array(
        "draw" => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
        "recordsTotal" => mysql_num_rows($res),  // total number of records
        "recordsFiltered" => mysql_num_rows($res), // total number of records after searching, if there is no searching then totalFiltered = totalData
        "data" => $data   // total data array
    );
    echo json_encode($json_data);

?>