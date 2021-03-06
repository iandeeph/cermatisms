<?php
if(!isset($_SESSION['user']) || !isset($_SESSION['priv'])){
session_destroy();
header ('Location : ./');
}else{
?>
<div class="row">
  <div class="container">
  <div class="col s12">
      <h3 class="left">SMS Blasting</h3>
  </div>
    <form class="col s10" enctype="multipart/form-data" method="POST" action="" style="padding-bottom:50px; padding-top:10px;">
      <div class="file-field input-field">
        <div class="btn">
          <span>Upload file</span>
          <input class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Upload file contain phone numbers" type="file" name="fileContent">
        </div>
        <div class="file-path-wrapper">
          <input placeholder="Upload CSV File, format : name, phone number, case, message..." class="file-path validate" type="text" name="filePath">
        </div>
      </div>
      <div class="col s12 center">
          <label class="active" for="or">-- OR --</label>
      </div>
      <div class="input-field col s6 ">
        <i class="material-icons prefix">contact_phone</i>
        <textarea id="phoneNumber" class="materialize-textarea" name="phoneNumber"></textarea>
        <label for="phoneNumber">Phone number</label>
      </div>
      <div class="input-field col s12 ">
        <i class="material-icons prefix">work</i>
        <input id="perihal" type="text" class="validate" name="case" >
        <label for="perihal">Customer Case</label>
      </div>
      <div class="input-field col s12">
        <i class="material-icons prefix">mode_edit</i>
        <textarea id="textarea1" class="materialize-textarea" name="message"></textarea>
        <label for="textarea1">Your Message</label>
      </div>
      <div class="col s12">
        <button class="waves-effect waves-light btn-large red darken-4" name="submitBlasting" type="submit"><i class="material-icons right">send</i>Send</button>
      </div>
      <div class="input-field col s7 left-align">
              <h6><span class="green-text text-darken-4">

<?php
//Upload File
if (isset($_POST['filePath']) && !empty($_POST['filePath'])) {
  if (is_uploaded_file($_FILES['fileContent']['tmp_name'])) {
    $filename = $_FILES['fileContent']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if( $ext !== 'csv') {
      echo "<span class='red-text text-darken-4'>File Extension not CSV, file extension only allowed CSV..!!!</span>";
    }else{
      //Import uploaded file to Database
      $handle = fopen($_FILES['fileContent']['tmp_name'], "r");
      while (($data = fgetcsv($handle, 1000, "," ,'"' )) !== FALSE) {
        $name = $data[0];
        $number = $data[1];
        $postcase=$data[2];
        $postMsg=$data[3];
        $userpriv = $_SESSION['priv'];
        $user=$_SESSION['user'];
        $msg = array();
        $totSmsPage = ceil(strlen($postMsg)/160);

        $query = "SHOW TABLE STATUS LIKE 'outbox'";
              $result = mysql_query($query);
              $data  = mysql_fetch_array($result);
              $newID = $data['Auto_increment'];

          if($totSmsPage == 1){
              $inserttooutbox1 = "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) 
                VALUES ('".$number."', '".$postMsg."', '".$user."')";
              $logging1 = "INSERT INTO log (user, action, date, messageID, phone, hal, message) 
                VALUES ('".$user."', 'Sending Message (Blasting by CSV)', now(), '".$newID."', '".$number."', '".$postcase."', '".$postMsg."')";
              $inserttocustomer1 = "INSERT INTO customer (name, phone, hal, smsID) 
                VALUES ('".$name."', '".$number."', '".$postcase."', '".$newID."')";

                if (mysql_query($inserttooutbox1)) {
                  echo "Message sent to ".$number;
                  echo "<br/>";
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
                  VALUES ('".$number."', '".$udh."', '".$msg."', '".$newID."', 'true', '".$user."', '-1')";
                $logging = "INSERT INTO log (user, action, date, messageID, phone, hal, message) 
                  VALUES ('".$user."', 'Sending Multiple Message (Blasting by CSV)', now(), '".$newID."', '".$number."',  '".$postcase."', '".$postMsg."')";
                $inserttocustomer = "INSERT INTO customer (name, phone, hal, smsID) 
                  VALUES ('".$name."', '".$number."', '".$postcase."', '".$newID."')";

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
                  echo "Multiple Message Blasting sent to ".$number;
                  echo "<br/>";
                }
                } else {
                  echo "Error: ".$inserttooutbox. " ".mysql_error($conn);
                }
            }
          }
        }

    fclose($handle);
    }
  }
}

if(isset($_POST['phoneNumber']) && !empty($_POST['phoneNumber'])) {
  $nmbr = explode("\n", str_replace("\r", "", $_POST['phoneNumber']));
  foreach ($nmbr as $number) {
    $postMsg=$_POST['message'];
    $postcase=$_POST['case'];
    $userpriv = $_SESSION['priv'];
    $user=$_SESSION['user'];
    $msg = array();
    $totSmsPage = ceil(strlen($postMsg)/160);

    $query = "SHOW TABLE STATUS LIKE 'outbox'";
          $result = mysql_query($query);
          $data  = mysql_fetch_array($result);
          $newID = $data['Auto_increment'];

        if($totSmsPage == 1){
            $inserttooutbox1 = "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) 
              VALUES ('".$number."', '".$postMsg."', '".$user."')";
            $logging1 = "INSERT INTO log (user, action, date, messageID, phone, hal, message) 
              VALUES ('".$user."', 'Sending Message (Blasting)', now(), '".$newID."', '".$number."', '".$postcase."', '".$postMsg."')";
            $inserttocustomer1 = "INSERT INTO customer (phone, hal, smsID) 
              VALUES ('".$number."', '".$postcase."', '".$newID."')";

              if (mysql_query($inserttooutbox1)) {
                echo "Message sent to ".$number;
                echo "<br/>";
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
                VALUES ('".$number."', '".$udh."', '".$msg."', '".$newID."', 'true', '".$user."', '-1')";
              $logging = "INSERT INTO log (user, action, date, messageID, phone, hal, message) 
                VALUES ('".$user."', 'Sending Multiple Message (Blasting)', now(), '".$newID."', '".$number."',  '".$postcase."', '".$postMsg."')";
              $inserttocustomer = "INSERT INTO customer (phone, hal, smsID) 
                VALUES ('".$number."', '".$postcase."', '".$newID."')";

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
                echo "Multiple Message Blasting sent to ".$number;
                echo "<br/>";
              }
              } else {
                echo "Error: ".$inserttooutbox. " ".mysql_error($conn);
              }
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