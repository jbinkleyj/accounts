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
class AccountableBehavior extends ModelBehavior {

	var $_settings = array(

	);

	function setup(&$model, $settings) {
	}

	function resetPassword() {

	}

	function generateActivationCode(&$model, $id = null) {
		$account = $model->read(array('email', 'created'), $id);
		extract($account['Account']);
		return Security::hash(Configure::read('Security.salt') . $email . $created);
	}

	function activate(&$model, $email = null, $code = false) {
		// We need the id and created date to check the code.
		$account = $model->find('first', array(
			'fields' => array('id', 'activated', 'banned', 'created'),
			'conditions' => array($model->name . '.email' => $email),
			'recursive' => -1
		));
		if (empty($account)) {
			$model->validationErrors['_activate'] = "Specified account does not exist.";
			return false;
		}
		extract($account['Account']);
		// Some extra security.
		if ($activated) {
			$model->validationErrors['_activate'] = "Your account has already been activated.";
			return false;
		}
		if ($banned) {
			$model->validationErrors['_activate'] = "Your account has been banned.";
			return false;
		}
		// If the code matches the hash, activate the user.
		if ($code == Security::hash(Configure::read('Security.salt') . $email . $created)) {
			$model->id = $id;
			$model->set('activated', true);
			if (!$model->save()) {
				$model->validationErrors['_activate'] = "Could not activate account.";
				return false;
			}
			return true;
		} else {
			$model->validationErrors['_activate'] = "Activation code incorrect.";
			return false;
		}
	}

	function ban(&$model, $id = null, $value = true) {
		// Ban $id.  Or unban if $value is false.
		if (isset($id)) {
			$model->id = $id;
		}
		$model->set('banned', $value);
		if (!$model->save()) {
			trigger_error("Could not ban user.", E_USER_WARNING);
		}
	}

	function unban(&$model, $id = null) {
		// Unban $id.
		$this->ban($model, $id, false);
	}

	function updateLastLogin(&$model) {
		// Set last_login to now, if we're logged in.
		if (Login::exists()) {
			$model->id = Login::get('Account.id');
			$model->set('last_login', date('Y-m-d h:i:s'));
			if (!$model->save()) {
				trigger_error("Could not update last login time.", E_USER_WARNING);
			}
		}
	}

	function beforeSave(&$model) {
		// Hash and prepare new password for saving.
		$newPassword =& $model->data[$model->name]['new_password'];
		if (!empty($newPassword)) {
			$model->data[$model->name]['password'] = Security::hash(Configure::read('Security.salt') . $newPassword);
		}
		return true;
	}

	function oldPassword(&$model, $data) {
		return $model->find('count', array(
			'conditions' => array(
				$model->name . '.id' => $model->data[$model->name]['id'],
				$model->name . '.password' => Security::hash(Configure::read('Security.salt') . current($data))
			)
		));
	}

	function match(&$model, $data, $targetField, $rule) {
        if (current($data) == $model->data[$model->name][$targetField]) {
            return true;
        }
	}

}
?>
