<div class="change_password form">
<?php
echo $form->create();
echo $form->inputs(array(
	'legend' => __("Account Settings", true),
	'id',
	'email',
	'old_password' => array('type' => 'password'),
	'new_password' => array('type' => 'password'),
	'confirm_password' => array('type' => 'password', 'label' => __("Confirm New Password", true))
));
echo $form->end(__("Save Changes", true));
?>
</div>
