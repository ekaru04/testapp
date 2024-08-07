<?php
session_start();
include("../../assets/config/db.php");

$requestData = $_REQUEST;
$closeID = $requestData[closeID];
$closeDate = $requestData[closeDate];
$outletID = $requestData[outletID];
$closeShift = $requestData[closeShift];

$query = "SELECT h.orderID, h.orderNo, h.orderDate, h.orderPeriode, h.orderAmount, p.total, p.dpp, p.vat, p.paymentAmount, p.paymentMethod, h.payerName, m.methodName, h.userID, u.fullname, h.remarks FROM taborderheader h 
INNER JOIN tabpaymentorder p ON h.orderID = p.orderID
INNER JOIN mpaymentmethod m ON p.paymentMethod = m.methodID 
INNER JOIN muser u ON h.userID = u.userID WHERE h.status != 0 AND h.userID = '$_SESSION[userID]' AND h.outletID = '$outletID' AND h.orderDate = '$closeDate' ORDER BY h.orderNo";
$res = mysql_query($query);

$x=0;
$data = array();
while ($row=mysql_fetch_array($res)){
    $x+=1;
    $nestedData = array();
    
    $nestedData[no] = $x;
    $nestedData[orderNo] = $row[orderNo];
    $nestedData[orderID] = $row["orderID"];
    $nestedData[orderDate] = $row["orderDate"];
    $nestedData[orderPeriode] = $row["orderPeriode"];
    $nestedData[orderAmount] = $row["orderAmount"];
    $nestedData[dpp] = $row["dpp"];
    $nestedData[vat] = $row["vat"];
    $nestedData[total] = $row["total"];
    $nestedData[paymentAmount] = $row["paymentAmount"];
    $nestedData[paymentMethod] = $row["paymentMethod"];
    $nestedData[payerName] = $row["payerName"];
    $nestedData[remarks] = $row["remarks"];
    $nestedData[userID] = $row["userID"];
    $nestedData[methodName] = $row["methodName"];
    $nestedData[fullname] = $row["fullname"];
    
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