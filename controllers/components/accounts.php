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
class AccountsComponent extends Object {

	var $settings = array(
		'emailFrom' => null,
		'emailActivationSubject' => "Please activate your account",
		'emailSuccessSubject' => "You have successfully signed up!",
		'emailPasswordResetSubject' => "Resetting your password",
		'emailDeleteEmail' => "We're sorry to see you go",
		'emailLayout' => 'default',
		'emailSendAs' => 'text',
		'redirectAfterActivation' => '/'
	);

	var $controller;
	var $Account;
	var $Auth, $Email;

	function initialize(&$controller, $settings) {
		$this->controller =& $controller;
		// Load the login singleton, where we can access logged in user info
		// from anywhere.
		App::import('Vendor', 'Accounts.Login');
		// Auth component dependancy.
		$this->Auth =& $controller->Auth;
		if (!is_a($this->Auth, 'AuthComponent')) {
			trigger_error("Auth component not loaded in controller.  Try putting something like \"var \$components = array('Session', 'Auth', 'Email', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		// Email component dependancy.
		$this->Email =& $controller->Email;
		if (!is_a($this->Email, 'EmailComponent')) {
			trigger_error("Email component not loaded in controller.  Try putting something like \"var \$components = array('Session', 'Auth', 'Email', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		// Settings.
		$this->settings = Set::merge($this->settings, $settings);
		$errors = array();
		if (!$this->settings['emailFrom']) {
			trigger_error("Please set 'emailFrom' for the Accounts.Accounts component.  E.g. \"var \$components = array('Accounts.Accounts' => array('emailFrom' => 'myemail@myhost.com', ... );\".", E_USER_WARNING);
		}
		// Load the Account model so we can save last login time.
		$this->Account =& $controller->Account;
		if (!isset($this->Account)) {
			$controller->loadModel('Account');
		}
	}

	function startup(&$controller) {
	}

	function setupAuth() {
		$this->Auth->userModel = 'Account';
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
		$this->Auth->loginError = __("Login failed.  Invalid email or password.", true);
		$this->Auth->loginAction = array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'login'
		);
		$this->Auth->allow(array('view', 'display'));
		// Put the auth info in the Login singleton for easy access.
		Login::set($this->Auth->user());
		// Update last login in Account model.
		$this->Account->updateLastLogin();
	}

	function sendActivationEmail($email, $code) {
		$this->__setupEmail($email, __($this->settings['emailActivationSubject'], true), 'activation');
		$this->controller->set(compact('email', 'code'));
		$success = $this->Email->send();
		if ($this->Email->smtpError) {
		  trigger_error("SMTP ERROR: " . $this->Email->smtpError, E_USER_WARNING);
		}
		return $success;
	}

	function sendSuccessEmail() {

	}

	function sendResetPasswordEmail() {

	}

	function sendDeleteEmail() {

	}

	function __setupEmail($to, $subject, $template) {
		$this->Email->reset();
		$this->Email->to = $to;
		$this->Email->from = $this->settings['emailFrom'];
		$this->Email->subject = $subject;
		$this->Email->template = $template;
		$this->Email->layout = $this->settings['emailLayout'];
		$this->Email->sendAs = $this->settings['emailSendAs'];
	}

	function storeAction() {
		// Could have a 'stored' var or something that stops forms from being posted straight after login.
	}

}
?>
