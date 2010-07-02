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

	var $__settings;
	var $controller;
	var $User;
	var $Auth, $Email, $Session, $Cookie;

	function initialize(&$controller) {
		$this->controller =& $controller;

		// Load the login singleton, where we can access logged in user info
		// from anywhere.
		App::import('Libs', 'Accounts.Login');

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

		// Load config.
		require(dirname(dirname(dirname(__FILE__))) . DS . 'config' . DS . 'accounts.php');
		// Extract model name from model setting, in case it's in a plugin.
		$modelParts = explode('.', Configure::read('accounts.model'));
		$this->modelName = $modelParts[count($modelParts)-1];
		Configure::write('accounts.modelName', $this->modelName);
		// Load config into $this->__settings.
		$this->__settings = Configure::read('accounts');

		// Load the user model so we can save last login time.
		$this->User =& $controller->{$this->modelName};
		if (!isset($this->User)) {
			$controller->loadModel($this->__settings['model']);
		}
		// Attach AccountsBehavior to user model.
		$this->User->Behaviors->attach('Accounts.Accounts');
		if (!is_a($this->User->Behaviors->Accounts, 'AccountsBehavior')) {
			trigger_error("Failed to load AccountsBehavior.", E_USER_ERROR);
		}

	}

	function setupAuth() {
		$this->Auth->userModel = $this->modelName;
		$this->Auth->fields = array(
			'username' => Configure::read('accounts.fields.username'),
			'password' => Configure::read('accounts.fields.password')
		);
		$this->Auth->userScope = array(
			$this->__settings['fields']['activated'] => true,
			$this->__settings['fields']['banned'] => false
		);
		$this->Auth->loginError = __("Login failed.  Invalid " . Inflector::humanize($this->__settings['fields']['username']) . " or password.  Please make sure you have activated your account.", true);
		$this->Auth->loginAction = array(
			'plugin' => 'accounts',
			'controller' => 'accounts',
			'action' => 'login'
		);
		$this->Auth->loginRedirect = $this->__settings['redirectAfterLogin'];
		$this->Auth->logoutRedirect = $this->__settings['redirectAfterLogout'];
		$this->Auth->autoRedirect = false;
		$this->Auth->allow(array('view', 'display'));
		// Put the auth info in the Login singleton for easy access.
		Login::set($this->Auth->user());
		// Update last login in user model.
		$this->User->updateLastLogin();
		// Manually login user if they have ticked remember me.
		$this->rememberMe();
	}

	function rememberMe() {
		if (Login::exists()) {
			// Remember the user.
			if (!empty($this->controller->data[$this->modelName]['remember_me'])) {
				$this->Cookie->write('rememberMe', Login::get($this->modelName . '.id'), true, '30 days');
			}
		} else {
			// Already remembered?  Login.
			$id = $this->Cookie->read('rememberMe');
			if ($id) {
				$user = $this->User->findById($id);
				$this->manualLogin($user);
			}
		}
	}

	function manualLogin($user) {
		// Login using an Account row instead of username and password.
		if (isset($user[$this->modelName][Configure::read('accounts.fields.password')])) {
			unset($user[$this->modelName][Configure::read('accounts.fields.password')]);
		}
		$this->controller->Session->write('Auth', $user);
		// Put the auth info in the Login singleton for easy access.
		Login::set($this->Auth->user());
		// Update last login in Account model.
		$this->User->updateLastLogin();
	}

	function sendActivationEmail($email, $username, $code) {
		$this->__setupEmail($email, __($this->__settings['emailActivationSubject'], true), 'activation');
		$this->controller->set(compact('username', 'code'));
		$this->Email->send();
		return !$this->__emailError();
	}

	function sendSuccessEmail() {

	}

	function sendResetPasswordEmail($email, $username, $code) {
		$this->__setupEmail($email, __($this->__settings['emailResetPasswordSubject'], true), 'reset_password');
		$this->controller->set(compact('username', 'code'));
		$this->Email->send();
		return !$this->__emailError();
	}

	function __setupEmail($to, $subject, $template) {
		$this->Email->reset();
		$this->Email->to = $to;
		$this->Email->from = $this->__settings['emailFrom'];
		$this->Email->subject = $subject;
		$this->Email->template = $template;
		$this->Email->layout = $this->__settings['emailLayout'];
		$this->Email->sendAs = $this->__settings['emailSendAs'];
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
