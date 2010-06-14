<div class="login form">
<?php
echo $session->flash('auth');
echo $form->create('Account', array('action' => 'login'));
echo $form->input('email');
echo $form->input('password');
echo $form->input('remember_me', array('type' => 'checkbox'));
echo $form->submit(__("Submit", true));
echo $form->end();
?>
</div>

<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li>
			<?php echo $html->link(__("Sign Up", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'sign_up'
			)); ?>
		</li>
		<li>
			<?php echo $html->link(__("Forgot your password?", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'send_reset_password_email'
			)); ?>
		</li>
		<li>
			<?php echo $html->link(__("Resend activation email", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'send_activation_email',
				$this->data['Account']['email']
			)); ?>
		</li>
	</ul>
</div>
