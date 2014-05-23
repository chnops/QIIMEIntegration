<?php
require_once './includes/setup.php';

// Run the application
$operatingSystem = new \Models\MacOperatingSystem();
$database = new \Database\PDODatabase($operatingSystem);
$workflow = new \Models\QIIMEWorkflow($database, $operatingSystem);
$indexController = new \Controllers\IndexController($database, $workflow);
$indexController->run();
