<?php
if(!isset($_SESSION['user']) || !isset($_SESSION['priv'])){
session_destroy();
header ('Location : ./');
}else{
?>
<div class="row">
  <div class="container">
  <div class="col s12">
      <h3 class="left">Sending</h3>
  </div>
    <form class="col s10" method="POST" action="" style="padding-bottom:50px; padding-top:10px;">
      <div class="input-field col s8 ">
        <i class="material-icons prefix">account_circle</i>
        <input id="customername" type="text" class="validate" name="name" required >
        <label for="customername">Customer Name</label>
      </div>
      <div class="input-field col s6 ">
        <i class="material-icons prefix">contact_phone</i>
        <input id="icon_telephone" type="tel" class="validate" name="number" pattern="^0[0-9]{9,12}|^\(?\+62[0-9]{9,12}" required >
        <label for="icon_telephone">Phone Number</label>
      </div>
      <div class="input-field col s12 ">
        <i class="material-icons prefix">work</i>
        <input id="perihal" type="text" class="validate" name="case" required >
        <label for="perihal">Customer Case</label>
      </div>
      <div class="input-field col s12">
        <i class="material-icons prefix">mode_edit</i>
        <textarea id="textarea1" class="materialize-textarea" length="160" name="message" required></textarea>
        <label for="textarea1">Your Message</label>
      </div>
      <div class="col s12">
        <button class="waves-effect waves-light btn-large red darken-4" type="submit"><i class="material-icons right">send</i>Send</button>
      </div>
      <div class="input-field col s7 left-align">
              <h6><span class="green-text text-darken-4">
  <?php
  if(isset($_POST['number'])) {
    $msg = array();
    $postNumber=$_POST['number'];
    $postMsg=$_POST['message'];
    $postname=$_POST['name'];
    $postcase=$_POST['case'];
    $userpriv = $_SESSION['priv'];
    $user=$_SESSION['user'];
    $totSmsPage = ceil(strlen($postMsg)/160);

$query = "SHOW TABLE STATUS LIKE 'outbox'";
      $result = mysql_query($query);
      $data  = mysql_fetch_array($result);
      $newID = $data['Auto_increment'];


    if($totSmsPage == 1){
      $inserttooutbox1 = "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) 
        VALUES ('".$postNumber."', '".$postMsg."', '".$user."')";
      $logging1 = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message) 
        VALUES ('".$user."', 'Sending Message', now(), '".$newID."', '".$postNumber."', '".$postname."', '".$postcase."', '".$postMsg."')";
      $inserttocustomer1 = "INSERT INTO customer (name, phone, hal, smsID) 
        VALUES ('".$postname."', '".$postNumber."', '".$postcase."', '".$newID."')";

        if (mysql_query($inserttooutbox1)) {
          echo "Message sent to ".$_POST['number'];
        } else {
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
          if (mysql_query($inserttooutbox)) {
             if ($i == 1){
            echo "Multiple Message sent to ".$_POST['number'];
          }
          } else {
            echo "Error: ".$inserttooutbox. " ".mysql_error($conn);
            }
        }
      }
    }
  ?>  
              </span></h6>
    </div>
  </form>
  </div>
</div>
<?php
}
?>
