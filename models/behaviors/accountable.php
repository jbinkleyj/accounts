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

	function activate() {

	}

	function ban() {

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
