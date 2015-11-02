<?php
$userpriv = $_SESSION['priv'];
$username = $_SESSION['user'];
$mergetable = "SELECT 
			DATE_FORMAT(date, '%e %b %Y - %k:%i') as date, 
			date as time,
			number 
			FROM (
				select 
					max(SendingDateTime) as date, 
					replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
					count(TextDecoded) as TotalSMS 
					FROM sentitems
					WHERE CreatorID = '".$username."'
					GROUP BY number) t 
			ORDER BY time DESC";
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
		echo '<a href="index.php?menu=thread&cat=detail&number='.$mergerow['number'].'&lastID='.$lastIdMsg.'" class="collection-item">';
		?>
	<span style="display:inline-block; width:150px"><?php echo $mergerow['date'];?></span>
	<span style="display:inline-block; width:350px"><?php echo $name;?></span>
	<span style="display:inline-block; width:700px"><?php echo $case;?></span>
		</a>
		<?php
		}else{
				$name = $mergerow['number'];
				$urlname = "";
				$case = "-";
		}
}
	?>
</div>
<!-- end -->
