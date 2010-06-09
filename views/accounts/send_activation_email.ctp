<p>Please enter the email address you signed up with.</p>
<?php
echo $form->create();
echo $form->input('email');
echo $form->end(__("Send me my activation code", true));
?>
