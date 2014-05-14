<?php 

namespace Models;

class QIIMEWorkflow implements WorkflowI {

	private $database = NULL;
	private $operatingSystem = NULL;

	private $steps = array(
		"login" => "Login",	
		"select" => "Select/create project",
		"upload" => "Upload files",
		"make_otu" => "Create OTU table",
		"make_phylogeny" => "Perform phylogeny analysis [optional]",
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
			case "Controllers\\MakeOtuController":
				return "make_otu";
			case "Controllers\\MakePhylogenyController":
				return "make_phylogeny";
			case "Controllers\\ViewResultsController":
				return "view";
		}
	}

	public function getController($step) {
			switch($step) {
			case "test":
				return new \Controllers\TestController($this->database, $this);
				break;
			case "login":
				return new \Controllers\LoginController($this->database, $this);
				break;
			case "select":
				return new \Controllers\SelectProjectController($this->database, $this);
				break;
			case "upload":
				return new \Controllers\UploadController($this->database, $this);
				break;
			case "make_otu":
				return new \Controllers\MakeOtuController($this->database, $this);
				break;
			case "make_phylogeny":
				return new \Controllers\MakePhylogenyController($this->database, $this);
				break;
			case "view":
				return new \Controllers\ViewResultsController($this->database, $this);
				break;
			default:
				return new \Controllers\LoginController($this->database, $this);
				break;
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
}
