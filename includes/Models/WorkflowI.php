<?php

namespace Models;

interface WorkflowI {
	public function __construct(\Database\DatabaseI $database, OperatingSystemI $operatingSystem);
	public function getSteps();
	public function getCurrentStep($controller);
	public function getController($step);

	public function getNewProject();
	public function findProject($username, $projectId);
	public function getAllProjects($username);
}
