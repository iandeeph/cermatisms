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
    $user=$_SESSION['user'];

    sendSms($postNumber, $postMsg, $postname, $postcase, $user);
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
