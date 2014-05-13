<?php
// setup $_SESSION
session_start();

// Set up environment so that any file can access classes in the directory ./include
set_include_path(get_include_path() . "./includes/:");
spl_autoload_register(function($class) {
	require_once("./includes/" . str_replace("\\", "/", $class) . ".php");
});

// Display the page that the user would like to see
$indexController = new IndexController();
$indexController->run();
