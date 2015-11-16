<?php
ob_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);
session_start();
require "php/connconf.php";
$menu = isset($_GET['menu'])?$_GET['menu']:'';

$queryLastId = mysql_query("SELECT max(ID) as lastId FROM inbox");
$resultLastId = mysql_fetch_array($queryLastId);
$lastIdMsg = $resultLastId['lastId'];

function ifSubmitFilter(){
    $qryOldestDate = mysql_query("SELECT ReceivingDateTime FROM inbox ORDER BY ReceivingDateTime ASC LIMIT 1");
    $rowOldestDate = mysql_fetch_array($qryOldestDate);
    $oldestDate = $rowOldestDate['ReceivingDateTime'];

    $qryNewestDate = mysql_query("SELECT ReceivingDateTime FROM inbox ORDER BY ReceivingDateTime DESC LIMIT 1");
    $rowNewestDate = mysql_fetch_array($qryNewestDate);
    $newestDate = $rowNewestDate['ReceivingDateTime'];
    if(isset($_POST['filterInboxSumbit'])){
    //---------------------->> Filtering By Date
        $postDateFrom   = (!empty($_POST['datefrom'])) ? $_POST['datefrom'] : $oldestDate;
        $postDateTo     = (!empty($_POST['dateto'])) ? $_POST['dateto'] : $newestDate;

        $dateFrom       = new DateTime($postDateFrom);
        $dateTo         = new DateTime($postDateTo);
        $from           = date_format($dateFrom, 'Y-m-d 00:00:00');
        $to             = date_format($dateTo, 'Y-m-d 23:59:59');

        $dateFilter = "(ReceivingDateTime BETWEEN '".$from."' AND '".$to."')";

        $labelDateFilter = "Filter Date From : <b>".date_format($dateFrom, 'j F Y')."</b> to : <b>".date_format($dateTo, 'j F Y')."</b>";

    //---------------------->> Filtering By Sender
        if(!empty($_POST['sender'])){
            $postSender = $_POST['sender'];
            $qrySenderFilter = mysql_query("SELECT phone FROM customer WHERE phone LIKE '%".$postSender."%' or name LIKE '%".$postSender."%'");
            $resultNumber = array();
            if(mysql_num_rows($qrySenderFilter)){
                while($rowSenderFilter = mysql_fetch_array($qrySenderFilter)){
                    if (substr( $rowSenderFilter['phone'], 0, 1)  ===  "0") {
                        $replaceSender = '+62'.substr($rowSenderFilter['phone'], 1);
                    }
                    $resultNumber[] = "SenderNumber='".$replaceSender."'";
                }
                $postNumberFilterBySender = join(' OR ', $resultNumber);
            }else{
                $postNumberFilterBySender = "SenderNumber NOT LIKE '%'";
            }
            $labelSenderFilter = "Filter Name/Phone : <b>".$_POST['sender']."</b>";
            $_SESSION['labelSenderFilter']   = $labelSenderFilter;
            $_SESSION['postSender']          = $postSender;
        }else{
            $postNumberFilterBySender = "SenderNumber LIKE '%'";
            unset($_SESSION['labelSenderFilter']);
            unset($_SESSION['postSender']);
        }
        $numberFilter = $postNumberFilterBySender;

    //---------------------->> Filtering By Case
        if(!empty($_POST['caseFilter'])){
            $postCase = $_POST['caseFilter'];
            $qryCaseFilter = mysql_query("SELECT phone FROM customer WHERE hal LIKE '%".$postCase."%'");
            $resultNumber2 = array();
            if(mysql_num_rows($qryCaseFilter)){
                while($rowCaseFilter = mysql_fetch_array($qryCaseFilter)){
                    if (substr($rowCaseFilter['phone'], 0, 1)  ===  "0") {
                        $replaceSender2 = '+62'.substr($rowCaseFilter['phone'], 1);
                    }
                    $resultNumber2[] = "SenderNumber='".$replaceSender2."'";
                }
                $postNumberFilterByCase = join(' OR ', $resultNumber2);
            }else{
                $postNumberFilterByCase = "SenderNumber NOT LIKE '%'";
            }
            $labelCaseFilter = "Filter Case : <b>".$_POST['caseFilter']."</b>";
            $_SESSION['labelCaseFilter']   = $labelCaseFilter;
            $_SESSION['postCase']          = $postCase;
        }else{
            $postNumberFilterByCase = "SenderNumber LIKE '%'";
            unset($_SESSION['labelCaseFilter']);
            unset( $_SESSION['postCase']);
        }
        $numberFilterByCase = $postNumberFilterByCase;

    //---------------------->> Filtering By Message
        if(!empty($_POST['messageFilter'])){
            $postMessage    = $_POST['messageFilter'];
            $NumberFilterByMsg = "TextDecoded LIKE '%".$postMessage."%'";
            $labelMsgFilter = "Filter Message : <b>".$_POST['messageFilter']."</b>";
            $_SESSION['labelMsgFilter']   = $labelMsgFilter;
            $_SESSION['postMessage']      = $postMessage;
        }else{
            $NumberFilterByMsg = "TextDecoded LIKE '%'";
            unset($_SESSION['labelMsgFilter']);
            unset( $_SESSION['postMessage']);

        }
        

        $whereFilterArray = array($dateFilter, '('.$numberFilter.')','('.$numberFilterByCase.')','('.$NumberFilterByMsg.')');
        $whereFilter = join(' AND ', $whereFilterArray);

        $_SESSION['labelDateFilter']     = $labelDateFilter;
        $_SESSION['whereFilter']         = $whereFilter;
        $_SESSION['postDateFrom']        = $dateFrom;
        $_SESSION['postDateTo']          = $dateTo;
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
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

        .pagination {
         display: inline-block;
        }
        .pagination > li{
            display: inline;
            width: auto;
            height: 35px;
            padding: 5px 15px;
        }
    </style>
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
                $getMenu = isset($_GET['menu'])?$_GET['menu']:'sendsms';
    		?>
            <li class="bold <?php if ($getMenu == 'sendsms'){echo 'active';} ?>">
            	<a href="index.php?menu=sendsms&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Send SMS</a>
            </li>
            <li class="bold <?php if ($getMenu == 'blasting'){echo 'active';} ?>">
                <a href="index.php?menu=blasting&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">SMS Blasting</a>
            </li>
            <li class="bold <?php if ($getMenu == 'cekinbox'){echo 'active';} ?>">
            	<a href="index.php?menu=cekinbox&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal" id="inboxNotif">Inbox</a>
            </li>
            <li class="bold <?php if ($getMenu == 'sentitem'){echo 'active';} ?>">
            	<a href="index.php?menu=sentitem&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Sent Item</a>
            </li>
            <li class="bold <?php if ($getMenu == 'pending'){echo 'active';} ?>">
                <a href="index.php?menu=pending&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Pending Item</a>
            </li>
            <li class="bold <?php if ($getMenu == 'thread'){echo 'active';} ?>">
                <a href="index.php?menu=thread&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Thread</a>
            </li>
            <li class="bold <?php if ($getMenu == 'mythread'){echo 'active';} ?>">
                <a href="index.php?menu=mythread&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">My Thread</a>
            </li>
            <?php
            if(isset($_SESSION['priv']) && $_SESSION['priv'] == 2) {
            ?>
            <li class="bold <?php if ($getMenu == 'logreport'){echo 'active';} ?>">
                <a href="index.php?menu=logreport&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Log Report</a>
            </li>
            <li class="bold <?php if ($getMenu == 'simulation'){echo 'active';} ?>">
                <a href="index.php?menu=simulation&lastID=<?php echo $lastIdMsg;?>" class="waves-effect waves-teal">Inbox Simulation</a>
            </li>
            <?php }?>
            <li class="bold <?php if ($getMenu == 'logout'){echo 'active';} ?>">
            	<a href="index.php?menu=logout" class="waves-effect waves-teal">Logout [ <?php echo $_SESSION['user'];?> ]</a>
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
                    case 'pending':
                        // <!-- Inbox Start  -->
                            include "pending.php";
                        //<!-- Inbox End  -->
                        break;
                    case 'thread':
                        // <!-- thread Start  -->
                            include "thread.php";
                        //<!-- thread End  -->
                        break;
                    case 'mythread':
                        // <!-- thread Start  -->
                            include "mythread.php";
                        //<!-- thread End  -->
                        break;
                    case 'blasting':
                        // <!-- SMS BLASTING Start  -->
                            include "blasting.php";
                        //<!-- SMS BLASTING End  -->
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
    <!--Import jQuery before materialize.js-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
        // request permission on page load
        document.addEventListener('DOMContentLoaded', function () {
          if (Notification.permission !== "granted")
            Notification.requestPermission();
        });


        //Flashing title --------|||
        (function () {

        var original = document.title;
        var timeout;

        window.flashTitle = function (newMsg, howManyTimes) {
            function step() {
                document.title = (document.title == original) ? newMsg : original;

                if (--howManyTimes > 0) {
                    timeout = setTimeout(step, 1000);
                };
            };

            howManyTimes = parseInt(howManyTimes);

            if (isNaN(howManyTimes)) {
                howManyTimes = 20;
            };

            cancelFlashTitle(timeout);
            step();
        };

        window.cancelFlashTitle = function () {
            clearTimeout(timeout);
            document.title = original;
        };

        }());

        //Flashing title ends ------ |||

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
                                "Message from " + data[i].number, {
                                icon: 'images/cermati.png',
                                body: data[i].TextDecoded,
                                });

                                notification.onclick = function () {
                                  window.open("index.php?menu=thread&cat=detail&number="+data[i].number+"&lastID="+data[i].ID);      
                                };

                                setTimeout(notification.close.bind(notification), 3000);
                            }

                            newNotif += data.length;
                            if($('#inboxNotif').find('span').length > 0) {
                                $('#inboxNotif').find('span').html(newNotif);
                            } else {
                                $('#inboxNotif').append('<span class="new badge">' +  newNotif + '</span>');
                            }

                            lastIdMsg = data[data.length - 1].ID;

                            flashTitle("New SMS...!!!");
                        }
                    }
                });
            }, 5000);
        });

        $(document).ready(function(){
            $('.collapsible').collapsible({
                accordion : false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
            });

            $('.datepicker').pickadate({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 15, // Creates a dropdown of 15 years to control year
                closeOnSelect: true
            });

            $('select').material_select();


            $('.tooltipped').tooltip({delay: 50});

            $(".clickable-row").click(function() {
                window.document.location = $(this).data("href");
            });

        });

        function resetField() {
            document.getElementById("filterInbox").reset();
        }
    </script>
  </body>
</html>