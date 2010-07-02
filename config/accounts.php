<?php
/**
 * CakePHP Accounts Plugin
 * http://github.com/adrianedworthy/accounts
 *
 * By Adrian Edworthy
 * http://adrianedworthy.com/
 *
 * Copyright (c) 2010, Adrian Edworthy
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

// ATTENTION.  There's no need to edit this file, you can override these
// settings in app/config/bootstrap.php.

// E.g. Configure::write('accounts.emailFrom', 'my@email.com');

$default = array(

	'model' => 'User',

	// Email.
	'emailFrom' => 'example@example.com',
	'emailActivationSubject' => "Please activate your account",
	'emailResetPasswordSubject' => "You have requested that your password be reset",
	'emailSuccessSubject' => "You have successfully signed up!",
	'emailPasswordResetSubject' => "Resetting your password",
	'emailLayout' => 'default',
	'emailSendAs' => 'text',

	// Redirection.
	'redirectAfterActivation' => '/',
	'redirectAfterResetPassword' => '/',
	'redirectAfterLogin' => '/',
	'redirectAfterLogout' => '/',
	'redirectAfterEdit' => '/',

	// The names of the fields in the database.
	'fields' => array(
		// Username and password fields are also assigned to Auth::$fields.
		'username' => 'email',
		'password' => 'password',
		'email' => 'email',
		'activated' => 'activated',
		'banned' => 'banned',
		'last_login' => 'last_login'
	),

	// These are not implemented yet.
	'enableSignUp' => true,
	'enableEmail' => true,
	'enableActivation' => true,
	'enableRememberMe' => true,
	'enableFacebook' => false,
	'enableAjax' => false

);

$bootstrapped = Configure::read('accounts');
Configure::write('accounts', Set::merge($bootstrapped, $default));

?>
