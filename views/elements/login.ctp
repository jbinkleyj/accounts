<?php if (Configure::read('accounts.enableAjax')): ?>

	<?php echo $html->link(__("Login", true), array(
		'plugin' => 'accounts',
		'controller' => 'accounts',
		'action' => 'login'
	), array('onclick' => "document.getElementById('modalLogin').style.display = 'block'; document.getElementById('LoginUsername').focus(); return false;")); ?>
	<div class="modal" id="modalLogin" style="display: none;"><div class="modalWindow">

		<h3>Login</h3>

		<?php
		echo $form->create(Configure::read('accounts.modelName'), array(
			'id' => 'loginForm',
			'url' => array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'login'
			)
		));
		echo $form->input(Configure::read('accounts.fields.username'), array('id' => 'LoginUsername'));
		echo $form->input(Configure::read('accounts.fields.password'), array('id' => 'LoginPassword'));
		?>
		<div class="modalButtons"><div class="modalButtonsInnermost">
			<?php
			echo $form->input('remember_me', array('type' => 'checkbox'));
			echo $form->submit(__("Submit", true));
			echo $form->submit(__("Cancel", true), array('class' => 'cancel', 'onclick' => "document.getElementById('modalLogin').style.display = 'none'; return false;"));
			?>
		</div></div>
		<?php
		echo $form->end();
		?>

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

	</div></div>

	or

	<?php echo $html->link(__("Sign Up", true), array(
		'plugin' => 'accounts',
		'controller' => 'accounts',
		'action' => 'sign_up'
	)); ?>

<?php else: ?>

	<div id="login" class="<?php echo ((Login::exists()) ? 'loggedIn' : 'loggedOut'); ?>">

		<?php if (!Login::exists()): ?>

			<?php
			// The login form.
			echo $form->create(Configure::read('accounts.modelName'), array('id' => 'loginForm', 'url' => array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'login'
			)));
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

<?php endif; ?>
