<?php
if(!isset($_SESSION['user']) || !isset($_SESSION['priv'])){
session_destroy();
header ('Location : ./');
}else{

function sorter($a, $b){
    $ad = strtotime($a['time']);
    $bd = strtotime($b['time']);
    return ($ad-$bd);
}
$messages = array();
$getNumber = $_GET['number'];

$queryCustomer = mysql_query("SELECT * FROM customer WHERE phone = '".$getNumber."'");
$rowCustomer = mysql_fetch_array($queryCustomer);
	$getName = $rowCustomer['name'];
	$getCase = $rowCustomer['hal'];


if($getName == ""){
	$dissbut = "disabled";
	$labelbut = "Add Customer Name";
}else{
	$dissbut = "";
	$labelbut = "SEND";
}
if(isset($_POST['submit'])&& $getName == ''){
?>
<div class="row">
	<div class="container">
	    <form class="col s12 z-depth-2" method="POST" target="">
	      <div class="row">
	      	<div class="col s12" style="padding-top:30px; padding-bottom:30px;">
	      		<span class="red-text">Name and Case Customer are empty, please fill for first time..</span>
	      	</div>
	        <div class="input-field col s6">
	          <input id="name" name="fillname" type="text" class="validate">
	          <label for="name">Customer Name</label>
	        </div>
	        <div class="input-field col s6">
	          <input id="Case" name="fillcase" type="text" class="validate">
	          <label for="case">Customer Case</label>
	        </div>
	        <div class="col offset-s1 s12 right">
	        	<a class="waves-effect waves-light btn-large red darken-4" onclick="javasrcipt:window.location.href='./index.php?menu=thread'"><i class="material-icons right">error</i>Cancel</a>
	        	<button class="waves-effect waves-light btn-large red darken-4" type="submit" name="fillsubmit"><i class="material-icons right">send</i>Send</button>
	      	</div>
	      </div>
	  </form>
  </div>
</div>
<?php
}else{
	$sender =$_GET['number'];
	if(isset($_POST['fillsubmit'])){
		$query = "SHOW TABLE STATUS LIKE 'outbox'";
                                      $result = mysql_query($query);
                                      $data  = mysql_fetch_array($result);
                                      $newID = $data['Auto_increment'];


		$name = $_POST['fillname'];
		$case =$_POST['fillcase'];
		$inserttocustomer = "INSERT INTO customer (name, phone, hal, smsID) VALUES ('".$name."', '".$sender."', '".$case."', '".$newID."')";
		if (mysql_query($inserttocustomer)) {
			header('Location:   ./index.php?menu=thread');
		}else{
	   	    echo "Error: ".mysql_error($conn);
	    }
	}

	if (isset ($_GET['number'])) {
		$sender =$_GET['number'];
		$name = $getName;
		$case =$getCase;
		$userpriv = $_SESSION['priv'];
		$messages = array();
		if(isset($_SESSION['priv']) && $_SESSION['priv'] == 2 ){
		$sentitems = "SELECT 
						DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
						SendingDateTime as time, TextDecoded  as text, 
						Status 
						FROM sentitems 
						WHERE replace(replace(DestinationNumber,'+62','0'), '+628', '08') = '".$sender."'";
		}else{
			$sentitems = "SELECT 
						DATE_FORMAT(SendingDateTime, '%e %b %Y - %k:%i') as date, 
						SendingDateTime as time, TextDecoded  as text, 
						Status 
						FROM sentitems 
						WHERE CreatorID != 'admin' AND replace(replace(DestinationNumber,'+62','0'), '+628', '08') = '".$sender."'";
		}
		$getMessages = mysql_query($sentitems) or die (mysql_error());
		if(($getMessages && mysql_num_rows($getMessages))) {
			while($msg = mysql_fetch_array($getMessages)) {
				$msg['type'] = 'sentitems';
				$messages[] = $msg;
			}
		}

		$inbox = "SELECT 
					DATE_FORMAT(ReceivingDateTime, '%e %b %Y - %k:%i') as date, 
					ReceivingDateTime as time, 
					TextDecoded as text 
					FROM inbox 
					WHERE replace(replace(SenderNumber,'+62','0'), '+628', '08') = '".$sender."'";

		$getMessages = mysql_query($inbox) or die (mysql_error());
		if(($getMessages && mysql_num_rows($getMessages))) {
			while($msg = mysql_fetch_array($getMessages)) {
				$msg['type'] = 'inbox';
				$messages[] = $msg;
			}
		}

		usort($messages, 'sorter');

?>
<div class="row">
	<div class="container">
		<div class="col s7">
	      <h5 class="left">SMS with <?php echo $getName." - ".$_GET['number'];?></h5>
	    </div>
	    <div class="col s5">
	      <a class="btn-floating btn-large waves-effect waves-light blue blue lighten-2 right" onclick="javasrcipt:window.location.href='<?php echo './index.php?menu=thread&cat=detail&number='.$sender.'&lastID='.$lastIdMsg;?>'"><i class="material-icons">replay</i></a>
	    </div>
	    <div class="col s12">
	      <h5 class="left">Case - <?php echo $getCase;?></h5>
	    </div>
		<div class="col s12 z-depth-1" style="padding-top:20px; padding-bottom:20px;">
		    <form class="col s12" method="POST" action="">
		    	<div class="col s12" style="height:55%; overflow-y: auto;">
		<?php
			foreach($messages as $msg) {
				if (isset($msg['Status'])) {
					switch ($msg['Status']){
						case 'SendingOK': 			$icon='done_all'; break;
						case 'SendingOKNoReport': 	$icon='done_all'; break;
						case 'SendingError': 		$icon='report_problem'; break;
						case 'DeliveryOK': 			$icon='done_all'; break;
						case 'DeliveryFailed': 		$icon='report_problem'; break;
						case 'DeliveryPending': 	$icon='done'; break;
						case 'DeliveryUnknown': 	$icon='report_problem'; break;
						case 'Error': 				$icon='report_problem'; break;
						default : 					$icon='report_problem'; break;
					}
				}
				if($msg['type'] === "inbox"){
					?>
			    	<div class="col s7 card-panel teal left lighten-2">
				    	<span class='blue-grey-text text-lighten-5'>
				    		Date : <?php echo $msg['date'];?>
				    	</span>
				    		<br/>
				    	<span class="white-text" style='word-wrap:break-word'>
				    		<?php echo $msg['text'];?>
				        </span>
				    </div>
		<?php
				} else {
		?>
					<div class="col s7 card-panel green right lighten-2">
				    	<span class='blue-grey-text text-lighten-5'>
				    		Date : <?php echo $msg['date'];?>
				    	</span>
				    		<br/>
				    	<span class="white-text" style='word-wrap:break-word'>
				    		<?php echo $msg['text'];?>
				        </span>
				    	<i class="tiny material-icons right white-text"><?php echo $icon;?></i>
				    </div>

		<?php
				    }
				}
				    	?>
				    </div>
				    <hr/ style="width:95%">
				    <div class="input-field col s8" style="margin-top:0">
				      <textarea placeholder="Reply..."="textarea1" class="materialize-textarea" length="160" name="message" <?php echo $dissbut; ?> required></textarea>
				    </div>
				    <div class="col s4 right" style="padding-top:40px">
				    <?php
				     if(isset($_POST['message'])) {
					    $postNumber=$sender;
					    $postMsg=$_POST['message'];
					    $postname=$getName;
					    $postcase=$case;
					    $user=$_SESSION['user'];
					    $query = "SHOW TABLE STATUS LIKE 'outbox'";
                                              $result = mysql_query($query);
                                              $data  = mysql_fetch_array($result);
                                              $newID = $data['Auto_increment'];


					    $totSmsPage = ceil(strlen($postMsg)/160);
					    if($totSmsPage == 1){
						      $inserttooutbox1 = "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID, ID) 
	        					VALUES ('".$postNumber."', '".$postMsg."', '".$user."', '".$newID."')";
						      $logging1 = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message) 
						        VALUES ('".$user."', 'Sending Message', now(), '".$newID."', '".$postNumber."', '".$postname."', '".$postcase."', '".$postMsg."')";
						      $inserttocustomer1 = "INSERT INTO customer (name, phone, hal, smsID) 
						        VALUES ('".$postname."', '".$postNumber."', '".$postcase."', '".$newID."')";

					        if (!mysql_query($inserttooutbox1)) {
					        	echo "Error: ".$inserttooutbox1. " ".mysql_error($conn);
					         }
					        if (!mysql_query($logging1)) {
					            echo "Error: ".$logging1. " ".mysql_error($conn);
					        }
					        if (!mysql_query($inserttocustomer1)) {
					            echo "Error: ".$inserttocustomer1. " ".mysql_error($conn);
					        }
					    }

					    if($totSmsPage <> 1){
					      $hitsplit = ceil(strlen($postMsg)/153);
					      $split  = str_split($postMsg, 153);

					      $query = "SHOW TABLE STATUS LIKE 'outbox'";
					      $result = mysql_query($query);
					      $data  = mysql_fetch_array($result);
					      $newID = $data['Auto_increment'];

					      for ($i=1; $i<=$totSmsPage; $i++){
					        $udh = "050003A7".sprintf("%02s", $hitsplit).sprintf("%02s", $i);
					        $msg = $split[$i-1];

					        if ($i == 1){
					          $inserttooutbox = "INSERT INTO outbox (DestinationNumber, UDH, TextDecoded, ID, MultiPart, CreatorID, Class)
					            VALUES ('".$postNumber."', '".$udh."', '".$msg."', '".$newID."', 'true', '".$user."', '-1')";
					          $logging = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message)
					            VALUES ('".$user."', 'Sending Multiple Message', now(), '".$newID."', '".$postNumber."', '".$postname."', '".$postcase."', '".$postMsg."')";
					          $inserttocustomer = "INSERT INTO customer (name, phone, hal, smsID) 
					            VALUES ('".$postname."', '".$postNumber."', '".$postcase."', '".$newID."')";
						if (!mysql_query($logging)) {
                                                      echo "Error: ".$logging. " ".mysql_error($conn);
                                                  }
                                                  if (!mysql_query($inserttocustomer)) {
                                                      echo "Error: ".$inserttocustomer. " ".mysql_error($conn);
                                                }
							
					        }else{
					          $inserttooutbox = "INSERT INTO outbox_multipart(UDH, TextDecoded, ID, SequencePosition)
					            VALUES ('".$udh."', '".$msg."', '".$newID."', '".$i."')";
					        }
						if (!mysql_query($inserttooutbox)) {
						 if ($i == 1){
                                                    echo "Error: ".$inserttooutbox. " ".mysql_error($conn);
                                                  }
						}
						}
					    }
					}
				    ?>
				      <button class="waves-effect waves-light btn-large blue lighten-2 right" type="submit" name="submit"><i class="material-icons right">send</i><?php echo $labelbut; ?></button>
				    </div>
				</form>
		</div>
	</div>
</div>


<?php
}else{header('location: ./');}
}
}
?>

