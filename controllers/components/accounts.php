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

	var $_settings = array(
		'emailFrom' => null,
		'emailActivationSubject' => "Please activate your account",
		'emailSuccessSubject' => "You have successfully signed up!",
		'emailPasswordResetSubject' => "Resetting your password",
		'emailDeleteEmail' => "We're sorry to see you go"
	);

	var $controller;
	var $Auth, $Email;

	function initialize(&$controller, $settings) {
		App::import('Vendor', 'Accounts.Login');
		$this->controller =& $controller;
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
		$this->_settings = Set::merge($this->_settings, $settings);
		$errors = array();
		if (!$this->_settings['emailFrom']) {
			trigger_error("Please set 'emailFrom' for the Accounts.Accounts component.  E.g. \"var \$components = array('Accounts.Accounts' => array('emailFrom' => 'myemail@myhost.com', ... );\".");
		}
	}

	function startup() {
	}

	function setupAuth() {
		$this->Auth->userModel = 'Account';
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
		$this->Auth->loginAction = array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'login'
		);
		$this->Auth->allow(array('view', 'display'));
		Login::set($this->Auth->user());
	}

	function sendActivationEmail() {

	}

	function sendSuccessEmail() {

	}

	function sendResetPasswordEmail() {

	}

	function sendDeleteEmail() {

	}

	function _email($to, $subject, $template) {

	}

	function storeAction() {
		// Could have a 'stored' var or something that stops forms from being posted straight after login.
	}

}
?>
