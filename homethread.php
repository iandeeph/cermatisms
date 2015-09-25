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
	
	echo '<a href="index.php?menu=thread&threadid=detail&senderid='.$name.'&phone='.$mergerow['number'].'&case='.$case.'&name='.$urlname.'" class="collection-item">';
	?>
	<span style="display:inline-block; width:300"><?php echo $mergerow['date'];?></span>
	<span style="display:inline-block; width:500"><?php echo $name;?></span>
	<span style="display:inline-block; width:700"><?php echo $case;?></span>
	<span class="badge"><?php echo $mergerow['TotalSMS'];?></span>
	</a>
	<?php
	}
	?>
</div>
<!-- end -->
