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

	function activate() {

	}

	function sendActivationEmail() {

	}

	function sendPasswordResetEmail() {

	}

	function login() {}

	function logout() {
		$this->redirect($this->Auth->logout());
	}

}
?>
