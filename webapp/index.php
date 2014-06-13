<?php
require_once './includes/setup.php';

// Run the application
$operatingSystem = new \Models\MacOperatingSystem();
$database = new \Database\PDODatabase($operatingSystem);
$workflow = new \Models\QIIMEWorkflow($database, $operatingSystem);

$roster = new \Utils\Roster($database, $operatingSystem);
\Utils\Roster::setDefaultRoster($roster);

$indexController = new \Controllers\IndexController($workflow);
$indexController->run();
