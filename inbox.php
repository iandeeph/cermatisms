<?php
if(isset($_GET['filter'])){
	$filter = $_GET['filter'];
}else{
	$filter = "";
}
switch ($filter) {
	case 'on':
		include "searchInbox.php";
		break;
	default:
		include "homeInbox.php";
		break;
}
?>