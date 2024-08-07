<?php
session_start();
include("../../assets/config/db.php");

$requestData = $_REQUEST;

$query = "SELECT h.orderID,h.orderNo,h.orderDate,h.orderPeriode,h.orderAmount,p.total,m.methodName,u.fullname,h.lastChanged FROM taborderheader h
INNER JOIN tabpaymentorder p ON h.orderID = p.orderID 
LEFT JOIN mpaymentmethod m ON p.paymentMethod = m.methodID 
INNER JOIN muser u ON h.userID = u.userID WHERE h.status = 0 AND h.outletID = '$_SESSION[outletID]' ORDER BY h.lastChanged";
$res = mysql_query($query);

$x=0;
$data = array();
while ($row=mysql_fetch_array($res)){
    $x+=1;
    $nestedData = array();
    
    $nestedData[no] = $x;
    $nestedData[orderNo] = $row[orderNo];
    $nestedData[orderDate] = $row["orderDate"];
    $nestedData[orderPeriode] = $row["orderPeriode"];
    $nestedData[orderAmount] = $row["orderAmount"];
    $nestedData[total] = $row["total"];
    $nestedData[methodName] = $row["methodName"];
    $nestedData[fullname] = $row["fullname"];
	
    $nestedData[lastChanged] = $row["lastChanged"];
    $nestedData[action] = "<a href='manage_order.php?orderID=$row[orderID]'>VIEW</a>";
    
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