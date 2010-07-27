<div class="login form">
<?php
echo $session->flash('auth');

// Create login form.
echo $form->create(Configure::read('accounts.modelName'), array(
	'id' => 'loginForm',
	'url' => array(
		'plugin' => 'accounts',
		'controller' => 'accounts',
		'action' => 'login'
	)
));

// Username, password, remember me.
echo $form->inputs(array(
	'legend' => __("Login", true),
	Configure::read('accounts.fields.username') => array('id' => 'LoginUsername'),
	Configure::read('accounts.fields.password') => array('id' => 'LoginPassword'),
	'remember_me' => array('type' => 'checkbox')
));
?>

<?php // If we're in a modal window there needs to be a cancel button. ?>
<?php if (@$isModal): ?>
	<div class="buttons"><div class="buttonsInnermost">
		<?php
		echo $form->submit(__("Login", true));
		echo $form->submit(__("Cancel", true), array('class' => 'cancel', 'onclick' => "document.getElementById('modalLogin').style.display = 'none'; return false;"));
		?>
	</div></div>
<?php else: ?>
	<?php echo $form->submit(__("Login", true)); ?>
<?php endif; ?>

<?php
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

<div style="clear: both;"></div>
