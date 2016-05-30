<?php 
	session_start();
	 unset($_SESSION["username"]);
//session_destroy();
echo "<script>window.location = '".$_SERVER['HTTP_REFERER']."';</script>";
?>
