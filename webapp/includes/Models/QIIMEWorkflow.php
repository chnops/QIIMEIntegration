<?php 

namespace Models;

class QIIMEWorkflow implements WorkflowI {

	private $operatingSystem = NULL;
	private $steps = array(
		"login" => "Login",	
		"select" => "Select/create project",
		"upload" => "Upload files",
		"make_otu" => "Create OTU table",
		"make_phylogeny" => "Perform phylogeny analysis [optional]",
		"view" => "View results");

	public function __construct(OperatingSystemI $operatingSystem) {
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

	public function getController($step, \Database\DatabaseI $database) {
			switch($_REQUEST['step']) {
			case "test":
				return new \Controllers\TestController($database, $this);
				break;
			case "login":
				return new \Controllers\LoginController($database, $this);
				break;
			case "select":
				return new \Controllers\SelectProjectController($database, $this);
				break;
			case "upload":
				return new \Controllers\UploadController($database, $this);
				break;
			case "make_otu":
				return new \Controllers\MakeOtuController($database, $this);
				break;
			case "make_phylogeny":
				return new \Controllers\MakePhylogenyController($database, $this);
				break;
			case "view":
				return new \Controllers\ViewResultsController($database, $this);
				break;
			default:
				return new \Controllers\LoginController($database, $this);
				break;
			}
	}

	public function getSteps() {
		return $this->steps;
	}

	public function getDefaultProject(\Database\DatabaseI $database) {
		return new QIIMEProject($database, $this, $this->operatingSystem);
	}
}
