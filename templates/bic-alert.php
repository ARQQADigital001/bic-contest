<?php
if(!isset($type)) return;
//$dashboard      = get_page_link(943);
//$create_account = get_page_link(584);

$dashboard=  get_page_link(6994);
$create_account = get_page_link(584);
$message ="";
switch ($type){
	case 'account_already_exists':
		$link    = "<a href='$dashboard'>".__("Dashboard",'bic')."</a>";
		$message = sprintf(__("You already have an account go to your  %s",'bic'),$link);
		break;
	case 'create_an_account':
		$link    = "<a href='$create_account'>".__("Create your account",'bic')."</a>";
		$message = sprintf(__("You don't have an account go to %s",'bic'),$link);
		break;
	case 'account_created_successfully':
		$link    = "<a href='$dashboard'>".__("Dashboard",'bic')."</a>";
		$message = sprintf(__("Account Created successfully. go to your  %s",'bic'),$link);
		break;
}?>
<div class="first-title alert">
	<h3><?php echo $message?></h3>
</div>