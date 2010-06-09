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

	var $helpers = array('Html', 'Form', 'Paginator');

	var $paginate = array(
		'limit' => 10,
		'order' => array('Account.email' => 'asc')
	);

	function beforeFilter() {
		if (!is_a($this->Accounts, 'AccountsComponent')) {
			trigger_error("Accounts.Accounts component not loaded in controller.  Try putting something like \"var \$components = array('Session', 'Auth', 'Email', 'Accounts.Accounts');\" in app/app_controller.php.", E_USER_ERROR);
		}
		parent::beforeFilter();
		$this->Auth->allow('signUp', 'resetPassword', 'activate', 'sendActivationEmail', 'sendResetPasswordEmail');
	}

	function index() {
		$accounts = $this->paginate('Account');
		$this->set(compact('accounts'));
		$this->set('title_for_layout', __("Manage Accounts", true));
	}

	function signUp() {
		if (!empty($this->data)) {
			$this->Account->create();
			if ($this->Account->save($this->data)) {
				$this->Accounts->sendActivationEmail();
				$this->redirect(array('action' => 'activate'));
			} else {
				$this->Session->setFlash(__("Please correct the below errors and try again.", true));
			}
		}
		$this->set('title_for_layout', __("Sign Up", true));
	}

	function sendActivationEmail($email = null) {
		if (!$email) {
			$email = $this->data['Account']['email'];
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
		}
		if ($this->Account->activate($email, $code)) {
			$this->Session->setFlash(__("Account successfully activated.", true));
//			$this->redirect(??);
		} else {
			$this->Session->setFlash(__($this->Account->validationErrors['_activate'], true));
		}
	}

	function sendPasswordResetEmail() {

	}

	function login() {}

	function logout() {
		$this->redirect($this->Auth->logout());
	}

	function changePassword($id = null) {
		if (!$id && $this->data['Account']['id']) {
			$this->Session->setFlash(__("No account specified.", true));
			$this->redirect('/');
		}
		if (!empty($this->data)) {
			if ($this->Account->save($this->data)) {
				$this->Session->setFlash(__("Your changes to your account have been saved.", true));
			} else {
				$this->Session->setFlash(__("There was a problem saving your changes, please correct the errors below and try again.", true));
			}
		} else {
			$this->Account->id = $id;
			if (!$this->Account->exists()) {
				$this->Session->setFlash(__("Account does not exist.", true));
				$this->redirect('/');
			}
			$this->data['Account']['id'] = $id;
		}
		$this->set('title_for_layout', __("Edit Account", true));
	}

}
?>
