<?php 

include("../assets/config/db.php");
session_start();

if(isset($_POST['submit'])){

$username = $_POST['username'];
$pass = hash('sha256', $_POST['password']);
$query = mysql_query("SELECT * FROM muser WHERE username='$username' AND password='$pass'");
$date = date('Y-m-d');

$rowUser = mysql_num_rows($query);

	if($rowUser > 0)
	{
		while($cek = mysql_fetch_array($query)){
			$_SESSION['username'] = $username;
			$_SESSION['userID'] = $cek['userID'];
			$_SESSION['fullname'] = $cek['fullname'];
			$_SESSION['outletID'] = $cek['outletID'];

			echo "<script type='text/javascript'>alert('Berhasil Login')</script>";

			$queryOpen = mysql_query("SELECT * FROM tabopencashier WHERE openDate = '$date'");
			if(mysql_num_rows($queryOpen) != 0) {
				
				$URL = "/picapos/app/pos";
				echo "<script type='text/javascript'>location.replace('$URL');</script>";

			} else {
				
				$URL="/picaPOS/app/openCashier"; 
				echo "<script type='text/javascript'>location.replace('$URL');</script>";
				
			}

		}
	}
	else
	{
		echo "<script type='text/javascript'>alert('Gagal Login')</script>";
		$URL="/picaPOS/app/"; 
    	echo "<script type='text/javascript'>location.replace('$URL');</script>";
		
	}
}

?>