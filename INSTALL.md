QIIMEIntegration - Installation
===============================
Copyright (C) 2014 Aaron Sharp
Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

### 1) Select a server
* This application was developed on Mac OS X, version 10.6.8, and has been tested on Mac OS X version 10.9.4
* This application was developed on Apache/2.2.24 (Unix), and has been tested on Apache/2.2.26 (Unix)
* As with any web server, the machine you use must have a static IP address/domain
* Because this application allows some write access to the server file system, we recommend choosing a computer that is carefully monitored, kept up to date, and otherwise well taken care of. While reasonable effort has been made to ensure security within the application, we recommend additional caution while the program is live

### 2) Clone git repository
The code for this program is freely available in the GitHub repository, chnops/QIIMEIntegration. In order to begin your own instance, choose a location on your server, navigate to that directory, then simply clone the repository

	cd <QIIMEIntegration_HOME>
	git clone git@github.com:chnops/QIIMEIntegration.git

If you would like to modify this application to better suit your own needs, you are welcome to do so. This can be done by creating a Fork of the GitHub repository, and cloning that instead of the original

There is one modification you will have to make before running QIIMEIntegration. While all files will be readable to the server software (Apache), the data and project directories have to be reassigned ownership so that they are writeable as well. For example:

	cd <QIIMEIntegration_HOME>/QIIMEIntegration
	sudo chown <SERVER_USER>:<SERVER_GROUP> data/
	sudo chown <SERVER_USER>:<SERVER_GROUP> projects/

The Mac OS X default value for both &lt;SERVER_USER&gt; and &lt;SERVER_GROUP&gt; is '_www'

### 3) Install dependencies
Once QIIMEIntegration is cloned onto your machine, it is time to configure your machine to run it. QIIMEIntegration ships with a full suite of unit tests, which you can run with the following set of commands:

	cd <QIIMEIntegration_HOME>/QIIMEIntegration/test_environment/tests/
	phpunit -c allTests.xml

Once all the tests pass (except for the ones that are marked incomplete), step 3 is complete.

Here are the dependencies you will have to install (if they are not installed already):

* PHPUnit version 4.1.0
	wget https://phar.phpunit.de/phpunit.phar
	chmod +x phpunit.phar
	mv phpunit.phar /usr/local/bin/phpunit
* PHP version 5.3.26 or 5.4.24
* SQLite version 3.8.4.2
	* Download from http://www.sqlite.org/download.html and install
* MacQIIME version 1.8.0
	* The default install is available from http://www.wernerlab.org/software/macqiime
	* The extension fastq-join is available from http://www.wernerlab.org/software/macqiime/add-fastq-join-to-macqiime-1-8-0/
	* You must also add the UNITe fungal ITS database with the following code:

		wget http://unite.ut.ee/sh_files/sh_qiime_release_04.07.2014.zip
		unzip sh_qiime_release_04.07.20214.zip
		mv sh_qiime_release_04.07.2014 /macqiime/UNITe

### 4) Setup apache
This step is potentially the most difficult, and the most important for the security of your QIIMEIntegration instance.
Ideally, therefore, this application would be set up by someone with a working knowledge of Apache and the current/desired configuration of the server. For other users, here is a brief crash course in setting up Apache.

Apache is server software. It sits on your hardware and manages communication between yours and other machines. It speaks protocols such as HTTP, so it understands when a separate machine sends it a GET or POST request generated, for example, by an HTML form. Also, it is a PHP language interpreter.
Its behavior is governed by directives stored in a file called httpd.conf, usually within the directory /etc/apache2. A directive is a one-word identifier, followed by one or more values. For example, 

	Port 80

tells Apache to listen on port 80, the typical port for web servers.

Many of the Mac OS X default directives will work, but there are too many, making the server inefficient and insecure. I recommend creating a new httpd.conf file. The following directives can be copied over verbatim from the Mac default:

	ServerRoot "/usr"
	Listen 80
	User _www
	Group _www
	ErrorLog "/private/var/log/apache2/error_log"
	LogLevel warn
	LogFormat "%h %l %u %t \"%r\" %>s %b" common
	CustomLog "/private/var/log/apache2/access_log" common
	TypesConfig /private/etc/apache2/mime.types
	AddType application/x-compress .Z
	AddType application/x-gzip .gz .tgz
	AddType application/x-httpd-php .php

The Mac OS X version will also have a long list of LoadModule directives. Each of these allows Apache to use additional directives. You will most likely only need the following:

	LoadModule php5_module libexec/apache2/libphp5.so
	LoadModule dir_module libexec/apache2/mod_dir.so
	LoadModule autoindex_module libexec/apache2/mod_autoindex.so
	LoadModule log_config_module libexec/apache2/mod_log_config.so
	LoadModule mime_module libexec/apache2/mod_mime.so

Because many servers actually run more than one website/application, it is wise to make QIIMEIntegration a virtual host, i.e. one of potentially many "hosts" (sites or applications) run by the server. To do this you will need the following directives:

	NameVirtualHost *:80
	# Says any IP address owned be this machine, and any requests that are picked up on port 80, will be handled by virtual hosts
	<VirtualHost *:80>
		# Your server can run as many virtual hosts as it has domain names assigned to it. Each domain name corresponds to a ServerName directive
		ServerName <QIIME_DOMAIN_NAME>

		# Directives located in this area will only apply to requests intended for QIIMEIntegration
	</VirtualHost>

Here are some important directories to put in your virtual host block:

	DocumentRoot "<QIIMEIntegration_HOME>/QIIMEIntegration/webapp"
	DirectoryIndex index.php
	ServerAdmin "<YOUR_EMAIL@SOMETHING.COM>"

	DefaultType text/plain
	AddDefaultCharset UTF-8
	TraceEnable off

And this would be a good way to format your 'DocumentRoot' directory (still in the VirtualHost block):  

	# here / refers to 'DocumentRoot' that you set earlier, not to the root directory of your server machine
	<Directory />
		# The next two directives allows users to look at directory indexes, for example, <QIIMEIntegration_HOME>/QIIMEIntegration/webapp/manual
		Options Indexes 
		IndexOptions FancyIndexing HTMLTable VersionSort

		AllowOverride None

		# The remaining options restrict access to your server, which we highly recommend doing
		Order Deny,Allow
		Deny from all
		# Only allow access from machines that you trust
		Allow from localhost
		# Allow from <OTHER_TRUSTED_IP_ADDRESS>
	</Directory>

You may want to enable password protection of your application (and we recommend doing so).  There are more secure ways that this, but here is one example of how that would be done:

	# This should be in the same <Directory /> block as above
	<Directory />
		AuthType Basic
		AuthName "Restricted Files"
		AuthBasicProvider file
		AuthUserFile <PASSWORD_FILE>
		Require user <AUTHORIZED_USER>
	</Directory>

You will have to create &lt;PASSWORD_FILE&gt; with the following commands in the terminal:

	htpasswd -c <PASSWORD_FILE> <AUTHORIZED_USER> 
	# You will be prompted to enter a password

If you use this method of password protection, you will need to add the following 'LoadModule' directives (outside the &lt;VirtualHost&gt; block):

	LoadModule authz_host_module libexec/apache2/mod_authz_host.so
	LoadModule auth_basic_module libexec/apache2/mod_auth_basic.so
	LoadModule authn_file_module libexec/apache2/mod_authn_file.so
	LoadModule authz_user_module libexec/apache2/mod_authz_user.so

### 5) Start apache
Once you are confident that apache is configured correctly, and QIIMEIntegration is passing all tests, it is time to start running the program live. The changes you made to httpd.conf will not go into effect until you start/restart Apache

	sudo apachectl configtest
	# Syntax OK
	sudo apachectl start
	# or
	sudo apachectl restart

QIIMEIntegration can now be accessed by pointing your browser to the domain name of your server. It would still be a good idea to check on other computers and make sure access rules are configured the way you want them
