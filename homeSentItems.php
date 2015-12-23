<?php
// untuk pagination.. jika page belum ditentukan maka defaultnya adalah $_GET['pages'] = 1
if(!isset($_GET['pages'])){
	$_GET['pages'] = 1;
}
$userpriv = $_SESSION['priv'];

//inisialisasi berapa baris yang ditampilkan perhalaman
$perPages = 13;

//priv yang membedakan antara admin dan user.. 
// priv = 2 adalah admin, jadi admin bisa liat semua pesan.. 
// jika bukan admin tidak dapat melihat pesan yang dibuat oleh admin..
// tujuannya adalah agar user tidak perlu melihat pesan yang sifatnya test dsb
// inisialisasi session priv dilakukan saat login (login.php)

if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
	// 'GROUP BY ID' dilakukan karena saat pesan yang dikirim lebih dari 160 character akan dipecah menjadi halaman berbeda dan dikirim sejumlah halaman tersebut..
	// Pesan yang terpecah tersebut memiliki ID yang sama pada table 'sentitems'
	// maka itu di GROUP BY ID agar pesan dapat ditampilkan menjadi 1 pesan penuh dan tidak terpecah (seperti saat dikirim) di halaman Sent Item.. 
	$getSentitems = mysql_query("SELECT * FROM sentitems GROUP BY ID");
}else{
	// tidak menampilkan pesan yang dibuat oleh user Admin
	// sejauh ini user admin ada 2 : Admin dan BashAdmin
	$getSentitems = mysql_query("SELECT * FROM sentitems WHERE CreatorID NOT LIKE '%admin' GROUP BY ID");
}

//mencari total halaman
$totCont = mysql_num_rows($getSentitems);
$totPages = ceil($totCont/$perPages);

//pagination code
if(isset($_GET['pages'])){
	$curPages = $_GET['pages'];
	// menentukan limitation query perhalaman
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

//memberikan attribut disable pada tombol next atau previous
if($curPages <= 1){
	$dissleft = "disabled";
}else{$dissleft="";}
if($curPages >= $tpages){
	$dissright = "disabled";
}else{$dissright="";}

//memberikan link pada tombol next dan previous
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

//delete all
if(isset($_POST['deleteAll']) && isset($_POST['actionCheckbox'])){
	// pada setiap element input checkbox diberikan name array, maka dilaukan loop disini untuk mengeluarkan semua input yang di checked / hanya yang di checked saja
	// name pada setiap element berisi ID
	foreach ($_POST['actionCheckbox'] as $ID) {
		// dicari berdasarkan ID yang didapat dari name checkbox yang checked
		// dilimit 1 untuk menghindari error (thanks @ihsan)
		$qrySent = mysql_query("SELECT * FROM sentitems  WHERE ID = '".$ID."' GROUP BY ID LIMIT 1");
		if($qrySent){
			$rowSent = mysql_fetch_array($qrySent);
		}

    	$qryCust = mysql_query("SELECT * FROM customer WHERE smsID = '".$ID."' LIMIT 1");
    	if($qryCust){
    		$rowCust = mysql_fetch_array($qryCust);
    	}

    	if($rowSent && $rowCust){
			$postNumber = $rowSent['DestinationNumber'];
			$postMsg = $rowSent['TextDecoded'];
		    $postname=$rowCust['name'];
		    $postcase=$rowCust['hal'];
			$IDCust = $rowCust['idCust'];

		    $user=$_SESSION['user'];
			$IDsent = $ID;

			// delete berdasarkan ID yang didapat dari name checkbox yang checked
			$delquery = "DELETE FROM sentitems WHERE ID = '".$IDsent."'";

			if (mysql_query($delquery)) {
				if(isset($_POST['customerid'])){
					// untuk pertama kali pengiriman sms atau balas sms.. maka user diharusnya memberikan nama dan case/hal untuk nomor tersebut (jika nomor belum diberi nama pastinya)
					// saat ini, setiap sms yang dikirim akan menambahkan record baru pada customer table.. dengan kata lain 1 sms = 1 customer
					// maka dari itu setiap sms dihapus, juga dilakukan penghapusan pada customer
					$delquerycust = "DELETE FROM customer WHERE idCust = '".$IDCust."'";
					if (!mysql_query($delquerycust)) {
				        echo "Error: ".mysql_error($conn);
				    }
				}
				// melakukan pencatatan pada table log
				$logging = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message)
							VALUES ('".$user."', 'Deleting Sent Items', now(), '".$IDsent."', '".$postNumber."', '".$postname."', '".$postcase."', '".$postMsg."')";
				mysql_query($logging);
		    }else{
		    	echo "Error: ".mysql_error($conn);
		    }
		}
	}
}


//resend all
if(isset($_POST['resendAll']) && isset($_POST['actionCheckbox'])){
	// pada setiap element input checkbox diberikan name array, maka dilaukan loop disini untuk mengeluarkan semua input yang di checked / hanya yang di checked saja
	// name pada setiap element berisi ID
	foreach ($_POST['actionCheckbox'] as $ID) {
		$qrySent = mysql_query("SELECT * FROM sentitems  WHERE ID = '".$ID."' GROUP BY IDLIMIT 1");
		if($qrySent){
			$rowSent = mysql_fetch_array($qrySent);
		}

		if($rowSent){
			// dilakukan query ke table 'log' untuk mengambil data yang sebelumnya sudah tercatat pada log..
			// hal tersebut dilakukan karena akan lebih mudah mengambil data yang sudah ada dalam 1 table, seperti data 'name' dan 'hal'
			// alasan lain adalah karena pada table log, data 'message' tidak terpecah seperti pada table 'sentitems'
			$qryLogForResend = mysql_query("SELECT *, 
							replace(replace(phone,'+62','0'), '+628', '08') as number, 
							DATE_FORMAT(date, '%e %b %Y  %k:%i') as time 
							FROM log 
							WHERE (message LIKE '%".$rowSent['TextDecoded']."%' AND phone = '".$rowSent['DestinationNumber']."')");
			if($qryLogForResend){
				$rowResend = mysql_fetch_array($qryLogForResend);
			}

			if($rowResend){
				$postNumber=$rowResend['number'];
				$postMsg=$rowResend['message'];
				$postname=$rowResend['name'];
				$postcase=$rowResend['hal'];
				$user=$_SESSION['user'];
				sendSms($postNumber, $postMsg, $postname, $postcase, $user);				
			}
		}
	}
}

?>
<div class="row">
	<div class="col s8">
		<h3>Sent Items</h3>
	</div>
	<div class="col s4">
      <a class="btn-floating btn-large waves-effect waves-light blue blue lighten-2 right" href="javascript:history.go(0)" style="margin-top:30px"><i class="material-icons">replay</i></a>
    </div>
<?php
// mengambil data pesan yang berada di outbox (pesan pending yang belum dikirim oleh gammu)
if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
$getPendingitems = mysql_query("SELECT * FROM outbox");
}else{
$getPendingitems = mysql_query("SELECT * FROM outbox WHERE CreatorID NOT LIKE '%admin'");
}

if($getPendingitems){
	$totCont = mysql_num_rows($getPendingitems);
}

// meberikan warna pada font, hijau jika tidak ada pending dan merah jika ada pesan pending
if($totCont && $totCont == 0) {
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
							// menampilkan semua user yang ada untuk dipilih
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
	<!-- FILTERING END -->
<div class="row">
	<div class="col s12">
		<form action="" method="POST" name="formSentItems">
			<div class="col s12">
				<a href="#modalResendAll" title="resend selected sms..." class="modal-trigger btn-floating btn-small waves-effect waves-light blue lighten-2 disabled" id="resendAll"><i class="material-icons">repeat</i></a>
				<a href="#modalDeleteAll" class="modal-trigger btn-floating btn-small waves-effect waves-light red lighten-2 disabled" id="deleteAll"><i class="material-icons">delete</i></a>
			</div>
			<table class="striped">
				<thead>
					<tr>
						<th width="100" data-field="action" class="center">
							<input type="checkbox" class="filled-in" name="allActionCheckbox" id="allActionCheckbox" />
							<label for="allActionCheckbox"></label>
						</th>
						<th width="150px" data-field="time">Time</th>
						<th width="200px" data-field="sender">Recipients</th>
						<th width="200px" data-field="case">Customer Case</th>
						<th data-field="message">Message</th>
						<th width="100px" data-field="status">Status</th>
						<th width="100" data-field="author">Author</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if(isset($_SESSION['priv']) && $_SESSION['priv'] == '2' ){
						$sentItemPerPages = mysql_query("SELECT *,
											replace(replace(DestinationNumber,'+62','0'), '+628', '08') as number, 
											DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
											CreatorID
											FROM sentitems
											GROUP BY ID
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
											GROUP BY ID
											ORDER BY SendingDateTime
											DESC LIMIT ".$perPages." 
											OFFSET ".$start." ");
						}
						$numCheckbox = 0;
						$contentMsg = array();
						if($sentItemPerPages && mysql_num_rows($sentItemPerPages) > 0 && isset($_SESSION['priv'])) {
							while($msg = mysql_fetch_array($sentItemPerPages)) {
								switch ($msg['Status']){
									// memberikan warna berbeda pada text untuk setiap status
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
									<td style="vertical-align:middle;" class="center">
										<input type="checkbox" class="filled-in" value="<?php echo $msg['ID']; ?>" name="actionCheckbox[]" id="<?php echo 'actionCheckbox'.$numCheckbox;?>" />
										<label for="<?php echo 'actionCheckbox'.$numCheckbox;?>"></label>
								    	
								    	<input name="number[]" type="hidden" value="<?php echo $msg['number'];?>">
										<input name="message[]" type="hidden" value="<?php echo $message;?>">
										<input name="name[]" type="hidden" value="<?php echo $nametodel;?>">
										<input name="case[]" type="hidden" value="<?php echo $case;?>">
										<input name="sentid[]" type="hidden" value="<?php echo $msg['ID'];?>">
										<input name="customerid[]" type="hidden" value="<?php echo $custid;?>">
								    </td>
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
									<?php
										// menampilkan isi pesan yang sebelumnya terpecah karena jumlah character lebih dari 160, dengan ini digabungkan kembali
										$sqlMsg = mysql_query("SELECT TextDecoded FROM sentitems WHERE ID = '".$msg['ID']."'");
										while($rowMsg= mysql_fetch_array($sqlMsg)){
											$contentMsg[]=$rowMsg['TextDecoded'];
										}
										$message = join('',$contentMsg);
										// karena $contentMsg adalah array, maka di unset per looping
										unset($contentMsg);
									?>
									<td style='word-wrap:break-word'>
										<p style="text-align:justify">
											<?php echo $message; ?>
										</p>
									</td>
									<td class='<?php echo $color."-text";?>'><?php echo $status; ?></td>
									<td><?php echo $msg['CreatorID']; ?></td>
								</tr>
							<?php
						$numCheckbox++;	
						}
					}
					?>
				</tbody>
			</table>
			<!-- Modal Delete ALL -->
			<div id="modalDeleteAll" class="modal">
				<div class="modal-content">
					<h4>Delete Confirmation</h4>
					<p>Are you sure want to delete?</p>
				</div>
				<div class="modal-footer">
					<button class="red modal-action modal-close waves-effect waves-green btn-flat" type="submit" name="deleteAll">Yes</button>
					<a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
				</div>
			</div>
			<!-- Modal Resend ALL -->
			<div id="modalResendAll" class="modal">
				<div class="modal-content">
					<h4>Resend Confirmation</h4>
					<p>Are you sure want to resend selected items?</p>
				</div>
				<div class="modal-footer">
					<button class="blue lighten-2 modal-action modal-close waves-effect waves-green btn-flat" type="submit" name="resendAll">Yes</button>
					<a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
				</div>
			</div>
		</form>
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