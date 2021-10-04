<?php
//ini_set('display_errors', 0);
//error_reporting(0);
//Servidor phpMyAdminPruebas:
/*
 $host = "192.168.1.67";
 $user = "dane_eas";
 $pass = "d4N3E4M2o11Camb10S";
 $db = "dimpe_eas";
 */ 

 /*
 $host = "192.168.1.67";
 $user = "gmejiaz";
 $pass = "LeM4nGow2nL4LtlYZmM9";
 $db = "encuestas_eas";
 */

// $host = "192.168.1.200";
$host = "192.168.1.200";
$user = "dimpe";
$pass = "D1mP3D3s4rr0ll0";
//$db = "encuestas_eas_joprod";
$db = "encuestas_eas_gocapruebas";

if (isset($_GET['debug-dane'])) {
	echo "<pre>";
	print_r("host : " . $host . "</br>");
	print_r("user : " . $user . "</br>");
	print_r("pass : " . $pass . "</br>");
	print_r("db : " . $db . "</br>");
	echo "</pre>";
}



$con = mysqli_connect($host, $user, $pass, $db);
if (!$con) {
	die("No se puede conectar: " . mysqli_error());
}
//mysqli_select_db($db, $con) or die(mysql_error());


?>
