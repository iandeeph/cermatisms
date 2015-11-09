<!-- FILTERING -->
<div class="col s12">
	<ul class="collapsible" data-collapsible="accordion">
		<li>
			<div class="collapsible-header"><i class="material-icons">search</i>Filter</div>
			<div class="col s12 collapsible-body" style="margin-top:30px;">
				<form action="" method="POST">
					<div class="col s3">
						<label class="active" for="datefrom">Date From</label>
						<input id="datefrom" type="date" class="datepicker">
					</div>
					<div class="col s3">
						<label class="active" for="dateto">Date To</label>
						<input id="dateto" type="date" class="datepicker">
					</div>
					<div class="col s3">
						<label class="active" for="sender">Sender</label>
						<input placeholder="Name/Phone number.. (Leave blank for any number..)" id ="sender" type="text" class="validate">							
					</div>
					<div class="input-field col s3" style="margin-bottom:5px">
							<select>
								<option value="" disabled selected>Status</option>
								<option value="1">Read</option>
								<option value="2">Unread</option>
							</select>
							<label>Select Status</label>
						</div>
					<div class="col s12">
						<label class="active" for="case">Cust Case</label>
						<input id ="case" type="text" class="validate">							
					</div>
					<div class="col s12" style="margin-bottom:15px">
						<a class="waves-effect waves-light btn-large"><i class="material-icons right">clear</i>Clear</a>
						<a class="waves-effect waves-light btn-large"><i class="material-icons right">send</i>Filter</a>
					</div>
			</div>
		</li>
	</ul>
</div>
<!-- FILTERING END -->

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
