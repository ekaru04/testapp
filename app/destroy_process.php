<?php  
	session_start();

	session_destroy();

	$URL="/picaPOS/app/"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";

?>