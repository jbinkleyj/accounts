<div id="modalLoginMenu">
	<?php if (!Login::exists()): ?>

		<?php echo $html->link(__("Login", true), array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'login'
		), array('onclick' => "document.getElementById('modalLogin').style.display = 'block'; document.getElementById('LoginUsername').focus(); return false;")); ?>

		<div class="modal" id="modalLogin" style="display: none;"><div class="modalWindow">

			<?php echo $this->element('login_form', array('plugin' => 'accounts', 'isModal' => true)); ?>

		</div></div>

		or

		<?php echo $html->link(__("Sign Up", true), array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'sign_up'
		)); ?>

	<?php else: ?>

			<?php echo __("Logged in as ", true) . Login::get(Configure::read('accounts.fields.username')); ?>.
			<?php echo $html->link(__("Account Settings", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'edit',
				Login::get('id')
			)); ?>

			or

			<?php echo $html->link(__("Logout", true), array(
				'plugin' => 'accounts',
				'controller' => 'accounts',
				'action' => 'logout'
			)); ?>.

	<?php endif; ?>
</div>
