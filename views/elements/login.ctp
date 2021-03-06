<div id="login" class="<?php echo ((Login::exists()) ? 'loggedIn' : 'loggedOut'); ?>">

	<?php if (!Login::exists()): ?>

		<?php
		echo $form->create(Configure::read('accounts.modelName'), array('id' => 'loginForm', 'url' => array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'login'
		)));
		echo $form->hidden('just_logged_in', array('value' => true));
		echo $form->input(Configure::read('accounts.fields.username'), array('id' => 'LoginBarUsername', 'error' => false));
		echo $form->input(Configure::read('accounts.fields.password'), array('id' => 'LoginBarPassword', 'error' => false));
		echo $form->input('remember_me', array('id' => 'LoginRemember', 'type' => 'checkbox', 'error' => false));
		echo $form->end(__("Login", true));
		?>

		<?php echo $html->link(__("Sign Up", true), array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'sign_up'
		)); ?>

		<?php echo $html->link(__("Forgot your password?", true), array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'send_reset_password_email'
		)); ?>

	<?php else: ?>

		<p>
			<?php echo __("Logged in as ", true) . Login::get(Configure::read('accounts.fields.username')); ?>.
			<?php echo $html->link(__("Account Settings", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'edit',
				Login::get('id')
			)); ?>
			<?php echo $html->link(__("Logout", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'logout'
			)); ?>.
		</p>

	<?php endif; ?>

</div>
