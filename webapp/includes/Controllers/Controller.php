<?php

namespace Controllers;

abstract class Controller {

	protected $database = NULL;
	protected $workflow = NULL;

	protected $title = "QIIME";
	protected $subTitle = "";
	protected $help = "";
	protected $step;
	private $content = "";

	protected $username = "";
	protected $project = NULL;
	protected $disabled = "";

	protected $isResultError = false;
	protected $hasResult = false;
	protected $result = "Result not yet implemented!";
	private $pastResults = "";

	public function __construct(\Database\DatabaseI $database, \Models\WorkflowI $workflow) {
		$this->database = $database;
		$this->workflow = $workflow;
		$this->step = $this->workflow->getCurrentStep($this);
	}

	public abstract function parseInput(); 

	public function parseSession() {
		if (!isset($_SESSION['username'])) {
			return;
		}
		$this->username = $_SESSION['username'];
		if (!isset($_SESSION['project_id'])) {
			return;
		}
		$this->project = $this->workflow->findProject($this->username, $_SESSION['project_id']);
	}

	public function retrievePastResults() {
		return "";
	}

	public function isResultError() {
		return $this->isResultError;
	}
	public function hasResult() {
		return $this->hasResult;
	}
	public function getResult() {
		return $this->result;
	}
	public function getSessionData() {
		if (!$this->username) {
			return "You are not logged on.";
		}
		$output =  "You are currently logged in as <strong>" . htmlentities($this->username) . "</strong>";

		if ($this->project) {
			$output .= ", and you have selected the project <strong>" . htmlentities($this->project->getName()) . "</strong>";
		}
		else {
			$output .= ", but <strong>you have not selected a project.</strong>";
		}
		return $output;
	}
	public function hasPastResults() {
		if (!$this->project) {
			return false;
		}
		$this->pastResults = $this->retrievePastResults();
		if ($this->pastResults) {
			return true;
		}
		return false;
	}
	public function renderPastResults() {
		return $this->pastResults;
	}
	public function getInstructions() {
		return "<div class=\"error\">Instructions not yet implemented!</div>";
	}
	public function getForm() {
		return "<div class=\"error\">Form not yet implemented!</div>";
	}
	public function getWorkflow() {
		return $this->workflow;
	}
	public function getSubTitle() {
		return $this->subTitle;
	}
	public function renderOutput() {
		ob_start();
		include 'views/content.php';
		$this->content = ob_get_clean();
		include 'views/template.php';
	}
	public function run() {
		$this->parseSession();
		$this->parseInput();
		$this->renderOutput();
	}
}
