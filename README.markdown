# Accounts

## Introduction

Version: 0.2 (not ready to use yet)

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

## Troubleshooting

### Emails not sending?

Try connecting to an SMTP server to send mail.  Put this in beforeFilter() in /app/app-controller.php and fill in your settings.

    $this->Email->smtpOptions = array(
		'host' => 'yoursmtpservershostname',
		'username' => 'yourusername',
		'password' => 'yourpassword',
        'port' => '25',
        'timeout' => '30',
        'client' => 'smtp_helo_hostname'
    );
	$this->Email->delivery = 'smtp';

## Todo

* Delete account
* Success and delete emails
* Configurable redirects
* Changeable login field
* Changeable model
* Preserve any form submissions that prompted login
* Ajax login
