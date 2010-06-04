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
class Account extends AccountsAppModel {

	var $name = 'Account';
	var $actsAs = 'Accounts.Accountable';

	var $validate = array(
		'email' => array(
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
			'message'=> "Your password must be between 6 and 100 characters long."
		),
		'confirm_password' => array(
			'rule' => array('match', 'new_password'),
			'message' => "You must enter exactly the same password twice."
		)
	);

	function beforeSave() {
		// Hash and prepare new password for saving.
		$newPassword =& $this->data[$this->name]['new_password'];
		if (!empty($newPassword)) {
			$this->data[$this->name]['password'] = Security::hash(Configure::read('Security.salt') . $newPassword);
		}
		return true;
	}

	function match($data, $targetField, $rule) {
        if (current($data) == $this->data[$this->name][$targetField]) {
            return true;
        }
	}

}
?>
