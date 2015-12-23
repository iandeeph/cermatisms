<?php
$cat = isset($_GET['cat'])?$_GET['cat']:'';
$userpriv = $_SESSION['user'];
?>
<div class="row">
	<div class="col s12">
      <h3 class="left">Thread Message</h3>
    </div>
    <div class="col s12">
    	<?php
    	switch ($cat) {
			case 'detail':
				// <!-- Detail Thread Start  -->
					include "detailthread.php";
				//<!-- Detail Thread End  -->
				break;
			default:
				// <!-- Menu Thread Start  -->
					include "homethread.php";
				//<!-- Menu Thread End  -->
				break;
		}?>
	</div>
</div>