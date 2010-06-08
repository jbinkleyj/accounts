<div id="login" class="<?php echo ((Login::exists()) ? 'loggedIn' : 'loggedOut'); ?>">

	<?php if (!Login::exists()): ?>

		<?php
		echo $form->create('Account', array('action' => 'login'));
		echo $form->input("email");
		echo $form->input("password");
		echo $form->input('remember', array('label' => __("Remember me", true),'type' => 'checkbox'));
		echo $form->end(__("Login", true));
		?>

		<p>
			<?php echo $html->link(__("Sign Up", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'signUp'
			)); ?>
		</p>

		<p>
			<?php echo $html->link(__("Forgot your password?", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'resetPassword'
			)); ?>
		</p>

	<?php else: ?>

		<p>
			<?php echo __("Logged in as ", true) . Login::get('Account.email'); ?>.
			<?php echo $html->link(__("Logout", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'logout'
			)); ?>.
		</p>

	<?php endif; ?>

</div>
