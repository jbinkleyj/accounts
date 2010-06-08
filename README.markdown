# Accounts

## Introduction

Version: 0.1 (not ready to use yet)

A CakePHP plugin for user login.  Uses cake's native auth and is very easy to setup.

## Installation

### Download

	cd app/plugins/
	git clone git://github.com/adrianedworthy/accounts.git

### Execute Sql

Execute accounts/config/accounts.sql in MySQL.

### In /app/app_controller.php

	<?php
	class AppController extends Controller {

		var $components = array('Session', 'Auth', 'Email', 'Accounts.Accounts' => array('emailFrom' => 'noreply@example.com'));

		function beforeFilter() {
			$this->Accounts->setupAuth();
		}

	}
	?>

### In /app/views/layout/default.ctp

Put this inside the head tag:

	echo $html->css('/accounts/css/login');

Put this just under the header div:

	<?php echo $this->element('login', array('plugin' => 'Accounts')); ?>
