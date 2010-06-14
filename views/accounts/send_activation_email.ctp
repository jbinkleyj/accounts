<p><?php __("Please enter the email address you signed up with.", true); ?></p>

<div class="send_activation_email form">
<?php
echo $form->create('Account');
echo $form->input('email');
echo $form->end(__("Send me my activation code", true));
?>
</div>
