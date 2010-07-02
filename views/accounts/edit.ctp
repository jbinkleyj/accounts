<div class="account_settings form">
<?php
echo $form->create(Configure::read('accounts.modelName'), array('url' => array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'edit'
)));
	?>

	<fieldset>
		<legend><?php __("Account Settings"); ?></legend>

		<?php
		echo $form->input('id');
		echo $form->input(Configure::read('accounts.fields.username'));
		if (Configure::read('accounts.fields.username') != Configure::read('accounts.fields.email')) {
			echo $form->input(Configure::read('accounts.fields.email'));
		}
		echo $form->inputs(array(
			'legend' => __("Optionally, change your password", true),
			'old_password' => array('type' => 'password'),
			'new_password' => array('type' => 'password'),
			'confirm_password' => array('type' => 'password', 'label' => __("Confirm New Password", true))
		));
		?>

	</fieldset>

<?php
echo $form->end(__("Save Changes", true));
?>
</div>
