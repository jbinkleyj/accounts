<div class="login form">
<?php
echo $session->flash('auth');
echo $form->create(Configure::read('accounts.modelName'), array('url' => array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'login'
)));
echo $form->input(Configure::read('accounts.fields.username'), array('id' => 'LoginUsername'));
echo $form->input(Configure::read('accounts.fields.password'), array('id' => 'LoginPassword'));
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
				@$this->data[Configure::read('accounts.modelName')][Configure::read('accounts.fields.username')]
			)); ?>
		</li>
	</ul>
</div>
