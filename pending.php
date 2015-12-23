<?php
if(!isset($_GET['pages'])){
	$_GET['pages'] = 1;
}
$userpriv = $_SESSION['priv'];
$perPages = 20;
if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
$getPendingitems = mysql_query("SELECT * FROM outbox");
}else{
$getPendingitems = mysql_query("SELECT * FROM outbox WHERE CreatorID NOT LIKE '%admin'");
}

$totCont = mysql_num_rows($getPendingitems);
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
	$prevCurPages = $curPages-1;
	$prevPage = $_SERVER['PHP_SELF']."?menu=pending&pages=".$prevCurPages."&lastID=".$lastIdMsg;
}else{
	$prevPage = '';
}
if ($curPages < $tpages) {
	$nextCurPages = $curPages+1;
	$nextPage = $_SERVER['PHP_SELF']."?menu=pending&pages=".$nextCurPages."&lastID=".$lastIdMsg;
}else{
	$nextPage = '';
}
?>
<div class="row">
	<div class="col s8">
		<h3>Pending Items</h3>
	</div>
	<div class="col s4">
      <a class="btn-floating btn-large waves-effect waves-light blue blue lighten-2 right" href="javascript:history.go(0)" style="margin-top:30px"><i class="material-icons">replay</i></a>
    </div>
    <?php
    if(mysql_num_rows($getPendingitems) == 0){
   	?>
	<div class="col s12">
		<div class="center">
			<h5>No Pending Items :)</h5>
		</div>
	</div>
   	<?php
    }else{
    ?>
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr><th width="200px" data-field="time">Time</th><th width="250px" data-field="sender">Recipients</th><th width="250px" data-field="case">Customer Case</th><th data-field="message">Message</th><th width="100px" data-field="status">Status</th><th width="100" data-field="author">Author</th><th width="50" data-field="action"></th></tr>
			</thead>
			<tbody>
				<?php
					if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
					$outboxPerPages = mysql_query("SELECT *,
										replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
										DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
										CreatorID
										FROM outbox
										ORDER BY SendingDateTime 
										DESC LIMIT ".$perPages." 
										OFFSET ".$start." ");
					} else {
						$outboxPerPages = mysql_query("SELECT *,
										replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
										DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
										CreatorID
										FROM outbox 
										WHERE CreatorID NOT LIKE '%admin' 
										ORDER BY SendingDateTime 
										DESC LIMIT ".$perPages." 
										OFFSET ".$start." ");
					}

					if($outboxPerPages && mysql_num_rows($outboxPerPages) > 0 && isset($_SESSION['priv'])) {
						while($msg = mysql_fetch_array($outboxPerPages)) {
						$status='Pending';
						$color='blue';
						$query = mysql_query("SELECT *,replace(replace(phone,'+62','0'), '+628', '08') as number FROM customer WHERE phone = '".$msg["number"]."'");
						if($query && mysql_num_rows($query)){
						while($row = mysql_fetch_array($query)){
								$name = $row['name']." - (".$msg['number'].")";
								$nametodel = $row['name'];
								$case = $row['hal'];
								$custid = $row['idCust'];
							}
						}else{
								$name = $msg['number'];
								$case = "-";
								$custid = NULL;
								$nametodel = NULL;

						}
							?>
								<tr>
									<td style='word-wrap:break-word'><?php echo $msg['date']; ?></td>
									<td>
										<?php echo $name; ?></br>
										<a href="https://crm.zoho.com/crm/GlobalSearch1.do?sModules=AllEntities&searchword=<?php echo $msg['number'] ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">CRM</a>
										<a href="https://support.zoho.com/support/cermati/ShowHomePage.do#Cases/search/CurDep/<?php echo $msg['number'] ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">SUPPORT</a>
									</td>
									<td style='word-wrap:break-word'><?php echo $case; ?></td>
									<td style='word-wrap:break-word'><?php echo $msg['TextDecoded']; if($msg['MultiPart'] == 'true'){echo "...";} else{ echo "";} ?></td>
									<td class='".$color."-text'><?php echo $status; ?></td>
									<td><?php echo $msg['CreatorID']; ?></td>
								</tr>
							<?php
						}
					}
				?>
			<tbody>
		</table>
	</div>
	<div class="col s12">
		<div class="center">
			<ul class="pagination">
				<li class="waves-effect <?php echo $dissleft; ?>" <?php echo $dissleft; ?>><a href="<?php echo $prevPage; ?>" class="<?php echo $dissleft; ?>"><i class="material-icons">chevron_left</i></a></li>
		<?php
		$almostLast = $totPages-4;
		$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=pending&pages=1&lastID=".$lastIdMsg."'>1 ... </a></li>";
		$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=pending&pages=".$totPages."&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";

		if($curPages <= 0 || $curPages > $totPages){
			header('Location: ./?menu=pending&pages=1&lastID='.$lastIdMsg);
		}elseif ($curPages >= 1 && $curPages <= 5) {
			$liFirstPage = "";
			$firstPosPage = 1;
			if($totPages < 10){
				$lastPosPage = $totPages;
				$liLastPage = "";
			}else{
				$lastPosPage = 10;
				$liLastPage = $lastPage;
			}
			
		}elseif ($curPages > 5 && $curPages < $almostLast) {
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
			header('Location: ./?menu=pending&pages=1&lastID='.$lastIdMsg);
		}
		
		echo $liFirstPage;

		for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=pending&pages=".$j."&lastID=".$lastIdMsg."'>".$j."</a></li>";
		}

		echo $liLastPage;
		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			  </ul>
		</div>
	</div>
</div>
<?php
}
?>
