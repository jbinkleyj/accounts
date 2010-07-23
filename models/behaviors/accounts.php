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
class AccountsBehavior extends ModelBehavior {

	// These are added to the model before validating if validation doesn't
	// already exists for the field.  To override, simply perform validation in
	// your model.
	var $__validateDefaults = array(
		'username' => array(
			'between' => array(
				'rule' => array('between', 4, 50),
				'message' => "Please enter a username between 4 and 50 characters long."
			),
			'content' => array(
				'rule' => '/^[\\d\\w]*[a-zA-Z][\\d\\w]*$/',
				'message' => "You may only make a username out of letters, numbers and underscores.  Furthermore your username must have at least one letter in it."
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => "That username is already registered.",
			)
		),
		'email' => array(
			'required' => true,
			'email' => array(
				'rule' => array('email', true),
				'message' => "Please supply a valid email address.",
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => "That email address is already registered.",
			)
		),
		'new_password' => array(
			'rule' => array('between', 6, 100),
			'message'=> "Please enter a password between 6 and 100 characters long.",
		)
	);
	var $__validateBackup = null;

	function setup(&$model) {
		$fields = Configure::read('accounts.fields');
		foreach ($this->__validateDefaults as $alias => $validation) {
			if (($fields['username'] != $fields['email']) || ($alias != 'username')) {
				// If the field name is changeable in config, check the config to
				// make sure we're copying the validation rules to the right fields.
				if (isset($fields[$alias])) {
					$field = $fields[$alias];
				} else {
					$field = $alias;
				}
				// If the validation rule doesn't already exist on the model, copy
				// it over.
				if (!isset($model->validate[$field]) && isset($this->__validateDefaults[$alias])) {
					$model->validate[$field] = $this->__validateDefaults[$alias];
				}
			}
		}
	}

	function beforeValidate(&$model, $options) {
		// Backup validation array so we can make temporary changes.
		$this->__validateBackup = $model->validate;
		$fields = Configure::read('accounts.fields');
		$fields['new_password'] = 'new_password';
		$fields['old_password'] = 'old_password';
		$fields['confirm_password'] = 'confirm_password';
		// Stop the password being set directly.
		if (isset($model->data[$model->alias][$fields['password']]) || in_array($fields['password'], $options['fieldList'])) {
			trigger_error("You cannot set the password directly, you must set the 'new_password' field instead.", E_USER_WARNING);
			return false;
		}
		// Accounts mode prevents users injecting values into forms when
		// updating a user.  It also removes irrelevant validation so it doesn't
		// bug you with required fields and such.
		if (!empty($model->accountsMode)) {
			// In accounts mode, allow access only to accounts plugin fields.
			foreach ($model->validate as $k => $v) {
				// Disable irrelevant validation.
				if (!in_array($k, $fields) && isset($model->validate[$k])) {
					unset($model->validate[$k]);
				}
			}
			foreach ($model->data[$model->alias] as $k => $v) {
				// Error if field is set in model.
				if (!in_array($k, $fields) && isset($model->data[$model->alias][@$fields[$k]])) {
					trigger_error("You cannot set field '{$fields[$k]}' when the user model is in accounts mode, you may only set accounts plugin fields.  Use \$this->{$model->alias}->accountsMode = true;", E_USER_WARNING);
					return false;
				}
			}
		} else {
			// In normal mode, allow access only to other fields.
			foreach ($fields as $field) {
				// Disable irrelevant validation.
				if (isset($model->validate[$field])) {
					unset($model->validate[$field]);
				}
				// Error if field is set in model.
				if (isset($model->data[$model->alias][$field])) {
					trigger_error("You cannot set field '$field' when the user model is in accounts mode.  Use \$this->{$model->alias}->accountsMode = true;", E_USER_WARNING);
					return false;
				}
			}
		}
		// Validate password.
		$newPassword = @$model->data[$model->alias]['new_password'];
		if ($newPassword) {
			$confirmPassword = @$model->data[$model->alias]['confirm_password'];
			// If updating, make sure old password is given and is correct.
			if (!empty($model->data[$model->alias]['id'])) {
				$oldPassword = @$model->data[$model->alias]['old_password'];
				$correct = $model->find('count', array(
					'conditions' => array(
						$model->alias . '.id' => $model->data[$model->alias]['id'],
						$model->alias . '.' . Configure::read('accounts.fields.password') => Security::hash($oldPassword, null, true)
					)
				));
				if (!$correct) {
					$model->validationErrors['old_password'] = "You must enter your old password correctly.";
				}
			}
			// Check that passwords match.
			if ($newPassword != $confirmPassword) {
				$model->validationErrors['confirm_password'] = "You must enter exactly the same password twice.";
			}
		}
	}

	function afterValidate(&$model) {
		// Restore any backed up validation.
		if (!empty($this->__validateBackup)) {
			$model->validate = $this->__validateBackup;
		}
		// Reset accounts mode.
		$model->accountsMode = false;
	}

	function beforeSave(&$model) {
		// Hash and prepare new password for saving.
		$newPassword = @$model->data[$model->alias]['new_password'];
		if (!empty($newPassword)) {
			$model->data[$model->alias][Configure::read('accounts.fields.password')] = Security::hash($newPassword, null, true);
			if (!empty($model->whitelist)) {
				$model->whitelist[] = 'password';
			}
		}
		return true;
	}

	function updateByUsername(&$model, $username, $data, $validate = true, $fields = array()) {
		$id = $model->field('id', array(
			$model->alias . '.' . Configure::read('accounts.fields.username') => $username
		));
		$data[$model->alias]['id'] = $id;
		$fields[] = 'id';
		$model->accountsMode = true;
		return $model->save($data, $validate, $fields);
	}

	function findByIdOrUsername(&$model, $idOrUsername, $fields = array()) {
		if (is_numeric($idOrUsername)) {
			return $model->read($fields, $idOrUsername);
		} else {
			return $model->find('first', array(
				'conditions' => array(
					$model->alias . '.' . Configure::read('accounts.fields.username') => $idOrUsername
				),
				'fields' => $fields,
				'recursive' => -1,
				'contain' => false
			));
		}
	}

	function findEmailFromUsername(&$model, $username) {
		if (Configure::read('accounts.fields.username') == Configure::read('accounts.fields.email')) {
			return $username;
		} else {
			return $model->field(Configure::read('accounts.fields.email'), array(
				$model->alias . '.' . Configure::read('accounts.fields.username') => $username
			));
		}
	}

	function generateActivationCode(&$model, $idOrUsername = null) {
		$user = $model->findByIdOrUsername($idOrUsername, array(Configure::read('accounts.fields.username'), 'created'));
		if (!$user) {
			return false;
		}
		return Security::hash($user[$model->alias][Configure::read('accounts.fields.username')] . $user[$model->alias]['created'], null, true);
	}

	function activate(&$model, $username = null, $code = false) {
		// We need the id and created date to check the activation code.
		$user = $model->findByIdOrUsername($username);
		if (empty($user)) {
			$model->validationErrors['_activate'] = "Specified account does not exist.";
			return false;
		}
		// Some extra security.
		if ($user[$model->alias][Configure::read('accounts.fields.activated')]) {
			$model->validationErrors['_activate'] = "Your account has already been activated.";
			return false;
		}
		if ($user[$model->alias][Configure::read('accounts.fields.banned')]) {
			$model->validationErrors['_activate'] = "Your account has been banned.";
			return false;
		}
		// If the code matches the hash, activate the user.
		if ($code == Security::hash($user[$model->alias][Configure::read('accounts.fields.username')] . $user[$model->alias]['created'], null, true)) {
			$model->accountsMode = true;
			$result = $model->save(array($model->alias => array(
				'id' => $user[$model->alias]['id'],
				Configure::read('accounts.fields.activated') => true
			)), true, array('id', Configure::read('accounts.fields.activated')));
			if (!$result) {
				$model->validationErrors['_activate'] = "Could not activate account.";
				return false;
			}
			return true;
		} else {
			$model->validationErrors['_activate'] = "Activation code incorrect.";
			return false;
		}
	}

	function generateResetPasswordCode(&$model, $username) {
		$user = $model->findByIdOrUsername($username);
		if (!$user) {
			return false;
		}
		return Security::hash($user[$model->alias][Configure::read('accounts.fields.username')] . $user[$model->alias][Configure::read('accounts.fields.password')], null, true);
	}

	function ban(&$model, $id = null, $value = true) {
		// Ban $id.  Or unban if $value is false.
		if (isset($id)) {
			$model->id = $id;
		}
		$model->set(Configure::read('accounts.fields.banned'), $value);
		$model->accountsMode = true;
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
			$model->accountsMode = true;
			$result = $model->save(array($model->alias => array(
				'id' => Login::get('id'),
				Configure::read('accounts.fields.last_login') => date('Y-m-d h:i:s')
			)), true, array('id', Configure::read('accounts.fields.last_login')));
			if (!$result) {
				trigger_error("Could not update last login time.", E_USER_WARNING);
				return false;
			}
			return true;
		}
	}

}
?>
