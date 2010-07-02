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
class Login {

	function &getInstance($data = null) {
		static $instance = array();
		// Create an instance.
		if (isset($data)) {
			$instance[0] =& $data;
		} else {
			if (!isset($instance)) {
				trigger_error(__("Account data must be passed to Login::set().", true), E_USER_WARNING);
				return false;
			}
		}
		return $instance[0];
	}

	function set($data) {
		// Called to instantiate an instance, $data typically would include
		// the contents of Auth::user().
		if (empty($data)) {
			return false;
		}
		Login::getInstance($data);
	}

	function get($path) {
		$data =& Login::getInstance();
		$path = str_replace('.', '/', $path);
		if (strpos($path, '/') === false) {
			$path = '/' . Configure::read('accounts.modelName') . '/' . $path;
		}
		if ($path{0} != '/') {
			// Must start with a /.
			$path = "/$path";
		}
		if ($value = Set::extract($path, $data)) {
			return $value[0];
		}
	}

	function justLoggedIn($value = null) {
		$data =& Login::getInstance();
		if ($value) {
			return $data['justLoggedIn'] = true;
		} else {
			return $data['justLoggedIn'];
		}
	}

	function exists() {
		$data =& Login::getInstance();
		return (BOOL) $data;
	}

}
?>
