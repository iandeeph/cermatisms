<?php
if(!isset($_GET['pages'])){
	$_GET['pages'] = 1;
}

if(isset($_SESSION['labelDateFilter'])){
	$labelDateFilter 	= $_SESSION['labelDateFilter'];
	$datefrom 			= $_SESSION['postDateFrom'];
	$dateTo 			= $_SESSION['postDateTo'];
}

	$whereFilter 		= $_SESSION['filterReport'];

$_SESSION['reportUser'] = $_GET['user'];
$reportUser = $_SESSION['reportUser'];

$perPages = 13;
$getSentitems = mysql_query("SELECT * FROM sentitems WHERE ".$whereFilter."AND (CreatorID = '".$reportUser."')");

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
	$prevPage = $_SERVER['PHP_SELF']."?menu=report&view=on&pages=".$prevCurPages."&user=".$reportUser."&lastID=".$lastIdMsg;
}else{
	$prevPage = '';
}
if ($curPages < $tpages) {
	$nextCurPages = $curPages+1;
	$nextPage = $_SERVER['PHP_SELF']."?menu=report&view=on&pages=".$nextCurPages."&user=".$reportUser."&lastID=".$lastIdMsg;
}else{
	$nextPage = '';
}
?>
<div class="row">
	<div class="col s8">
		<h3>Sent Items Report</h3>
	</div>
	<div class="col s4">
      <a class="btn-floating btn-large waves-effect waves-light blue blue lighten-2 right" href="javascript:history.go(0)" style="margin-top:30px"><i class="material-icons">replay</i></a>
    </div>
</div>
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
	<div class="col s4">
		<div class="card-panel teal">
			<span class="white-text">
				<?php
					echo "Author Filter : <b>".$reportUser."</b>";;
				?>
			</span>
		</div>
	</div>
</div>
<div class="row">
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr><th width="200" data-field="time">Time</th><th width="300" data-field="sender">Recipients</th><th width="350" data-field="case">Customer Case</th><th data-field="message">Message</th><th width="150" data-field="status">Status</th><th width="150" data-field="author">Author</th><th width="50" data-field="action"></th></tr>
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
					$sentItemPerPages = mysql_query("SELECT *,
										replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
										DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
										CreatorID
										FROM sentitems
										WHERE ".$whereFilter."AND (CreatorID = '".$reportUser."')
										ORDER BY SendingDateTime 
										DESC LIMIT ".$perPages." 
										OFFSET ".$start." ");

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
								<td style='word-wrap:break-word'><?php echo $case; ?></td>
								<td style='word-wrap:break-word'><?php echo $msg['TextDecoded']; ?></td>
								<td class='".$color."-text'><?php echo $status; ?></td>
								<td><?php echo $msg['CreatorID']; ?></td>
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
		$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=report&view=on&pages=1&user=".$reportUser."&lastID=".$lastIdMsg."'>1 ... </a></li>";
		$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=report&view=on&pages=".$totPages."&user=".$reportUser."&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";

		if($curPages <= 0 || $curPages > $totPages){
			header('Location: ./?menu=report&view=on&pages=1&user=".$reportUser."&lastID='.$lastIdMsg);
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
			header('Location: ./?menu=report&view=on&pages=1&user=".$reportUser."&lastID='.$lastIdMsg);
		}
		
		echo $liFirstPage;

		for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=report&view=on&pages=".$j."&user=".$reportUser."&lastID=".$lastIdMsg."'>".$j."</a></li>";
		}

		echo $liLastPage;
		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			  </ul>
		</div>
	</div>
</div>