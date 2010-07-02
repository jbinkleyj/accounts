<p><?php __("Please enter the email address you signed up with.", true); ?></p>

<div class="send_activation_email form">
<?php
echo $form->create(Configure::read('accounts.modelName'), array('url' => array(
	'plugin' => 'accounts',
	'controller' => 'accounts',
	'action' => 'send_activation_email',
)));
echo $form->input(Configure::read('accounts.fields.username'));
echo $form->end(__("Send me my activation code", true));
?>
</div>
