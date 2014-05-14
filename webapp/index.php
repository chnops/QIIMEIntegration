<?php
// setup $_SESSION
session_start();

// Set up environment so that any file can access classes in the directory ./include
set_include_path(get_include_path() . "./includes/:");
spl_autoload_register(function($class) {
	require_once("./includes/" . str_replace("\\", "/", $class) . ".php");
});

// Run the application
$database = new \Database\PDODatabase();
$operatingSystem = new \Models\MacOperatingSystem();
$workflow = new \Models\QIIMEWorkflow($operatingSystem);
$indexController = new \Controllers\IndexController($database, $workflow);
$indexController->run();
