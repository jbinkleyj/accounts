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

	var $name = 'Accounts';
	var $modelName;

	var $helpers = array('Html', 'Form', 'Paginator');

	var $paginate = array(
		'limit' => 10,
		'order' => array('email' => 'asc')
	);

	function beforeFilter() {
		if (!is_a($this->Accounts, 'AccountsComponent')) {
			trigger_error("Accounts.Accounts component not loaded in controller.  Try putting something like \"var \$components = array('Session', 'Auth', 'Email', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		parent::beforeFilter();
		$this->Auth->allow('sign_up', 'reset_password', 'activate', 'send_activation_email', 'send_reset_password_email', 'login', 'logout');
		$this->modelName = $this->Accounts->settings['modelName'];
	}

	function index() {
		$accounts = $this->paginate($this->modelName);
		$this->set(compact('accounts'));
		$this->set('title_for_layout', __("Manage Accounts", true));
	}

	function sign_up() {
		if (!empty($this->data)) {
			$this->Account->create();
			if ($this->Account->save($this->data)) {
				// Send activation email.
				$this->send_activation_email($this->data[$this->modelName]['email']);
				return;
			} else {
				$this->Session->setFlash(__("Please correct the below errors and try again.", true));
			}
		}
		$this->set('title_for_layout', __("Sign Up", true));
	}

	function send_activation_email($email = null) {
		if (!$email) {
			// Get email from the form instead.
			$email = $this->data[$this->modelName]['email'];
		} else {
			// Fill in the email field in case there are errors to be corrected.
			$this->data[$this->modelName]['email'] = $email;
		}
		$this->set('title_for_layout', __("Send Me An Activation Email", true));
		if ($email) {
			$code = $this->Account->generateActivationCode($email);
			if ($code) {
				if ($this->Accounts->sendActivationEmail($email, $code)) {
					$this->set('title_for_layout', __("Activation Email Sent", true));
					$this->render('send_activation_email_success');
				} else {
					$this->Session->setFlash(__("Sorry, there was a problem sending you an activation email.", true));
				}
			} else {
				$this->Session->setFlash(__("This email address isn't signed up.  Please check your spelling, or sign up if you haven't already.", true));
			}
		}
	}

	function activate($email = null, $code = null) {
		if (!$email || !$code) {
			$this->Session->setFlash(__("Email or activation code missing.  Please check that you are visiting the link exactly as it is in your email.", true));
			$this->redirect('/');
		}
		if ($this->Account->activate($email, $code)) {
			$account = $this->Account->findByEmail($email);
			$this->Accounts->manualLogin($account);
			$this->Session->setFlash(__("Account successfully activated and you are now logged in.", true));
			$this->redirect($this->Accounts->settings['redirectAfterActivation']);
		} else {
			$this->Session->setFlash(__($this->Account->validationErrors['_activate'], true));
		}
	}

	function send_reset_password_email($email = null) {
		if (!$email) {
			$email = $this->data[$this->modelName]['email'];
		} else {
			$this->data[$this->modelName]['email'] = $email;
		}
		if ($email) {
			$this->set('title_for_layout', __("Reset Password", true));
			$code = $this->Account->generateResetPasswordCode($email);
			if ($code) {
				if ($this->Accounts->sendResetPasswordEmail($email, $code)) {
					$this->Session->setFlash(__("Reset password email sent.", true));
					$this->render('send_reset_password_email_success');
				} else {
					$this->Session->setFlash(__("Sorry, there was a problem sending you a reset password email.", true));
				}
			} else {
				$this->Session->setFlash(__("This email address isn't signed up.  Please check your spelling, or sign up if you haven't already.", true));
			}
		}
	}

	function reset_password($email = null, $code = null) {
		if (!$email || !$code) {
			$this->Session->setFlash(__("Email or reset password code missing.  Please check that you are visiting the link exactly as it is in your email.", true));
			$this->redirect('/');
		}
		if ($code == $this->Account->generateResetPasswordCode($email)) {
			if (!empty($this->data)) {
				if ($this->Account->updateByEmail($email, $this->data)) {
					$this->Session->setFlash(__("Password changed.", true));
					$this->redirect($this->Accounts->settings['redirectAfterResetPassword']);
				} else {
					$this->Session->setFlash(__("There were problems changing your password.  Please correct the errors below and try again.", true));
				}
			}
		} else {
			$this->Session->setFlash(__("Email or reset password code incorrect.  Please check that you are visiting the link exactly as it is in your email.", true));
		}
		$this->set('title_for_layout', __("Reset Password", true));
		$this->set(compact('email', 'code'));
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
		$this->Account->id = $id;
		if (!$this->Account->exists()) {
			$this->Session->setFlash(__("Account does not exist.", true));
			$this->redirect('/');
		}
		if (!empty($this->data)) {
			if (empty($this->data[$this->modelName]['new_password'])) {
				unset($this->data[$this->modelName]['old_password']);
				unset($this->data[$this->modelName]['new_password']);
				unset($this->data[$this->modelName]['confirm_password']);
			}
			if ($this->Account->save($this->data)) {
				$this->Session->setFlash(__("Changes saved.", true));
				$this->redirect($this->Accounts->settings['redirectAfterEdit']);
			} else {
				$this->Session->setFlash(__("There were problems saving your changes.  Please correct the errors below and try again.", true));
			}
		} else {
			$this->data = $this->{$this->modelName}->read(null, $id);
			unset($this->data[$this->modelName]['password']);
		}
		$this->set('title_for_layout', __("Edit Account", true));
	}

}
?>
