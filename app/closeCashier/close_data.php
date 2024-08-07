<?php
session_start();
include("../../assets/config/db.php");

$requestData = $_REQUEST;

$query = "SELECT c.closeID, c.closeDate, c.closePeriode, c.closeShift, c.firstModal, c.newModal, c.grandTotal, c.totalReceived, u.fullname, c.lastChanged 
FROM tabCloseCashierHeader c 
INNER JOIN muser u ON c.username = u.username WHERE c.status = 1 AND c.outletID = '$_SESSION[outletID]' ORDER BY c.lastChanged";
$res = mysql_query($query);

$x=0;
$data = array();
while ($row=mysql_fetch_array($res)){
    $x+=1;
    $nestedData = array();

    $grandTotal = number_format($row["grandTotal"]);
    $totalReceived = number_format($row["totalReceived"]);
    $firstModal = number_format($row["firstModal"]);
    $newModal = number_format($row["newModal"]);
    $totalReceived = number_format($row["totalReceived"]);
    
    $nestedData[no] = $x;
    $nestedData[closeID] = $row[closeID];
    $nestedData[closeDate] = $row["closeDate"];
    $nestedData[closePeriode] = $row["closePeriode"];
    $nestedData[closeShift] = $row["closeShift"];
    $nestedData[firstModal] = $firstModal;
    $nestedData[newModal] = $newModal;
    $nestedData[grandTotal] = $grandTotal;
    $nestedData[totalReceived] = $totalReceived;
    $nestedData[fullname] = $row["fullname"];
	
    $nestedData[lastChanged] = $row["lastChanged"];
    $nestedData[action] = "<a href='close_views.php?closeID=$row[closeID]'>VIEW</a>";
    
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