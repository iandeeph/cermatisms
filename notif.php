<?php
ini_set("display_errors", "1");
ini_set('error_reporting', E_ALL);
include('php/connconf.php');

$lastID = intval($_GET['last']);

$queryNewMsg = mysql_query("SELECT *, replace(replace(replace(SenderNumber,'+62','0'),'62', '0'), '+628', '08') as number  FROM inbox WHERE ID > '".$lastID."'") or die(mysql_error());
$newMsg = mysql_num_rows($queryNewMsg);

if($newMsg > 0) {
	$res = array();
	while($msg = mysql_fetch_array($queryNewMsg)) {
		$res[] = $msg;
	}
	echo json_encode($res);
}
?>