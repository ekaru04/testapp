<?PHP
	include("../../assets/config/db.php");
	session_start();

$user = $_SESSION['userID'];
$outletID = $_SESSION['outletID'];
$orderID = $_GET[orderID];
$orderNo = $_GET[orderNo];
$dateCreated = date("Y-m-d");
$lastChanged = date("Y-m-d H:i:s");
$journalID = date("YmdHis");

$query = "UPDATE taborderheader SET 
				status='9',
				lastChanged='$lastChanged'
				WHERE orderID = '$orderID' AND outletID = '$outletID'";
$res = mysql_query($query);

$query = "UPDATE taborderDetail SET 
				status='9',
				lastChanged='$lastChanged'
				WHERE orderID = '$orderID' AND status = '0'";
$res = mysql_query($query);

/* Insert SystemJournal */
$journalID = date("YmdHis");
$act = "ORDER_".$orderNo."_".$orderID."_DELETED";

$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,userID,dateCreated,logCreated,status) VALUES('$journalID','$act','ORDER_DELETE','$user','$dateCreated','$lastChanged', 'SUCCESS')";
$resJournal = mysql_query($queryJournal);

echo "<script type='text/javascript'>alert('DRAFT DELETED!')</script>";
$URL="/picaPOS/app/order"; 
echo "<script type='text/javascript'>location.replace('$URL');</script>";
?>