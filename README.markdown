# Accounts

## Introduction

Status: usable, but needs testing and some methods may change in the near future.

A CakePHP plugin for user login.  It uses cake's native auth to setup an environment ready for users to sign up, reset password, remember me and the like with as little effort from you as possible (while being flexible and easy to extend).

### Testing

Please help me test and improve the Accounts plugin!

## Installation

### Download

	cd app/plugins/
	git clone git://github.com/adrianedworthy/accounts.git

### Execute Sql

Execute accounts/config/users.sql in MySQL.

### In /app/app_controller.php

	<?php
	class AppController extends Controller {

		var $components = array(
			'Auth', 'Email', 'Session', 'Cookie', 'Accounts.Accounts'
		);

		function beforeFilter() {
			$this->Accounts->setupAuth();
		}

	}
	?>

### In /app/views/layout/default.ctp

Put this inside the head tag:

	echo $html->css('/accounts/css/accounts');

Put this just under the header div:

	<?php echo $this->element('login', array('plugin' => 'Accounts')); ?>

### That's it!

You're done.  Try signing up.

If you have any problems, try the troubleshooting section below.

## Configuration

### Changing Config Options

You'll find the config file in accounts/config/accounts.php.  When you want to override a value, simply configure it in your app/config/bootstrap.php like so:

	Configure::write('accounts.emailFrom', 'foo@bar.com');

### Logging in with a username

So you want to login with a username instead of an email address?  Simply add the 'username' column to your table and in app/config/bootstrap.php:

	Configure::write('accounts.fields.username', 'username');

### User Model

Accounts uses a model called 'User'.  Since this doesn't exist, cake creates it on the fly and then Accounts adds the behavior.  If you need to add functionality or validation to the model, simply create a model by the same name in your app.

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

* Ensure required fields are illustrated correctly
* Successfully signed up email
* Preserve any form submissions that prompted login (but do not post!)
* Ajax login
* Facebook connect
* enableWhatever configuration options

* Possibly add ACL management?
