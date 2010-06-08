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
