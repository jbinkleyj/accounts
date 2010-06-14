<div class="reset_password form">
<?php
echo $form->create('Account', array('url' => array(
	$email,
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
