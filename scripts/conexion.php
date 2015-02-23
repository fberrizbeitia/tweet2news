<?php 
//@session_start();

/*
$dbname="lomastui_mercadeo";
$dbhost="localhost";
$dbusr="lomastui_merca";
$dbpass="l4sveg4s";
*/

$dbname="newsConstruction";
$dbhost="localhost";
$dbusr="root";
$dbpass="";


$conn = mysql_connect($dbhost, $dbusr, $dbpass) 
			 or die("No puedo conectarme: " . mysql_error());

mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET'utf8'");

mysql_select_db($dbname);

mb_internal_encoding("utf-8");

// some of the process mighy take some time
set_time_limit(0);
//----------------------------------

function closeConnection($conn){
	mysql_close($conn);
}
?>