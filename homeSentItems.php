<?php
if(!isset($_GET['pages'])){
	$_GET['pages'] = 1;
}
$userpriv = $_SESSION['priv'];
$perPages = 13;
if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
	$getSentitems = mysql_query("SELECT * FROM sentitems");
}else{
	$getSentitems = mysql_query("SELECT * FROM sentitems WHERE CreatorID NOT LIKE '%admin'");
}
$totCont = mysql_num_rows($getSentitems);
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
	$prevPage = $_SERVER['PHP_SELF']."?menu=sentitem&pages=".$prevCurPages."&lastID=".$lastIdMsg;
}else{
	$prevPage = '';
}
if ($curPages < $tpages) {
	$nextCurPages = $curPages+1;
	$nextPage = $_SERVER['PHP_SELF']."?menu=sentitem&pages=".$nextCurPages."&lastID=".$lastIdMsg;
}else{
	$nextPage = '';
}
ifSubmitSentFilter($oldestDate, $newestDate);
?>
<div class="row">
	<div class="col s8">
		<h3>Sent Items</h3>
	</div>
	<div class="col s4">
      <a class="btn-floating btn-large waves-effect waves-light blue blue lighten-2 right" href="javascript:history.go(0)" style="margin-top:30px"><i class="material-icons">replay</i></a>
    </div>
<?php
if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
$getPendingitems = mysql_query("SELECT * FROM outbox");
}else{
$getPendingitems = mysql_query("SELECT * FROM outbox WHERE CreatorID NOT LIKE '%admin'");
}

$totCont = mysql_num_rows($getPendingitems);

if($totCont == 0) {
	$fontColor = 'green';
}else{
	$fontColor = 'red';
}
?>
	<div class="col s12">
		<a class='<?php echo $fontColor;?>-text' href='index.php?menu=pending&pages=1&lastID=<?php echo $lastIdMsg; ?>'>[ <?php echo $totCont;?> ] sms pending</a>
	</div>
	<!-- FILTERING -->
    <div class="col s12">
		<ul class="collapsible" data-collapsible="accordion">
			<li>
				<div class="collapsible-header"><i class="material-icons">search</i>Filter</div>
				<div class="col s12 collapsible-body" style="margin-top:30px;">
					<form action="index.php?menu=sentitem&pages=1&filter=on&lastID=<?php echo $lastIdMsg; ?>" method="POST" name="filterSentitems">
						<div class="col s3">
							<label class="active" for="datefrom">Date From</label>
							<input name="datefrom" id="datefrom" type="date" class="datepicker">
						</div>
						<div class="col s3">
							<label class="active" for="dateto">Date To</label>
							<input name="dateto" id="dateto" type="date" class="datepicker">
						</div>
						<div class="input-field col s3" style="margin-bottom:20px">
							<select name="statusSentFilter" id="statusSentFilter">
								<option value="" disabled selected>Status</option>
								<option value="All">All</option>
								<option value="Sent">Sent</option>
								<option value="Failed">Failed</option>
							</select>
							<label>Select Status</label>
						</div>
						<div class="input-field col s3" style="margin-bottom:20px">
							<select name="authorSentFilter" id="authorSentFilter">
								<option value="" disabled selected>Author</option>
								<option value="All">All</option>
							<?php
								$qryUser = mysql_query("SELECT username FROM user");
								while($rowUser = mysql_fetch_array($qryUser)){
									echo "<option value=".$rowUser['username'].">".$rowUser['username']."</option>";
								}
							?>
							</select>
							<label>Select Author</label>
						</div>
						<div class="col s6">
							<label class="active" for="receipentSentFilter">Receipents</label>
							<input name="receipentSentFilter" placeholder="Name/Phone number.. (Leave blank for any number..)" id ="receipentSentFilter" type="text" class="validate">							
						</div>
						<div class="col s6">
							<label class="active" for="caseSentFilter">Cust Case</label>
							<input name="caseSentFilter" id ="caseSentFilter" type="text" class="validate">							
						</div>
						<div class="col s12">
							<label class="active" for="messageSentFilter">Message</label>
							<input name="messageSentFilter" id ="messageSentFilter" type="text" class="validate">							
						</div>
						<div class="col s12" style="margin-bottom:15px">
							<a id="resetButton" class="waves-effect waves-light btn-large"><i class="material-icons right">clear</i>Clear</a>
							<button name="filterSentSumbit" class="waves-effect waves-light btn-large"><i class="material-icons right">send</i>Filter</button>
						</div>
					</form>
				</div>
			</li>
		</ul>
	</div>
</div>
	<!-- FILTERING END -->
<div class="row">
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr><th width="150px" data-field="time">Time</th><th width="200px" data-field="sender">Recipients</th><th width="200px" data-field="case">Customer Case</th><th data-field="message">Message</th><th width="100px" data-field="status">Status</th><th width="100" data-field="author">Author</th><th width="50" data-field="action"></th></tr>
			</thead>
			<tbody>
				<?php
				if(isset($_POST['sentid']) || isset($_POST['customerid'])){
					$postNumber=$_POST['number'];
				    $postMsg=$_POST['message'];
				    $postname=$_POST['name'];
				    $postcase=$_POST['case'];
				    $user=$_SESSION['user'];
					$IDsent = $_POST['sentid'];
					$IDCust = $_POST['customerid'];


					$delquery = "DELETE FROM sentitems WHERE ID = '".$IDsent."'";
					if (mysql_query($delquery)) {
						if(isset($_POST['customerid'])){
						$delquerycust = "DELETE FROM customer WHERE idCust = '".$IDCust."'";
							if (!mysql_query($delquerycust)) {
						        echo "Error: ".mysql_error($conn);
						    }
						}
						$logging = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message)
									VALUES ('".$user."', 'Deleting Sent Items', now(), '".$IDsent."', '".$postNumber."', '".$postname."', '".$postcase."', '".$postMsg."')";
						mysql_query($logging);
				    }else{
				    	echo "Error: ".mysql_error($conn);
				    }
				}
					if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
					$sentItemPerPages = mysql_query("SELECT *,
										replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
										DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
										CreatorID
										FROM sentitems
										ORDER BY SendingDateTime 
										DESC LIMIT ".$perPages." 
										OFFSET ".$start." ");
					} else {
						$sentItemPerPages = mysql_query("SELECT *,
										replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
										DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
										CreatorID
										FROM sentitems 
										WHERE CreatorID NOT LIKE '%admin' 
										ORDER BY SendingDateTime 
										DESC LIMIT ".$perPages." 
										OFFSET ".$start." ");
					}
					$numModal = 1;
					if($sentItemPerPages && mysql_num_rows($sentItemPerPages) > 0 && isset($_SESSION['priv'])) {
						while($msg = mysql_fetch_array($sentItemPerPages)) {
							switch ($msg['Status']){
								case 'SendingOK': $status='Sent'; $color='green'; break;
								case 'SendingOKNoReport': $status='Sent'; $color='green'; break;
								case 'SendingError': $status='Failed'; $color='red'; break;
								case 'DeliveryOK': $status='Sent'; $color='green'; break;
								case 'DeliveryFailed': $status='Failed'; $color='red'; break;
								case 'DeliveryPending': $status='Pending'; $color='yellow'; break;
								case 'DeliveryUnknown': $status='Failed'; $color='red'; break;
								case 'Error': $status='Failed'; $color='red'; break;
								default : $status='Failed'; $color='red'; break;
							}
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
									<?php echo $msg['date']?>
									<a href="<?php echo $urlToThread; ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">THREAD</a>
								</td>
								<td>
									<?php echo $name; ?></br>
									<a href="https://crm.zoho.com/crm/GlobalSearch1.do?sModules=AllEntities&searchword=<?php echo $msg['number'] ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">CRM</a>
									<a href="https://support.zoho.com/support/cermati/ShowHomePage.do#Cases/search/CurDep/<?php echo $msg['number'] ?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">SUPPORT</a>
								</td>
								<td style='word-wrap:break-word;'><?php echo $case; ?></td>
								<td style='word-wrap:break-word'><?php echo $msg['TextDecoded']; ?></td>
								<td class='<?php echo $color."-text";?>'><?php echo $status; ?></td>
								<td><?php echo $msg['CreatorID']; ?></td>
								<td style="vertical-align:middle;">
							      	<a title="resend sms..." href="<?php echo "#modal".$numModal;?>" class="modal-trigger valign btn-floating btn-small waves-effect waves-light blue lighten-2" name="resend"><i class="material-icons">repeat</i></a>
							    </td>
							    <!-- Modal Structure -->
							    <?php
							    $qryLogForResend = mysql_query("SELECT *, replace(replace(phone,'+62','0'), '+628', '08') as number, DATE_FORMAT(date, '%e %b %Y  %k:%i') as time FROM log WHERE (message LIKE '%".$msg['TextDecoded']."%' AND phone = '".$msg['number']."')");
							    $rowResend = mysql_fetch_array($qryLogForResend);
							    ?>
								<div id="<?php echo "modal".$numModal;?>" class="modal modal-fixed-footer">
									<form class="col s10" method="POST" action="" style="padding-bottom:50px; padding-top:10px;">
										<div class="modal-content">
											<h4>Resend Message</h4>
											<div class="input-field col s8 ">
												<i class="material-icons prefix">account_circle</i>
												<input value="<?php echo $rowResend['name']; ?>" id="resendCustName" type="text" class="validate" name="resendCustName" required >
												<label for="resendCustName">Customer Name</label>
											</div>
											<div class="input-field col s6 ">
												<i class="material-icons prefix">contact_phone</i>
												<input value="<?php echo $rowResend['number']; ?>" id="resendCustPhone" type="tel" class="validate" name="resendCustPhone" pattern="^0[0-9]{9,12}|^\(?\+62[0-9]{9,12}" required >
												<label for="resendCustPhone">Phone Number</label>
											</div>
											<div class="input-field col s12 ">
												<i class="material-icons prefix">work</i>
												<input value="<?php echo $rowResend['hal']; ?>" id="resendCustCase" type="text" class="validate" name="resendCustCase" required >
												<label for="resendCustCase">Customer Case</label>
											</div>
											<div class="input-field col s12">
												<i class="material-icons prefix">mode_edit</i>
												<textarea id="resendCustMessage" class="materialize-textarea" length="160" name="resendCustMessage" required><?php echo $rowResend['message']; ?></textarea>
												<label for="resendCustMessage">Your Message</label>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="modal-action modal-close waves-effect waves-green btn-flat blue lighten-2" name="<?php echo "resendButton".$numModal;?>"><i class="material-icons right">send</i>Resend</button>
											<a type="cancel" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
										</div>
										<?php
											if(isset($_POST['resendButton'.$numModal])) {
												$postNumber=$_POST['resendCustPhone'];
												$postMsg=$_POST['resendCustMessage'];
												$postname=$_POST['resendCustName'];
												$postcase=$_POST['resendCustCase'];
												$user=$_SESSION['user'];
												sendSms($postNumber, $postMsg, $postname, $postcase, $user);
											}
										?>
									</form>
								</div>
								<td style="vertical-align:middle;">
									<form class="" method="POST" action="">
										<input name="number" type="hidden" value="<?php echo $msg['number'];?>">
										<input name="message" type="hidden" value="<?php echo $msg['TextDecoded'];?>">
										<input name="name" type="hidden" value="<?php echo $nametodel;?>">
										<input name="case" type="hidden" value="<?php echo $case;?>">
										<input name="sentid" type="hidden" value="<?php echo $msg['ID'];?>">
										<input name="customerid" type="hidden" value="<?php echo $custid;?>">
							      		<button class="valign btn-floating btn-small waves-effect waves-light red lighten-2" type="submit" name="submit"><i class="material-icons">delete</i></button>
							      	</form>
							    </td>
							</tr>
						<?php
						$numModal++;
					}
				}
				?>
			<tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col s12">
		<div class="center">
			<ul class="pagination">
				<li class="waves-effect <?php echo $dissleft; ?>" <?php echo $dissleft; ?>><a href="<?php echo $prevPage; ?>" class="<?php echo $dissleft; ?>"><i class="material-icons">chevron_left</i></a></li>
		<?php
		$almostLast = $totPages-4;
		$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=sentitem&pages=1&lastID=".$lastIdMsg."'>1 ... </a></li>";
		$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=sentitem&pages=".$totPages."&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";

		if($curPages <= 0 || $curPages > $totPages){
			header('Location: ./?menu=sentitem&pages=1&lastID='.$lastIdMsg);
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
			header('Location: ./?menu=sentitem&pages=1&lastID='.$lastIdMsg);
		}
		
		echo $liFirstPage;

		for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=sentitem&pages=".$j."&lastID=".$lastIdMsg."'>".$j."</a></li>";
		}

		echo $liLastPage;
		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			  </ul>
		</div>
	</div>
</div>