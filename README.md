QIIMEIntegration webapp
=======================
Copyright (C) 2014 Aaron Sharp <br/>
Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

Program overview
----------------
QIIME is an open source software package that is useful for microbial ecologists working with next generation sequencing data. This program, QIIMEIntegration, is an interface for QIIME; it makes QIIME easier to learn and use.
The user can log on and create "projects", upload their raw data files, and then run steps of the QIIME pathway on a remote machine.  All the while this application provides helpful notes and tips about QIIME usage.
While easy to use, setup of this program is involved, and requires administrative privelages.  Therefore, this program is best used by research group leaders--or their computer savvy friends--who want to provide group members easy access to QIIME.

Contents of this directory
--------------------------
###files
* README.md - This file
* program_overview.png - A high-level diagram of the code structure
* INSTALL.mds - Instructions for deploying this application on a server
* LICENSE.txt - A copy of the GNU general public license, version 3

###directories
* includes/ - Contains almost all of the code, mostly as PHP class files.  Also contains the README that describes the inner workings of the project
* test_environment/ - Contains a suite of unit tests.  Also contains a README that explains test layout
* data/ - Contains database and its accompanying schema file
* projects/ - Contains all of the project data files, uploaded or generated.  The layout of this folder is as follows:
	* Each user has a folder: u1/, u2/, u3/...
	* Each project has a folder: u1/p1/, u1/p2/, u2/p1/...
	* Each project folder has an uploads folder: u1/p1/uploads/, u1/p2/uploads/, u2/p1/uploads/...
	* Each time a script is run, a new folder is created: /u1/p1/r1/, u1/p2/r2, /u2/p1/r3/...
* webapp/ - The server document root; contains all files that are meant to be accessed directly by the user
	* index.php - The main file that runs the whole application when requested on the server
	* download.php - A PHP script that downloads one of the generated or uploaded data files
	* style.css - Most of the CSS styling directives
	* javascript.js - A set of JavaScript functions used on multiple pages of the application
	* parameter_relationships.js - A set of JavaScript functions used on only one page of the application
	* help/ - Individual files for each parameter, which contain a short description and help text
	* manual/ - The --help text of each script, unmodified from QIIME
	* versions/ - The version information for each of the QIIME scripts

Best practice
-------------
* This is a powerful application, which brings the user very close to the server file system.  While much has been done to ensure security, there is still a chance it could be used incorrectly.  We recommend carefully controlling access to this application.  More on this in INSTALL.md
* Because logging on in this application is not designed for security purposes (i.e. there is no password or session timeout), it is important that users know to actively log off when they are finished
* It is also critical that users agree not to interfere with the work of others

Future functionality
--------------------
* Instead of hard-coding of QIIME scripts/parameters into PHP, we should use a separate code/implementation-agnostic file
* Remove the OperatingSystem branch of the program.  Instead of having it run QIIME for the user on the server, let it print out the code that can be copied and pasted into the terminal
* Provide utilities for tracking past runs (the code that would be copied and pasted).  Let users save and name individual runs/sequences of runs for reuse with or without modification
* Create separete files for other useful programs, so that this application can teach lab members how to use, for example, BLAST, uclust, PyNAST, the tuxedo suite, etc.
