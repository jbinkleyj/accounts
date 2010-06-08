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

	function activate(&$model, $id = null) {
		// Ban $id.  Or unban if $value is false.
		if (isset($id)) {
			$model->id = $id;
		}
		$model->set('activated', true);
		if (!$model->save()) {
			trigger_error("Could not activate user.", E_USER_WARNING);
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
