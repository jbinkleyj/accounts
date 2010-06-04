<?php
echo $session->flash('auth');
echo $form->create('Account', array('action' => 'login'));
echo $form->input("email");
echo $form->input("password");
echo $form->input('remember', array('label' => __("Remember me", true),'type' => 'checkbox'));
echo $form->submit(__("Submit", true));
echo $form->end();
?>

<p>
	Not registered yet?
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
