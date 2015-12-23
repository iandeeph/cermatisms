<?php
ifSubmitInboxFilter($oldestDate, $newestDate);

if(isset($_SESSION['labelDateFilter'])){
	$labelDateFilter 	= $_SESSION['labelDateFilter'];
	$datefrom 			= $_SESSION['postDateFrom'];
	$dateTo 			= $_SESSION['postDateTo'];
}
	$whereFilter 		= $_SESSION['whereFilter'];

if(isset($_SESSION['labelSenderFilter'])){
	$labelSenderFilter 	= $_SESSION['labelSenderFilter'];
	$postSender 		= $_SESSION['postSender'];
}else{
	$postSender			= "";
}

if(isset($_SESSION['labelCaseFilter'])){
	$labelCaseFilter 	= $_SESSION['labelCaseFilter'];
	$postCase 			= $_SESSION['postCase'];
}else{
	$postCase			= "";
}

if(isset($_SESSION['labelMsgFilter'])){
	$labelMsgFilter 	= $_SESSION['labelMsgFilter'];
	$postMessage 		= $_SESSION['postMessage'];
}else{
	$postMessage		= "";
}

$qryInbox = "SELECT *
		FROM inbox
		WHERE ".$whereFilter."";

$getInbox = mysql_query($qryInbox);

$totCont = mysql_num_rows($getInbox);
if($totCont >= 15){
	$perPages = 15;
	$totPages = ceil($totCont/$perPages);
}elseif($totCont > 0){
	$perPages = $totCont;
	$totPages = ceil($totCont/$perPages);
}elseif($totCont == 0){
	$perPages = $totCont;
	$totPages = 1;
}

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

$qryInbox = "SELECT ID,
				DATE_FORMAT(ReceivingDateTime, '%e %b %Y - %k:%i') as date, 
				replace(replace(SenderNumber,'+62','0'), '+628', '08') as number,
				TextDecoded
				FROM inbox 
				WHERE ".$whereFilter."
				ORDER BY ReceivingDateTime DESC LIMIT ".$perPages." OFFSET ".$start." ";

$inboxItemPerPages = mysql_query($qryInbox);
?>
<div class="row">
	<div class="col s5">
		<h3>Inbox</h3>
	</div>
	<div class="col s7" style="margin-top:30px">
		<a class="btn-floating btn-large waves-effect waves-light blue lighten-2 right" href="javascript:history.go(0)"><i class="material-icons">replay</i></a>
    </div>
    <!-- FILTERING -->
    <div class="col s12">
		<ul class="collapsible" data-collapsible="accordion">
			<li>
				<div class="collapsible-header"><i class="material-icons">search</i>Filter</div>
				<div class="col s12 collapsible-body" style="margin-top:30px;">
					<form  id="filterInbox" action="" method="POST">
						<div class="col s3">
							<label  class="active" for="datefrom">Date From</label>
							<input value="<?php echo date('j F, Y', $datefrom);?>" name="datefrom" id="datefrom" type="date" class="datepicker">
						</div>
						<div class="col s3">
							<label class="active" for="dateto">Date To</label>
							<input value="<?php echo date('j F, Y', $dateTo);?>" name="dateto" id="dateto" type="date" class="datepicker">
						</div>
						<div class="col s3">
							<label class="active" for="sender">Sender</label>
							<input value="<?php echo $postSender;?>" name="sender" placeholder="Name/Phone number.. (Leave blank for any number..)" id ="sender" type="text" class="validate">				
						</div>
						<div class="col s3">
							<label class="active" for="caseFilter">Cust Case</label>
							<input value="<?php echo $postCase;?>" name="caseFilter"  id ="caseFilter" type="text" class="validate">							
						</div>
						<div class="col s12">
							<label class="active" for="messageFilter">Message</label>
							<input value="<?php echo $postMessage;?>" name="messageFilter" id="messageFilter" type="text" class="validate">							
						</div>
						<div class="col s12" style="margin-bottom:15px">
							<a id="resetButton" class="waves-effect waves-light btn-large"><i class="material-icons right">clear</i>Clear</a>
							<button class="waves-effect waves-light btn-large" name="filterInboxSumbit"><i class="material-icons right">send</i>Filter</button>
						</div>
					</form>
				</div>
			</li>
		</ul>
	</div>
	<!-- FILTERING END -->
	<div class="row">
		<div class="col s4">
			<div class="card-panel teal">
				<span class="white-text">
					<?php
						echo $labelDateFilter;
					?>
				</span>
			</div>
		</div>
	<?php
		if(isset($_SESSION['labelSenderFilter'])){
	?>
		<div class="col s4">
			<div class="card-panel teal">
				<span class="white-text">
					<?php
						echo $labelSenderFilter;
					?>
				</span>
			</div>
		</div>
	<?php
		}
		if(isset($_SESSION['labelCaseFilter'])){
	?>
		<div class="col s4">
			<div class="card-panel teal">
				<span class="white-text">
					<?php
						echo $labelCaseFilter;
					?>
				</span>
			</div>
		</div>
	<?php
		}
		if(isset($_SESSION['labelMsgFilter'])){
	?>
		<div class="col s4">
			<div class="card-panel teal">
				<span class="white-text">
					<?php
						echo $labelMsgFilter;
					?>
				</span>
			</div>
		</div>
	<?php
		}
	?>
	</div>
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
					    $postcase=$_POST['hal'];
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
					while($msg = mysql_fetch_array($inboxItemPerPages)){
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
						<tr>
							<td style='word-wrap:break-word'>
								<?php echo  $msg['date'];?>
								<a href="<?php echo $urlToThread; ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">THREAD</a>
							</td>
							<td>
								<?php echo $name;?></br>
								<a href="https://crm.zoho.com/crm/GlobalSearch1.do?sModules=AllEntities&searchword=<?php echo $msg['number']; ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">CRM</a>
								<a href="https://support.zoho.com/support/cermati/ShowHomePage.do#Cases/search/CurDep/<?php echo $msg['number']; ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">SUPPORT</a>
							</td>
							<td style='word-wrap:break-word'>
								<?php echo $case ;?>
							</td>
							<td style='word-wrap:break-word;'>
								<?php echo $msg['TextDecoded'];?>
							</td>						
							<td style="vertical-align:middle;">
								<form class="" method="POST" action="">
									<input name="number" type="hidden" value="<?php echo $msg['number'];?>">
									<input name="message" type="hidden" value="<?php echo $msg['TextDecoded'];?>">
									<input name="name" type="hidden" value="<?php echo $name;?>">
									<input name="case" type="hidden" value="<?php echo $case;?>">
									<input name="inboxid" type="hidden" value="<?php echo $msg['ID'];?>">
									<input name="customerid" type="hidden" value="<?php echo $custid;?>">
						      		<button class="valign btn-floating btn-small waves-effect waves-light red lighten-2" type="submit" name="submit"><i class="material-icons">delete</i></button>
						      	</form>
						    </td>
						</tr>
				<?php
					}
				?>
			<tbody>
		</table>
	</div>
	<div class="col s12">
		<div class="center">
			<ul class="pagination">
				<?php
					if($page<=0)$page=1;

					if($curPages <= 1){
						$dissleft = "disabled";
					}else{$dissleft="";}
					if($curPages >= $tpages){
						$dissright = "disabled";
					}else{$dissright="";}

					if ($curPages > 1) {
						$prevCurPages = $curPages-1;
						$prevPage = $_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$prevCurPages."&filter=on&lastID=".$lastIdMsg;
					}else{
						$prevPage = '';
					}
					if ($curPages < $tpages) {
						$nextCurPages = $curPages+1;
						$nextPage = $_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$nextCurPages."&filter=on&lastID=".$lastIdMsg;
					}else{
						$nextPage = '';
					}

					$almostLast = $totPages-4;
					$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=cekinbox&pages=1&filter=on&lastID=".$lastIdMsg."'>1 ... </a></li>";
					$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$totPages."&filter=on&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";
				?>
				<li class="waves-effect <?php echo $dissleft; ?>" <?php echo $dissleft; ?>><a href="<?php echo $prevPage; ?>" class="<?php echo $dissleft; ?>"><i class="material-icons">chevron_left</i></a></li>
					
				<?php
					if($curPages <= 0 || $curPages > $totPages){
						header('Location: ./?menu=cekinbox&pages=1&filter=on&lastID='.$lastIdMsg);
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
						header('Location: ./?menu=cekinbox&pages=1&filter=on&lastID='.$lastIdMsg);
					}
					
					echo $liFirstPage;

					for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
						if ($curPages == $j) {
							$active = 'active';
						}else{$active="";}
						echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=cekinbox&pages=".$j."&filter=on&lastID=".$lastIdMsg."'>".$j."</a></li>";
					}

					echo $liLastPage;

					?>

				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			</ul>
		</div>
	</div>
</div>