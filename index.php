<?php
ob_start();
error_reporting(E_ALL);
session_start();
require "php/connconf.php";
$menu = isset($_GET['menu'])?$_GET['menu']:'';

    $queryLastId = mysql_query("SELECT max(ID) as lastId FROM inbox");
    $resultLastId = mysql_fetch_array($queryLastId);
    $lastIdMsg = $resultLastId['lastId'];
?>
<!DOCTYPE html>
<html>
  <head>

    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        .pagination li.active{
            background-color: #64B5F6;

        }
        a.disabled {
            pointer-events: none;
        }
        header, main, footer {
         padding-left: 240px;
        }

    </style>

 <!--Import jQuery before materialize.js-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
// request permission on page load
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
});

var lastIdMsg = <?php echo $lastIdMsg;?> || '';
var newNotif = 0;
$(document).ready(function(){
    setInterval(function(){
        $.ajax({
            url: './notif.php?last='+lastIdMsg,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if(data && data.length > 0) {
                    for(i in data) {
                        var notification = new Notification(
                        "Message from " + data[i].SenderNumber, {
                        icon: 'images/cermati.png',
                        body: data[i].TextDecoded,
                        });

                        notification.onclick = function () {
                          window.open("index.php?menu=thread&cat=detail&number="+data[i].SenderNumber+"&lastID="+data[i].ID);      
                        };
                    }
                    newNotif += data.length;
                    if($('#inboxNotif').find('span').length > 0) {
                        $('#inboxNotif').find('span').html(newNotif);
                    } else {
                        $('#inboxNotif').append('<span class="new badge">' +  newNotif + '</span>');
                    }
                    lastIdMsg = data[data.length - 1].ID;
                }
            }
        });
    }, 5000);
});
</script>
  </head>

  <body>
  	<header>
      	<nav class="top-nav blue lighten-2" style="height:100px">
            <div class="container" style="height:100%; padding-top:10px;">
                <div class="valign-wrapper" style="vertical-align:middle;"><h3 class="page-title valign">Cermati SMS</h3></div>
            </div>
        </nav>
        <div class="container">
        <a href="#" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only"><i class="mdi-navigation-menu"></i></a>
        </div>
        <ul id="nav-mobile" class="side-nav fixed">
            <li class="logo center" style="height:100px;">
            	<img src="images/cermati.png" style="height:100px" alt="Cermati">
            	</img>
        	</li>
        	<hr style="width:80%"/>
        	<?php
        	if(isset($_SESSION['logged'])) {
    		?>
            <li class="bold">
            	<a href="index.php?menu=sendsms&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Send SMS</a>
            </li>
            <li class="bold">
            	<a href="index.php?menu=cekinbox&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal" id="inboxNotif">Inbox</a>
            </li>
            <li class="bold">
            	<a href="index.php?menu=sentitem&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Sent Item</a>
            </li>
            <li class="bold">
                <a href="index.php?menu=thread&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Thread</a>
            </li>
            <?php
            if(isset($_SESSION['priv']) && $_SESSION['priv'] == 2) {
            ?>
            <li class="bold">
                <a href="index.php?menu=logreport&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Log Report</a>
            </li>
            <li class="bold">
                <a href="index.php?menu=simulation&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Inbox Simulation</a>
            </li>
            <?php }?>
            <li class="bold">
            	<a href="index.php?menu=logout" class="waves-effect waves-teal">Logout</a>
            </li>
            <?php
        }
        ?>
        </ul>
    </header>
    <main>
	<div class="row">
		<div class="col s12">
			<!-- page content  start-->
			<?php
				if(isset($_SESSION['logged'])) {		
				switch ($menu) {
					case 'sendsms':
						// <!-- Send SMS Form Start  -->
							include "sendsms.php";
						//<!-- Send SMS Form End  -->
						break;

					case 'cekinbox':
						// <!-- Inbox Start  -->
							include "inbox.php";
						//<!-- Inbox End  -->
						break;

					case 'sentitem':
						// <!-- Inbox Start  -->
							include "sentitem.php";
						//<!-- Inbox End  -->
						break;
                    case 'thread':
                        // <!-- thread Start  -->
                            include "thread.php";
                        //<!-- thread End  -->
                        break;
                    case 'logreport':
                        // <!-- log Start  -->
                            include "logreport.php";
                        //<!-- log End  -->
                        break;
                    case 'simulation':
                        // <!-- log Start  -->
                            include "simulation.php";
                        //<!-- log End  -->
                        break;
					case 'logout':
						// <!-- logout Start  -->
							include "logout.php";
						//<!-- logout End  -->
						break;
					
					default:
						// <!-- home Start  -->
							include "sendsms.php";
						//<!-- home End  -->
						break;
				}
			} else {
				// <!-- Login Page Start -->
  					include "login.php"; 
				// <!-- Login Page End -->
			}
			?>
		<!-- page content end-->
		</div>
	 </div>
    </main>
  </body>
</html>