# Accounts

Version: 0.1 (not ready to use yet)

A CakePHP plugin for user login.  Uses cake's native auth and is very easy to setup.

## Installation

Execute accounts/config/accounts.sql in MySQL.

In /app/app_controller.php:

<?php
class AppController extends Controller {

	var $components = array('Session', 'Auth', 'Email', 'Accounts.Accounts' => array('emailFrom' => 'noreply@wishclue.com'));

	function beforeFilter() {
		$this->Accounts->setupAuth();
	}

}
?>

Put this in /app/views/layout/default.ctp somewhere:

<?php echo $this->element('login', array('plugin' => 'Accounts')); ?>
