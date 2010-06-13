<?php __("To change your password, please click on the link below:"); ?>


<?php echo $html->url(array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'reset_password',
	$email,
	$code
), true); ?>


<?php __("If you have received this email in error, and you do not wish to change your password, please ignore it and nothing will be changed."); ?>
