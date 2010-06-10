<p><?php __("Please enter the email address you signed up with.", true); ?></p>

<?php
echo $form->create('Account');
echo $form->input('email');
echo $form->end(__("Send me my reset password code", true));
?>
