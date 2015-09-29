<!-- Login Page Start -->
    <div class="container">
       <div class="section">
          <div class="row">
            <form class="col offset-s2 s6 z-depth-2" method="POST" action="">
              <div class="row">
                <div class="col s12 center-align">
                  <h4><span>LOGIN</span></h4>
                </div>
                <div class="input-field col offset-s1 s10">
                  <input id="username" type="text" class="validate" name="username"required>
                  <label for="username">Username</label>
                </div>
                <div class="input-field col offset-s1 s10">
                  <input id="password" type="password" class="validate" name="password" required>
                  <label for="password">Password</label>
                </div>
                <div class="input-field col s11">
                  <button class="btn waves-effect waves-light right blue lighten-2" style="height:45px" type="submit" name="submit">  Submit
                    <i class="material-icons">send</i>
                  </button>
                </div>
                <div class="input-field col offset-s1 s12">
                  <h6><span class="red-text text-darken-4">   
<?php
if(isset($_POST['submit'])){

  $postUsername = $_POST['username'];
  $postPassword = $_POST['password'];

  $sqlUser = (  "SELECT * FROM user WHERE username = '".$postUsername."' AND password = '".$postPassword."'");
  $result = mysql_query($sqlUser);
  $row = mysql_fetch_array($result);

  if(mysql_num_rows($result) == 1){ 

    $_SESSION['logged'] = 1;
    $_SESSION['user'] = $_POST['username'];
    $_SESSION['priv'] = $row['priviledge'];
    header('Location:   index.php?menu=home&lastID='.$lastIdMsg);
    exit;
  } else { echo "Username dan Password Salah.!!";
  }
}
?>
          </span></h6>
          </div>
        </div>
      </form>
    </div>
    </div> 
  </div>
<!-- Login Page End -->