<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */
error_log("boostrap");
ob_start();

// Set up environment so that any file can access classes in the directory ../../includes
set_include_path(get_include_path() . "../../includes/:");
spl_autoload_register(function($class) {
	require_once(str_replace("\\", "/", $class) . ".php");
});

system("sqlite3 ../data/database.sqlite < ../data/clean_schema.sql;
rm -rf ../projects/*;");
