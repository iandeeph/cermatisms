<?php
if(isset($_GET['filter'])){
	$filter = $_GET['filter'];
}else{
	$filter = "";
}
switch ($filter) {
	case 'on':
		include "searchSentItems.php";
		break;
	default:
		include "homeSentItems.php";
		break;
}
?>