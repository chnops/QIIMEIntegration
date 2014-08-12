QIIMEIntegration - Installation
===============================
Copyright (C) 2014 Aaron Sharp
Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

### 1) Select a server
* This application was developed on Mac OS X, version 10.6.8, and has been tested on Mac OS X version &&&
* This application was developed on Apache/2.2.24 (Unix), and has been tested on Apache &&&
* As with any web server, the machine you use must have a static IP address/domain
* Because this application allows some write access to the server file system, we recommend choosing a computer that is carefully monitored, kept up to date, and otherwise well taken care of.  While reasonable effort has been made to ensure security within the application, we recommend additional caution while the program is live

### 2) Clone git repository
The code for this program is freely available in the GitHub repository, chnops/QIIMEIntegration.  In order to begin your own instance, choose a location on your server, navigate to that directory, then simply clone the repository

	cd <QIIME_HOME>
	git clone git@github.com:chnops/QIIMEIntegration.git

If you would like to modify this application to better suit your own needs, you are welcome to do so. This can be done by creating a Fork of the GitHub repository, and cloning that instead of the original

### 3) Install dependencies
Once QIIMEIntegration is cloned onto your machine, it is time to configure your machine to run it. QIIMEIntegration ships with a full suite of unit tests, which you can run with the following set of commands:

	cd <QIIME_HOME>/test_environment/tests/
	phpunit -c allTests.xml

Once all the tests pass, this step is complete. Depending on what is already installed on your server, you may have to add the following dependencies (or they may already be installed):

* PHPUnit version 4.1.0 or &&&
* PHP version 5.3.26 or &&&
* SQLite version 3.8.4.2 or &&&
* MacQIIME version 1.8.0
	* QIIMEIntegration was developed and tested with the default install, available from http://www.wernerlab.org/software/macqiime
	* The only additional configuration MacQIIME requires is installing fastq-join, which is available from http://www.wernerlab.org/software/macqiime/add-fastq-join-to-macqiime-1-8-0/
	* Add UNITe &&&
	* Make sure /macqiime/configs/bash_profile.txt is in the correct location

### 4) Setup apach
This is potentially the most difficult, and the most important for the security of your QIIMEIntegration instance.
Apache takes its configuration from a file called httpd.conf, usually stored in the directory /etc/apache2.
For QIIMEIntegration to run correctly, you must edit that file.
If the machine you are using is already a server:

	do this

otherwise:

	do that

### 5) Start apache
Once you are confident that apache is configured correctly, and QIIMEIntegration is passing all tests, it is time to start running the program live

	sudo apachectl configtest
	# Syntax OK
	sudo apachectl start

QIIMEIntegration can now be accessed by pointing your browser to the domain name of your server. It would still be a good idea to check on other computers and make sure access rules are configured the way you want them
