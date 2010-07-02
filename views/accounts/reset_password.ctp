<div class="reset_password form">
<?php
echo $form->create(Configure::read('accounts.modelName'), array('url' => array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'reset_password',
	$username,
	$code
)));
echo $form->inputs(array(
	'legend' => __("Reset Password", true),
	'new_password' => array('type' => 'password'),
	'confirm_password' => array('type' => 'password', 'label' => __("Confirm New Password", true))
));
echo $form->end(__("Save Password", true));
?>
</div>
