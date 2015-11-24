<?php
if(isset($_GET['view'])){
	$view = $_GET['view'];
}else{
	$view = "";
}
switch ($view) {
	case 'on':
		include "viewReport.php";
		break;
	default:
//===========> DEFAULT START
ifSubmitFilterReport($oldestDate, $newestDate);

if(isset($_SESSION['labelDateFilter'])){
	$labelDateFilter 	= $_SESSION['labelDateFilter'];
	$datefrom 			= $_SESSION['postDateFrom'];
	$dateTo 			= $_SESSION['postDateTo'];
}

	$whereFilter 		= $_SESSION['filterReport'];

$qryReport = mysql_query("SELECT CreatorID as user,count(TextDecoded) as total FROM sentitems WHERE ".$whereFilter." GROUP BY CreatorID");
?>
<div class="row">
	<div class="col s8">
		<h3>Customer Service Report</h3>
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
					<form action="" method="POST" name="filterSentitems">
						<div class="col s3">
							<label class="active" for="datefrom">Date From</label>
							<input value="<?php echo date('j F, Y', $datefrom); ?>" name="datefrom" id="datefrom" type="date" class="datepicker">
						</div>
						<div class="col s3">
							<label class="active" for="dateto">Date To</label>
							<input value="<?php echo date('j F, Y', $dateTo); ?>" name="dateto" id="dateto" type="date" class="datepicker">
						</div>
						<div class="col s12" style="margin-bottom:15px">
							<a id="resetButton" class="waves-effect waves-light btn-large"><i class="material-icons right">clear</i>Clear</a>
							<button name="filterReport" class="waves-effect waves-light btn-large"><i class="material-icons right">send</i>Filter</button>
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
	</div>
	<div class="col s12">
		<table class="striped">
			<thead>
				<tr>
					<th width="300" data-field="time">User</th>
					<th width="150" data-field="sender">Success Send</th>
					<th width="150" data-field="case">Failed Send</th>
					<th width="150" data-field="status">Total SMS</th>
					<th width="150" data-field="author">No. of Customer</th>
					<th width="200" data-field="action">Detail</th>
				</tr>
			</thead>
			<tbody>
				<?php
					while($rowReport = mysql_fetch_array($qryReport)){
						$qrySent = mysql_query("SELECT 
							count(TextDecoded) as totSent 
							FROM sentitems 
							WHERE (Status LIKE 'SendingOK%') AND (CreatorID = '".$rowReport['user']."') AND (".$whereFilter.")");
						$rowSent = mysql_fetch_array($qrySent);

						$qryFail = mysql_query("SELECT 
							count(TextDecoded) as totFail 
							FROM sentitems 
							WHERE (Status = 'SendingError' OR Status = 'DeliveryFailed' OR Status = 'Error') AND (CreatorID = '".$rowReport['user']."') AND (".$whereFilter.")");
						$rowFail = mysql_fetch_array($qryFail);

						$qryNumCust = mysql_query("SELECT 
							DestinationNumber as totNumCust
							FROM sentitems 
							WHERE (CreatorID = '".$rowReport['user']."') AND (".$whereFilter.")
							GROUP BY DestinationNumber ");
						
						$NumCust = 0;
						while($rowNumCust = mysql_fetch_array($qryNumCust)){
							$NumCust++;
						}

				?>
						<tr>
							<td><?php echo $rowReport['user']; ?></td>
							<td><?php echo $rowSent['totSent']; ?></td>
							<td><?php echo $rowFail['totFail']; ?></td>
							<td><?php echo $rowReport['total']; ?></td>
							<td><?php echo $NumCust; ?></td>
							<td><a href="index.php?menu=report&view=on&user=<?php echo $rowReport['user'];?>&lastID=<?php echo $lastIdMsg;?>" target="_blank" class="waves-effect waves-light btn blue lighten-2">View</a></td>
						</tr>
				<?php
					}
				?>
			<tbody>
		</table>
	</div>
</div>
<?php

//===========> DEFAULT ENDS
		break;
}
?>