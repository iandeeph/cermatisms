<?php
$userpriv = $_SESSION['priv'];
if(!isset($_GET['pages'])){
	$_GET['pages'] = 1;
}
$perPages = 15;

if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
$mergetable1 = "SELECT 
				DATE_FORMAT(date, '%e %b %Y - %k:%i') as date, 
				date as time,
				number, 
				TotalSMS
				FROM (
					SELECT 
						max(date) as date, 
						number, 
						sum(TotalSMS) as TotalSMS
						FROM (
							select 
								max(ReceivingDateTime) as date, 
								replace(replace(SenderNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS
								FROM inbox
								GROUP BY number 

								UNION ALL select 

								max(SendingDateTime) as date, 
								replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS 
								FROM sentitems 
								GROUP BY number) t 
						GROUP BY number) mergetable
				ORDER BY time DESC";
} else {
	$mergetable1 = "SELECT 
				DATE_FORMAT(date, '%e %b %Y - %k:%i') as date, 
				date as time,
				number, 
				TotalSMS
				FROM (
					SELECT 
						max(date) as date, 
						number, 
						sum(TotalSMS) as TotalSMS
						FROM (
							select 
								max(ReceivingDateTime) as date, 
								replace(replace(SenderNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS
								FROM inbox 
								GROUP BY number 

								UNION ALL select 

								max(SendingDateTime) as date, 
								replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS 
								FROM sentitems
								WHERE CreatorID != 'admin'
								GROUP BY number) t 
						GROUP BY number) mergetable
				ORDER BY time DESC";
}

$getMergeQuery = mysql_query($mergetable1);
$totCont = mysql_num_rows($getMergeQuery);
$totPages = ceil($totCont/$perPages);

//pagination code
if(isset($_GET['pages'])){
	$curPages = $_GET['pages'];
	if ($curPages>0 && $curPages<=$totPages) {
		$start = ($curPages-1)*$perPages;
		$end = $start+$perPages;
	}else{
		$start=0;
		$end=$perPages;
	}
}else{
	$start=0;
	$end=$perPages;
}

if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
$mergetable = "SELECT 
				DATE_FORMAT(date, '%e %b %Y - %k:%i') as date, 
				date as time,
				number, 
				TotalSMS
				FROM (
					SELECT 
						max(date) as date, 
						number, 
						sum(TotalSMS) as TotalSMS
						FROM (
							select 
								max(ReceivingDateTime) as date, 
								replace(replace(SenderNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS
								FROM inbox
								GROUP BY number 

								UNION ALL select 

								max(SendingDateTime) as date, 
								replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS 
								FROM sentitems 
								GROUP BY number) t 
						GROUP BY number) mergetable
				ORDER BY time DESC LIMIT ".$perPages." OFFSET ".$start."";
} else {
	$mergetable = "SELECT 
				DATE_FORMAT(date, '%e %b %Y - %k:%i') as date, 
				date as time,
				number, 
				TotalSMS
				FROM (
					SELECT 
						max(date) as date, 
						number, 
						sum(TotalSMS) as TotalSMS
						FROM (
							select 
								max(ReceivingDateTime) as date, 
								replace(replace(SenderNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS
								FROM inbox 
								GROUP BY number 

								UNION ALL select 

								max(SendingDateTime) as date, 
								replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
								count(TextDecoded) as TotalSMS 
								FROM sentitems
								WHERE CreatorID != 'admin'
								GROUP BY number) t 
						GROUP BY number) mergetable
				ORDER BY time DESC LIMIT ".$perPages." OFFSET ".$start."";
}

$page=intval($_GET['pages']);
$tpages=$totPages;

if($page<=0)$page=1;

if($curPages <= 1){
	$dissleft = "disabled";
}else{$dissleft="";}
if($curPages >= $tpages){
	$dissright = "disabled";
}else{$dissright="";}

if ($curPages > 1) {
	$prevCurPage = $curPages -1;
	$prevPage = $_SERVER['PHP_SELF']."?menu=thread&pages=".$prevCurPage."&lastID=".$lastIdMsg;
}else{
	$prevPage = '';
}
if ($curPages < $tpages) {
	$nextCurPage = $curPages+1;
	$nextPage = $_SERVER['PHP_SELF']."?menu=thread&pages=".$nextCurPage."&lastID=".$lastIdMsg;
}else{
	$nextPage = '';
}
?>
<!-- start -->				
<div class="collection">
<?php
$newSMS = 0;
$mergequery = mysql_query($mergetable);
while($mergerow = mysql_fetch_array($mergequery)){

	$query = mysql_query("SELECT *,
		replace(replace(phone,'+62','0'), '+628', '08') as number 
		FROM customer 
		WHERE replace(replace(phone,'+62','0'), '+628', '08') = '".$mergerow["number"]."'");
	if($query && mysql_num_rows($query)){
		while($row = mysql_fetch_array($query)){
				$name = $row['name']." - (".$mergerow['number'].")";
				$urlname = $row['name'];
				$case = $row['hal'];
			}
		}else{
				$name = $mergerow['number'];
				$urlname = "";
				$case = "-";
		}

	if($queryNewSMS = mysql_query("SELECT count(readStatus) as newSMS FROM inbox where readStatus = 'unread' and replace(replace(SenderNumber,'+62','0'), '+628', '08') = '".$mergerow['number']."'")){
		$rowNewSMS = mysql_fetch_array($queryNewSMS);
	}else{
		echo mysql_error($conn);
	}
	

	echo '<a href="index.php?menu=thread&cat=detail&number='.$mergerow['number'].'&lastID='.$lastIdMsg.'" class="collection-item new">';
	?>
	<span style="display:inline-block; width:150px"><?php echo $mergerow['date'];?></span>
	<span style="display:inline-block; width:350px"><?php echo $name;?></span>
	<span style="display:inline-block; width:700px"><?php echo $case;?></span>
	<?php
		if($rowNewSMS['newSMS'] == 0){
			echo '<span class="badge">'.$mergerow['TotalSMS'].'</span>';			
		} else {
			echo '<span class="new badge">'.$rowNewSMS['newSMS'].'</span>';
		}
	?>
	</a>
	<?php
	}
	?>
</div>
<div class="col s12">
		<div class="center">
			<ul class="pagination">
				<li class="waves-effect <?php echo $dissleft; ?>" <?php echo $dissleft; ?>><a href="<?php echo $prevPage; ?>" class="<?php echo $dissleft; ?>"><i class="material-icons">chevron_left</i></a></li>
		<?php
		$almostLast = $totPages-4;
		$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=thread&pages=1&lastID=".$lastIdMsg."'>1 ... </a></li>";
		$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=thread&pages=".$totPages."&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";

		if($curPages <= 0 || $curPages > $totPages){
			header('Location: ./?menu=thread&pages=1&lastID='.$lastIdMsg);
		}elseif ($curPages >= 1 && $curPages <= 5) {
			$liFirstPage = "";
			$firstPosPage = 1;
			$lastPosPage = 10;
			$liLastPage = $lastPage;
		}elseif ($curPages > 5 && $curPages <= $almostLast) {
			$liFirstPage = $firstPage;
			$firstPosPage = $curPages-4;
			$lastPosPage = $curPages+4;
			$liLastPage = $lastPage;
		}elseif ($curPages >= $almostLast && $curPages <= $totPages) {
			$liFirstPage = $firstPage;
			$firstPosPage = $totPages-9;
			$lastPosPage = $totPages;
			$liLastPage = "";
		}else{
			header('Location: ./?menu=thread&pages=1&lastID='.$lastIdMsg);
		}

		echo $liFirstPage;

		for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=thread&pages=".$j."&lastID=".$lastIdMsg."'>".$j."</a></li>";
		}

		echo $liLastPage;

		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			</ul>
		</div>
	</div>
<!-- end -->
