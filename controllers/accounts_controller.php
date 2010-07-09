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
class AccountsController extends AccountsAppController {

	var $uses = null;
	var $modelName;
	var $User;

	var $helpers = array('Html', 'Form', 'Paginator');

	var $paginate = array(
		'limit' => 10
	);

	function beforeFilter() {
		if (!is_a($this->Accounts, 'AccountsComponent')) {
			trigger_error("Accounts.Accounts component not loaded in controller.  Try putting something like \"var \$components = array('Session', 'Auth', 'Email', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		parent::beforeFilter();
		$this->Auth->allow('sign_up', 'reset_password', 'activate', 'send_activation_email', 'send_reset_password_email', 'login', 'logout');
		$this->modelName = Configure::read('accounts.modelName');
		$this->User =& $this->{$this->modelName};
		// Set the paginate order, which depends on the field settings.
		$this->paginate['order'] = array(Configure::read('accounts.fields.username') => 'asc');
	}

	function index() {
		$users = $this->paginate($this->modelName);
		$this->set(compact('accounts'));
		$this->set('title_for_layout', __("Manage Accounts", true));
	}

	function sign_up() {
		if (!empty($this->data)) {
			$this->User->create();
			$this->User->accountsMode = true;
			if ($this->User->save($this->data, true, array(
				Configure::read('accounts.fields.username'),
				Configure::read('accounts.fields.email'),
				'new_password',
				'confirm_password'
			))) {
				// Send activation email.
				$this->send_activation_email($this->data[$this->modelName][Configure::read('accounts.fields.username')]);
				return;
			} else {
				$this->Session->setFlash(__("Please correct the below errors and try again.", true));
			}
		}
		$this->set('title_for_layout', __("Sign Up", true));
	}

	function send_activation_email($username = null) {
		if (!$username) {
			// Get username from the form instead.
			$username = $this->data[$this->modelName][Configure::read('accounts.fields.username')];
		} else {
			// Fill in the username field in case there are errors to be corrected.
			$this->data[$this->modelName][Configure::read('accounts.fields.username')] = $username;
		}
		$this->set('title_for_layout', __("Send Me An Activation Email", true));
		if ($username) {
			$code = $this->User->generateActivationCode($username);
			if ($code) {
				$email = $this->User->findEmailFromUsername($username);
				if ($this->Accounts->sendActivationEmail($email, $username, $code)) {
					$this->set('title_for_layout', __("Activation Email Sent", true));
					$this->render('send_activation_email_success');
				} else {
					$this->Session->setFlash(__("Sorry, there was a problem sending you an activation email.", true));
				}
			} else {
				$this->Session->setFlash(__("This " . Inflector::humanize(Configure::read('accounts.fields.username')) . " isn't signed up.  Please check your spelling, or sign up if you haven't already.", true));
			}
		}
	}

	function activate($username = null, $code = null) {
		if (!$username || !$code) {
			$this->Session->setFlash(__(Inflector::humanize(Configure::read('accounts.fields.username')) . " or activation code missing.  Please check that you are visiting the link exactly as it is in your email.", true));
			$this->redirect('/');
		}
		if ($this->User->activate($username, $code)) {
			$user = $this->User->findByIdOrUsername($username);
			$this->Accounts->manualLogin($user);
			$this->Session->setFlash(__("Account successfully activated and you are now logged in.", true));
			$this->redirect(Configure::read('accounts.redirectAfterActivation'));
		} else {
			$this->Session->setFlash(__($this->User->validationErrors['_activate'], true));
		}
	}

	function send_reset_password_email($username = null) {
		if (!$username) {
			$username = $this->data[$this->modelName][Configure::read('accounts.fields.username')];
		} else {
			$this->data[$this->modelName][Configure::read('accounts.fields.username')] = $email;
		}
		if ($username) {
			$this->set('title_for_layout', __("Reset Password", true));
			$code = $this->User->generateResetPasswordCode($username);
			if ($code) {
				$email = $this->User->findEmailFromUsername($username);
				if ($this->Accounts->sendResetPasswordEmail($email, $username, $code)) {
					$this->Session->setFlash(__("Reset password email sent.", true));
					$this->render('send_reset_password_email_success');
				} else {
					$this->Session->setFlash(__("Sorry, there was a problem sending you a reset password email.", true));
				}
			} else {
				$this->Session->setFlash(__("This " . Inflector::humanize(Configure::read('accounts.fields.username')) . " isn't signed up.  Please check your spelling, or sign up if you haven't already.", true));
			}
		}
	}

	function reset_password($username = null, $code = null) {
		if (!$username || !$code) {
			$this->Session->setFlash(__(Inflector::humanize(Configure::read('accounts.fields.username')) . " or reset password code missing.  Please check that you are visiting the link exactly as it is in your email.", true));
			$this->redirect('/');
		}
		if ($code == $this->User->generateResetPasswordCode($username)) {
			if (!empty($this->data)) {
				if ($this->User->updateByUsername($username, $this->data, false, array(
					'new_password',
					'confirm_password'
				))) {
					$this->Session->setFlash(__("Password changed.", true));
					$this->redirect(Configure::read('accounts.redirectAfterResetPassword'));
				} else {
					$this->Session->setFlash(__("There were problems changing your password.  Please correct the errors below and try again.", true));
				}
			}
		} else {
			$this->Session->setFlash(__(Inflector::humanize(Configure::read('accounts.fields.username')) . " or reset password code incorrect.  Please check that you are visiting the link exactly as it is in your email.", true));
		}
		$this->set('title_for_layout', __("Reset Password", true));
		$this->set(compact('username', 'code'));
	}

	function login() {
		Login::set($this->Auth->user());
		if (Login::exists() && !empty($this->data)) {
			Login::justLoggedIn(true);
			$this->Accounts->rememberMe();
			$this->redirect($this->Auth->loginRedirect);
		}
	}

	function logout() {
		$this->Cookie->destroy('rememberMe');
		$this->redirect($this->Auth->logout());
	}

	function edit($id = null) {
		if (!$id && $this->data[$this->modelName]['id']) {
			$this->Session->setFlash(__("No account specified.", true));
			$this->redirect('/');
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__("Account does not exist.", true));
			$this->redirect('/');
		}
		if (!empty($this->data)) {
			if (empty($this->data[$this->modelName]['new_password'])) {
				unset($this->data[$this->modelName]['old_password']);
				unset($this->data[$this->modelName]['new_password']);
				unset($this->data[$this->modelName]['confirm_password']);
			}
			$this->User->accountsMode = true;
			if ($this->User->save($this->data, true, array(
				// Specify fields.
				Configure::read('accounts.fields.username'),
				Configure::read('accounts.fields.email'),
				'new_password'
			))) {
				// Update session with manual login and redirect.
				$this->Session->setFlash(__("Changes saved.", true));
				$user = $this->User->read();
				$this->Accounts->manualLogin($user);
				$this->redirect(Configure::read('accounts.redirectAfterEdit'));
			} else {
				$this->Session->setFlash(__("There were problems saving your changes.  Please correct the errors below and try again.", true));
			}
		} else {
			$this->data = $this->User->read(null, $id);
			unset($this->data[$this->modelName][Configure::read('accounts.fields.password')]);
		}
		$this->set('title_for_layout', __("Edit Account", true));
	}

}
?>
