<?php
$logquery = "SELECT *, replace(replace(phone,'+62','0'), '+628', '08') as number, DATE_FORMAT(date, '%e %b %Y  %k:%i') as time FROM log ORDER BY date DESC";

	if(!isset($_GET['pages'])){
		$_GET['pages'] = 1;
	}

	$perPages = 13;
	$getSentitems = mysql_query("SELECT * FROM log");
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
		$prevPage = $_SERVER['PHP_SELF']."?menu=logreport&pages=".$prevCurPage = $_GET['pages'] - 1;
	}else{
		$prevPage = '';
	}
	if ($curPages < $tpages) {
		$nextPage = $_SERVER['PHP_SELF']."?menu=logreport&pages=".$nextCurPage = $_GET['pages'] + 1;
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
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr>
					<th width="200" data-field="time">Time</th>
					<th width="150" data-field="user">User</th>
					<th width="300" data-field="action">Action</th>
					<th width="200" data-field="number">Number</th>
					<th width="300" data-field="name">Name</th>
					<th width="500" data-field="case">Case</th>
					<th width="100" data-field="id">ID Message</th>
					<th width="750" data-field="message">Message</th>
					<th width="50" data-field="delete"></th>
				</tr>
			</thead>
			<tbody>
				<?php
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
	<div class="container">
		<div class="col s12">
			<ul class="pagination">
				<li class="waves-effect <?php echo $dissleft; ?>" <?php echo $dissleft; ?>><a href="<?php echo $prevPage; ?>" class="<?php echo $dissleft; ?>"><i class="material-icons">chevron_left</i></a></li>
		<?php
		for ($j=1; $j <= $tpages; $j++) {
			if ($curPages == $j) {
				$active = 'active';
			}else{$active="";}
			echo "<li class='".$active."'><a href='".$_SERVER['PHP_SELF']."?menu=logreport&pages=".$j."'>".$j."</a></li>";
		}
		?>
				<li class="waves-effect <?php echo $dissright; ?>"><a href="<?php echo $nextPage; ?>" class="<?php echo $dissright; ?>"><i class="material-icons">chevron_right</i></a></li>
			  </ul>
		</div>
	</div>
</div>
