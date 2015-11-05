<?php
	if(!isset($_GET['pages'])){
		$_GET['pages'] = 1;
	}
	$perPages = 13;
	$getInbox = mysql_query("SELECT * FROM inbox");
	$totCont = mysql_num_rows($getInbox);
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
		$prevPage = $_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$prevCurPages."&lastID=".$lastIdMsg;
	}else{
		$prevPage = '';
	}
	if ($curPages < $tpages) {
		$nextCurPages = $curPages+1;
		$nextPage = $_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$nextCurPages."&lastID=".$lastIdMsg;
	}else{
		$nextPage = '';
	}
?>
<div class="row">
	<div class="col s5">
		<h3>Inbox</h3>
	</div>
	<div class="col s7" style="margin-top:30px">
		<a class="btn-floating btn-large waves-effect waves-light blue lighten-2 right" href="javascript:history.go(0)"><i class="material-icons">replay</i></a>
    </div>
    <!-- FILTERING
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
							<label class="active" for="sender">sender</label>
							<input placeholder="name/phone number" id ="sender" type="text" class="validate">							
						</div>
						<div class="col s3">
							<a class="waves-effect waves-light btn-large"><i class="material-icons right">send</i>Filter</a>
						</div>
					</form>
				</div>
			</li>
		</ul>
	</div>
	-->
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr><th width="200" data-field="time">Time</th><th width="300" data-field="sender">Sender</th><th width="350" data-field="case">Customer Case</th><th width="700" data-field="message">Message</th><th data-field="action"></th></tr>
			</thead>
			<tbody>
				<?php
				if(isset($_POST['inboxid']) || isset($_POST['customerid'])){
					$postNumber=$_POST['number'];
				    $postMsg=$_POST['message'];
				    $postname=$_POST['name'];
				    $postcase=$_POST['case'];
				    $user=$_SESSION['user'];
					$IDinbox = $_POST['inboxid'];
					$IDCust = $_POST['customerid'];


					$delquery = "DELETE FROM inbox WHERE ID = '".$IDinbox."'";
					if (mysql_query($delquery)) {
						if(isset($_POST['customerid'])){
						$delquerycust = "DELETE FROM customer WHERE idCust = '".$IDCust."'";
							if (!mysql_query($delquerycust)) {
						        echo "Error: ".mysql_error($conn);
						    }
						}
						$logging = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message)
									VALUES ('".$user."', 'Deleting Inbox', now(), '".$IDinbox."', '".$postNumber."', '".$postname."', '".$postcase."', '".$postMsg."')";
						mysql_query($logging);
				    }else{
				    	echo "Error: ".mysql_error($conn);
				    }
				}
				$inboxItemPerPages = mysql_query("SELECT * ,
									replace(replace(SenderNumber,'+62','0'), '+628', '08') as number, 
									DATE_FORMAT(ReceivingDateTime, '%e %b %Y - %k:%i') as date 
									FROM inbox 
									ORDER BY ReceivingDateTime DESC LIMIT ".$perPages." OFFSET ".$start." ");
								

				if($inboxItemPerPages && mysql_num_rows($inboxItemPerPages) > 0 && isset($_SESSION['priv'])) {
					while($msg = mysql_fetch_array($inboxItemPerPages)) {
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
							$urlToThread = "index.php?menu=thread&cat=detail&number=".$msg['number']."&lastID=".$lastIdMsg;
				?>
						<tr class="clickable-row" data-href="<?php echo $urlToThread;?>">
							<td style='word-wrap:break-word'>
								<?php echo  $msg['date'];?>
							</td>
							<td>
								<?php echo $name;?>
							</td>
							<td style='word-wrap:break-word'>
								<?php echo $case;?>
							</td>
							<td style='word-wrap:break-word; widht:200px;'>
								<?php echo $msg['TextDecoded'];?>
							</td>						
							<td style="vertical-align:middle;">
								<form class="" method="POST" action="">
									<input name="number" type="hidden" value="<?php echo $msg['number'];?>">
									<input name="message" type="hidden" value="<?php echo $msg['TextDecoded'];?>">
									<input name="name" type="hidden" value="<?php echo $nametodel;?>">
									<input name="case" type="hidden" value="<?php echo $case;?>">
									<input name="inboxid" type="hidden" value="<?php echo $msg['ID'];?>">
									<input name="customerid" type="hidden" value="<?php echo $custid;?>">
						      		<button class="valign btn-floating btn-small waves-effect waves-light red lighten-2" type="submit" name="submit"><i class="material-icons">delete</i></button>
						      	</form>
						    </td></tr>
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
		$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=cekinbox&pages=1&lastID=".$lastIdMsg."'>1 ... </a></li>";
		$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$totPages."&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";

		if($curPages <= 0 || $curPages > $totPages){
			header('Location: ./?menu=cekinbox&pages=1&lastID='.$lastIdMsg);
		}elseif ($curPages >= 1 && $curPages <= 5) {
			$liFirstPage = "";
			$firstPosPage = 1;
			$lastPosPage = 10;
			$liLastPage = $lastPage;
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
			header('Location: ./?menu=cekinbox&pages=1&lastID='.$lastIdMsg);
		}
		
		echo $liFirstPage;

		for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$j."&lastID=".$lastIdMsg."'>".$j."</a></li>";
		}

		echo $liLastPage;

		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			</ul>
		</div>
	</div>
</div>
