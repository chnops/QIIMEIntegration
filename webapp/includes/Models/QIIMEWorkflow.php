<?php 

namespace Models;

class QIIMEWorkflow implements WorkflowI {

	private $database = NULL;
	private $operatingSystem = NULL;

	private $steps = array(
		"login" => "Login",	
		"select" => "Select/create project",
		"upload" => "Upload files",
		"run" => "Run scripts",
		"view" => "View results");

	public function getSteps() {
		return $this->steps;
	}

	public function __construct(\Database\DatabaseI $database, OperatingSystemI $operatingSystem) {
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
	}

	public function getNextStep($step) {
		$keys = array_keys($this->steps);
		$currentPosition = 0;
		$found = false;
		for (; $currentPosition < count($keys); $currentPosition++) {
			if ($step == $keys[$currentPosition]) {
				$found = true;
				break;
			}
		}
		if ($found) {
			if ($currentPosition == count($keys)-1) {
				return $keys[$currentPosition];
			}
			return $keys[$currentPosition + 1];
		}
		return $keys[0];
	}

	public function getPreviousStep($step) {
		$keys = array_keys($this->steps);
		$currentPosition = count($keys) - 1;
		$found = false;
		for (; $currentPosition >= 0; $currentPosition--) {
			if ($step == $keys[$currentPosition]) {
				$found = true;
				break;
			}
		}
		if ($found) {
			if ($currentPosition == 0) {
				return $keys[$currentPosition];
			}
			return $keys[$currentPosition - 1];
		}
		return $keys[count($keys)-1];
	}

	public function getCurrentStep($controller) {
		$objectName = get_class($controller);
		switch ($objectName) {
			case "Controllers\\TestController":
				return "test";
			case "Controllers\\Controller":
				return "login";
			case "Controllers\\IndexController":
				return "login";
			case "Controllers\\LoginController":
				return "login";
			case "Controllers\\SelectProjectController":
				return "select";
			case "Controllers\\UploadController":
				return "upload";
			case "Controllers\\RunScriptsController":
				return "run";
			case "Controllers\\ViewResultsController":
				return "view";
		}
	}

	public function getController($step) {
			switch($step) {
			case "test":
				return new \Controllers\TestController($this);
			case "login":
				return new \Controllers\LoginController($this);
			case "select":
				return new \Controllers\SelectProjectController($this);
			case "upload":
				return new \Controllers\UploadController($this);
			case "run":
				return new \Controllers\RunScriptsController($this);
			case "view":
				return new \Controllers\ViewResultsController($this);
			default:
				return new \Controllers\LoginController($this);
			}
	}

	public function getNewProject() {
		return new QIIMEProject($this->database, $this, $this->operatingSystem);
	}
	public function findProject($username, $projectId) {
		$projectName = $this->database->getProjectName($username, $projectId);
		if ($projectName == "ERROR") {
			return NULL;
		}
		$project = $this->getNewProject();
		$project->setName($projectName);
		$project->setOwner($username);
		$project->setId($projectId);
		return $project;	
	}
	public function getAllProjects($username) {
		$projectArrays = $this->database->getAllProjects($username);
		$projects = array();
		foreach ($projectArrays as $projectArray) {
			$project = $this->getNewProject();
			$project->setName($projectArray["name"]);
			$project->setOwner($projectArray["owner"]);
			$project->setId($projectArray["id"]);
			$projects[] = $project;	
		}
		return $projects;
	}

	public function getEnvironmentSource() {
		return "/macqiime/configs/bash_profile.txt";
	}
}
