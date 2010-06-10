<?php __("Before you may login, please activate your account by clicking on the link provided:", true); ?>


<?php echo $html->url(array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'activate',
	$email,
	$code
), true); ?>
