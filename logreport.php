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
		$prevPage = $_SERVER['PHP_SELF']."?menu=logreport&pages=".$prevCurPages."&lastID=".$lastIdMsg;
	}else{
		$prevPage = '';
	}
	if ($curPages < $tpages) {
		$nextCurPages = $curPages+1;
		$nextPage = $_SERVER['PHP_SELF']."?menu=logreport&pages=".$nextCurPages."&lastID=".$lastIdMsg;
	}else{
		$nextPage = '';
	}
?>
<div class="row">
	<div class="col s8">
		<h3>Log Report</h3>
	</div>
	<div class="col s4">
      <a class="btn-floating btn-large waves-effect waves-light blue blue lighten-2 right" href="javascript:history.go(0)" style="margin-top:30px"><i class="material-icons">replay</i></a>
    </div>

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
						<div class="input-field col s3" style="margin-bottom:5px">
							<select>
								<option value="" disabled selected>Action</option>
								<option value="1">Sending</option>
								<option value="2">Sending Multiple SMS</option>
								<option value="3">Sending Blasting SMS</option>
								<option value="4">Delete Message</option>
								<option value="5">Inbox Simulation</option>
							</select>
							<label>Select Action</label>
						</div>
						<div class="input-field col s3" style="margin-bottom:5px">
							<select>
								<option value="" disabled selected>User</option>
							<?php
								$numUser = 1;
								$qryUser = mysql_query("SELECT username FROM user");
								while($rowUser = mysql_fetch_array($qryUser)){
									echo "<option value=".$numUser.">".$rowUser['username']."</option>";
									$numUser++;
								}
							?>
							</select>
							<label>Select User</label>
						</div>
						<div class="col s6">
							<label class="active" for="receipent">Receipents</label>
							<input placeholder="Name/Phone number.. (Leave blank for any number..)" id ="receipent" type="text" class="validate">							
						</div>
						<div class="col s6">
							<label class="active" for="case">Case</label>
							<input id ="case" type="text" class="validate">							
						</div>
						<div class="col s12">
							<label class="active" for="message">Message</label>
							<input id ="message" type="text" class="validate">							
						</div>
						<div class="col s12" style="margin-bottom:15px">
							<a class="waves-effect waves-light btn-large"><i class="material-icons right">clear</i>Clear</a>
							<a class="waves-effect waves-light btn-large"><i class="material-icons right">send</i>Filter</a>
						</div>
					</form>
				</div>
			</li>
		</ul>
	</div>
	<!-- FILTERING END -->
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr>
					<th width="100" data-field="time">Time</th>
					<th width="50" data-field="user">User</th>
					<th width="150" data-field="action">Action</th>
					<th width="100" data-field="number">Number</th>
					<th width="150" data-field="name">Name</th>
					<th width="100" data-field="case">Case</th>
					<th width="50" data-field="id">ID Message</th>
					<th data-field="message">Message</th>
					<th width="50" data-field="delete"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$logquery = "SELECT *, replace(replace(phone,'+62','0'), '+628', '08') as number, DATE_FORMAT(date, '%e %b %Y  %k:%i') as time FROM log ORDER BY date DESC LIMIT ".$perPages." OFFSET ".$start."";
				$takelog = mysql_query($logquery);
		    	if($takelog && mysql_num_rows($takelog) > 0){
			    	while ($log = mysql_fetch_array($takelog)){
			    		$logid = $log['idLog'];
			    		$user = $log['user'];
			    		$action = $log['action'] ;
			    		$date = $log['time'];
			    		$ID = $log['messageID'];
			    		$phone = $log['number'];
			    		$name = $log['name'];
			    		$case = $log['hal'];
			    		$message = $log['message'];

			    		switch ($action){
			    			case 'Sending Message': $color='blue accent-2'; break;
			    			case 'Reply From Thread': $color='blue accent-4'; break;
			    			case 'Deleting Inbox': $color='orange darken-3'; break;
			    			case 'Deleting Sent Items': $color='orange darken-3'; break;
			    			case 'Admin Doing Incoming Inbox Simulation': $color='green darken-3'; break;
			    			default : $color='blue accent-2'; break;
			    		}
			    	if(isset($_POST['idlog'])){
					$delquery = "DELETE FROM log WHERE idLog = '".$_POST['idlog']."'";
					if (!mysql_query($delquery)) {
			          echo "Error: ".$delquery. " ".mysql_error($conn);
			          }
			        }
			    ?>
			        <tr>
			          	<td><?php echo $date;?></td>
			          	<td><?php echo $user;?></td>
			          	<td style='word-wrap:break-word'><?php echo $action;?></td>
			          	<td><?php echo $phone;?></td>
			          	<td><?php echo $name;?></td>
			          	<td style='word-wrap:break-word'><?php echo $case;?></td>
			          	<td><?php echo $ID;?></td>
			          	<td style='word-wrap:break-word'><?php echo $message;?></td>
						<td style="vertical-align:middle;">
							<form method="POST" action="">
								<input name="idlog" type="hidden" value="<?php echo $logid;?>">
					      		<button class="valign btn-floating btn-small waves-effect waves-light red lighten-2" type="submit"><i class="material-icons">delete</i></button>
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
		$firstPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=logreport&pages=1&lastID=".$lastIdMsg."'>1 ... </a></li>";
		$lastPage = "<li><a href='".$_SERVER['PHP_SELF']."?menu=logreport&pages=".$totPages."&lastID=".$lastIdMsg."'> ... ".$totPages."</a></li>";

		if($curPages <= 0 || $curPages > $totPages){
			header('Location: ./?menu=logreport&pages=1&lastID='.$lastIdMsg);
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
			header('Location: ./?menu=logreport&pages=1&lastID='.$lastIdMsg);
		}
		
		echo $liFirstPage;

		for ($j=$firstPosPage; $j <= $lastPosPage; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=logreport&pages=".$j."&lastID=".$lastIdMsg."'>".$j."</a></li>";
		}

		echo $liLastPage;

		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			</ul>
		</div>
	</div>
</div>
