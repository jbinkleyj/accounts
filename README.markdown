# Accounts

## Introduction

Version: 0.5 (usable, but needs testing)

A CakePHP plugin for user login.  Uses cake's native auth and is very easy to setup.

### Testing

Please help me test and improve the Accounts plugin!

### Why name the model 'Account'?

By default the plugin is designed to separate all the login functionality from any other user related data and logic.

## Installation

### Download

	cd app/plugins/
	git clone git://github.com/adrianedworthy/accounts.git

### Execute Sql

Execute accounts/config/accounts.sql in MySQL.

### In /app/app_controller.php

	<?php
	class AppController extends Controller {

		var $components = array(
			'Auth', 'Email', 'Session', 'Cookie',
			'Accounts.Accounts' => array('emailFrom' => 'noreply@example.com')
		);

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
* Configurable activated, banned etc. fields
* Rename login.css
* Views like baked views
* Allow user to change email
* Move login singleton out of vendors
* Changeable login field
* Changeable model (test)
* Do controller names like change_password instead of changePassword
* Preserve any form submissions that prompted login
* Ajax login
