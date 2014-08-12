QIIMEIntegration webapp
=======================
Copyright (C) 2014 Aaron Sharp
Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

Program overview:
----------------
QIIME is an open source software package that is useful for microbial ecologists working with next generation sequencing data. This program, QIIMEIntegration, is an interface for QIIME; it makes QIIME easier to learn and use.
The user can log on and create "projects", upload their raw data files, and then run steps of the QIIME pathway on a remote machine.  All the while this application provides helpful notes and tips about QIIME usage.
While easy to use, setup of this program is involved, and requires administrative privelages.  Therefore, this program is best used by research group leaders--or their computer savvy friends--who want to provide easy access to group members.

Contents of this directory:
---------------------------
###files
* README.md - this file
* index.php - The main file that runs the whole application when requested on the server
* download.php - A PHP script that downloads one of the generated or uploaded data files
* style.css - Most of the CSS styling directives
* javascript.js - A set of JavaScript functions used on multiple pages of the application
* parameter_relationships.js - A set of JavaScript functions used on only one page of the application

###directories
* public/ - Contains all files that could be downloaded as-is, with no PHP modifications, such as man pages for all QIIME scripts, version number for all QIIME scripts, and custom help text for all parameters
* includes/ - Contains most of the PHP code, mostly as PHP class files.  Also contains the README that describes the inner workings of the project
* tests/ - Contains a suite of unit tests.  Also contains a README that explains test layout
* data/ - Contains database and its accompanying schema file
* projects/ - Contains all of the project data files, uploaded or generated.  The layout of this folder is as follows:
	Each user has a folder: u1/, u2/, u3/...
	Each project has a folder: u1/p1/, u1/p2/, u2/p1/...
	Each project folder has an uploads folder: u1/p1/uploads/, u1/p2/uploads/, u2/p1/uploads/...
	Each time a script is run, a new folder is created: /u1/p1/r1/, u1/p2/r2, /u2/p1/r3/...

Outstanding issues:
------------------
* Convert hard-coding of QIIME scripts/parameters/relationships into text/json config files
* We should convert this into a training--not a doing it for you--utility
