<?php
require_once './includes/setup.php';

// Initialize system defaults
$operatingSystem = new \Models\MacOperatingSystem();
$database = new \Database\PDODatabase($operatingSystem);
$workflow = new \Models\QIIMEWorkflow($database, $operatingSystem);

$roster = new \Utils\Roster($database, $operatingSystem);
\Utils\Roster::setDefaultRoster($roster);

// Run the application
$indexController = new \Controllers\IndexController($workflow);
$indexController->run();
