<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

// setup $_SESSION
session_start();

// Set up environment so that any file can access classes in the directory ../includes
set_include_path(get_include_path() . "../includes/:");
spl_autoload_register(function($class) {
	require_once(str_replace("\\", "/", $class) . ".php");
});

// Make it so that a user can leave/log out, and the script will still run
ignore_user_abort(true);
set_time_limit(0);
