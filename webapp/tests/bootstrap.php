<?php
ob_start();

// Set up environment so that any file can access classes in the directory ./include
set_include_path(get_include_path() . "../includes/:");
spl_autoload_register(function($class) {
	require_once("../includes/" . str_replace("\\", "/", $class) . ".php");
});
