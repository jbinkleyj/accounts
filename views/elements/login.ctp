<div id="login" class="<?php echo ((Login::exists()) ? 'loggedIn' : 'loggedOut'); ?>">

	<?php if (!Login::exists()): ?>

		<?php
		echo $form->create('Account', array(
			'action' => 'login',
			'url' => array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'login'
			)
		));
		echo $form->input('email', array('id' => 'LoginEmail'));
		echo $form->input('password', array('id' => 'LoginPassword'));
		echo $form->input('remember_me', array('id' => 'LoginRemember', 'type' => 'checkbox'));
		echo $form->end(__("Login", true));
		?>

		<?php echo $html->link(__("Sign Up", true), array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'signUp'
		)); ?>

		<?php echo $html->link(__("Forgot your password?", true), array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'sendResetPasswordEmail'
		)); ?>

	<?php else: ?>

		<p>
			<?php echo __("Logged in as ", true) . Login::get('Account.email'); ?>.
			<?php echo $html->link(__("Account Settings", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'changePassword',
				Login::get('Account.id')
			)); ?>
			<?php echo $html->link(__("Logout", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'logout'
			)); ?>.
		</p>

	<?php endif; ?>

</div>
