<div class="row">
  <div class="container">
  <div class="col s12">
      <h3 class="left">Incoming Inbox Simulation</h3>
    </div>
    <form class="col s10" method="POST" action="" style="padding-bottom:50px; padding-top:10px;">
      <div class="input-field col s6 ">
        <i class="material-icons prefix">contact_phone</i>
        <input id="icon_telephone" type="tel" class="validate" name="number" pattern="^0[0-9]{10,11}|^\(?\+62[0-9]{10,11}" required >
        <label for="icon_telephone">Phone Number</label>
      </div>
      <div class="input-field col s12">
        <i class="material-icons prefix">mode_edit</i>
        <textarea id="textarea1" class="materialize-textarea" length="160" name="message" required></textarea>
        <label for="textarea1">Your Message</label>
      </div>
      <div class="col offset-s1 s4">
        <button class="waves-effect waves-light btn-large red darken-4" type="submit"><i class="material-icons right">send</i>Send</button>
      </div>
      <div class="input-field col s7 left-align">
              <h6><span class="red-text text-darken-4">
  <?php
  if(isset($_POST['number'])) {
    $postNumber=$_POST['number'];
    $postMsg=$_POST['message'];
    $user=$_SESSION['user'];

    $IDsmsQuery = mysql_query("SELECT ID as lastid FROM sentitems ORDER BY ID DESC LIMIT 1");
    $getIDsms = mysql_fetch_array($IDsmsQuery);
    $lastid = $getIDsms['lastid'] + 1;

    $inserttoinbox = "INSERT INTO inbox (ReceivingDateTime, SenderNumber, TextDecoded) VALUES (now(), '".$postNumber."', '".$postMsg."')";
    $logging = "INSERT INTO log (user, action, date, messageID, phone, name, hal, message) VALUES ('".$user."', 'Admin Doing Incoming Inbox Simulation', now(), '".$lastid."', '".$postNumber."', '', '', '".$postMsg."')";

    if (mysql_query($inserttoinbox)) {
      mysql_query($logging);
        echo "Message sent to ".$_POST['number'];
      } else {
        echo "Error: ".mysql_error($conn);
      }
    }
  ?>  
              </span></h6>
    </div>
  </form>
  </div>
</div>