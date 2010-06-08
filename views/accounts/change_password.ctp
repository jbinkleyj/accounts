<?php
echo $form->create();
echo $form->inputs(array(
	'legend' => __("Change your password", true),
	'id',
	'old_password' => array('type' => 'password'),
	'new_password' => array('type' => 'password'),
	'confirm_password' => array('type' => 'password', 'label' => __("Confirm New Password", true))
));
echo $form->end(__("Save Changes", true));
?>
