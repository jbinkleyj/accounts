<p>Please enter the email address you signed up with.</p>
<?php
echo $form->create('Account');
echo $form->input('email');
echo $form->end(__("Send me my activation code", true));
?>
