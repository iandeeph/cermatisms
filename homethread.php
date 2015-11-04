<?php
$userpriv = $_SESSION['priv'];
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
				ORDER BY time DESC";
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
				ORDER BY time DESC";
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
<!-- end -->
