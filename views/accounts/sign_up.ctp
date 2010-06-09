<?php
echo $form->create();
echo $form->inputs(array(
	'legend' => __("Sign Up", true),
	'email',
	'new_password' => array('type' => 'password'),
	'confirm_password' => array('type' => 'password')
));
echo $form->end(__("Sign Up", true));
?>
