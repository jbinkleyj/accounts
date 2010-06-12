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
		'model' => 'Accounts.Account',
		'emailFrom' => null,
		'emailActivationSubject' => "Please activate your account",
		'emailResetPasswordSubject' => "You have requested that your password be reset",
		'emailSuccessSubject' => "You have successfully signed up!",
		'emailPasswordResetSubject' => "Resetting your password",
		'emailDeleteEmail' => "We're sorry to see you go",
		'emailLayout' => 'default',
		'emailSendAs' => 'text',
		'redirectAfterActivation' => '/'
	);

	var $controller;
	var $Account;
	var $Auth, $Email, $Session, $Cookie;

	function initialize(&$controller, $settings) {
		$this->controller =& $controller;
		// Load the login singleton, where we can access logged in user info
		// from anywhere.
		App::import('Vendor', 'Accounts.Login');
		// Auth component dependancy.
		$this->Auth =& $controller->Auth;
		if (!is_a($this->Auth, 'AuthComponent')) {
			trigger_error("Auth component not loaded in controller.  Try putting something like \"var \$components = array('Auth', 'Email', 'Session', 'Cookie', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		// Email component dependancy.
		$this->Email =& $controller->Email;
		if (!is_a($this->Email, 'EmailComponent')) {
			trigger_error("Email component not loaded in controller.  Try putting something like \"var \$components = array('Auth', 'Email', 'Session', 'Cookie', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		// Session component dependancy.
		$this->Session =& $controller->Session;
		if (!is_a($this->Session, 'SessionComponent')) {
			trigger_error("Session component not loaded in controller.  Try putting something like \"var \$components = array('Auth', 'Email', 'Session', 'Cookie', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		// Cookie component dependancy.
		$this->Cookie =& $controller->Cookie;
		if (!is_a($this->Cookie, 'CookieComponent')) {
			trigger_error("Cookie component not loaded in controller.  Try putting something like \"var \$components = array('Auth', 'Email', 'Session', 'Cookie', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		// Settings.
		$this->settings = Set::merge($this->settings, $settings);
		$errors = array();
		if (!$this->settings['emailFrom']) {
			trigger_error("Please set 'emailFrom' for the Accounts.Accounts component.  E.g. \"var \$components = array('Accounts.Accounts' => array('emailFrom' => 'myemail@myhost.com', ... );\".", E_USER_WARNING);
		}
		$modelParts = explode('.', $this->settings['model']);
		$this->settings['modelName'] = $modelParts[count($modelParts)-1];
		// Load the Account model so we can save last login time.
		$this->Account =& $controller->{$this->settings['modelName']};
		if (!isset($this->Account)) {
			$controller->loadModel($this->settings['model']);
		}
	}

	function setupAuth() {
		$this->Auth->userModel = $this->settings['modelName'];
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
		$this->Auth->userScope = array('activated' => true, 'banned' => false);
		$this->Auth->loginError = __("Login failed.  Invalid email or password.  Please make sure you have activated your account.", true);
		$this->Auth->loginAction = array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'login'
		);
		$this->Auth->autoRedirect = false;
		$this->Auth->allow(array('view', 'display'));
		// Put the auth info in the Login singleton for easy access.
		Login::set($this->Auth->user());
		// Update last login in Account model.
		$this->Account->updateLastLogin();
		// Manually login user if they have ticked remember me.
		$this->rememberMe();
	}

	function rememberMe() {
		if (Login::exists()) {
			// Remember the user.
			if (!empty($this->controller->data[$this->settings['modelName']]['remember_me'])) {
				$this->Cookie->write('rememberMe', Login::get($this->settings['modelName'] . '.id'), true, '30 days');
			}
		} else {
			// Already remembered?  Login.
			$id = $this->Cookie->read('rememberMe');
			if ($id) {
				$account = $this->Account->findById($id);
				$this->manualLogin($account);
			}
		}
	}

	function manualLogin($account) {
		// Login using an Account row instead of username and password.
		if (isset($account[$this->settings['modelName']]['password'])) {
			unset($account[$this->settings['modelName']]['password']);
		}
		$this->controller->Session->write('Auth', $account);
		// Put the auth info in the Login singleton for easy access.
		Login::set($this->Auth->user());
		// Update last login in Account model.
		$this->Account->updateLastLogin();
	}

	function sendActivationEmail($email, $code) {
		$this->__setupEmail($email, __($this->settings['emailActivationSubject'], true), 'activation');
		$this->controller->set(compact('email', 'code'));
		$this->Email->send();
		return !$this->__emailError();
	}

	function sendSuccessEmail() {

	}

	function sendResetPasswordEmail($email, $code) {
		$this->__setupEmail($email, __($this->settings['emailResetPasswordSubject'], true), 'reset_password');
		$this->controller->set(compact('email', 'code'));
		$this->Email->send();
		return !$this->__emailError();
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

	function __emailError() {
		if ($this->Email->smtpError) {
			trigger_error("SMTP ERROR: " . $this->Email->smtpError, E_USER_WARNING);
			return $this->Email->smtpError;
		}
	}

	function storeAction() {
		// Could have a 'stored' var or something that stops forms from being posted straight after login.
	}

}
?>
