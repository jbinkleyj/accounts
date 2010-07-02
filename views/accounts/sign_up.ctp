<div class="sign_up form">
<?php
echo $form->create(Configure::read('accounts.modelName'), array('url' => array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'sign_up'
)));
	?>

	<fieldset>
		<legend><?php __("Sign Up"); ?></legend>

		<?php
		echo $form->input(Configure::read('accounts.fields.username'));
		if (Configure::read('accounts.fields.username') != Configure::read('accounts.fields.email')) {
			echo $form->input(Configure::read('accounts.fields.email'));
		}
		echo $form->input('new_password', array('type' => 'password'));
		echo $form->input('confirm_password', array('type' => 'password'));
		?>

	</fieldset>

<?php
echo $form->end(__("Sign Up", true));
?>
</div>
