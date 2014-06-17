<?php

namespace Controllers;

abstract class Controller {

	protected $workflow = NULL;

	protected $title = "QIIME";
	protected $step;

	protected $username = "";
	protected $project = NULL;
	protected $disabled = "";

	protected $isResultError = false;
	protected $result = "";
	private $pastResults = "";

	public function __construct(\Models\WorkflowI $workflow) {
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

	public abstract function retrievePastResults();

	public function isResultError() {
		return $this->isResultError;
	}
	public function getResult() {
		return $this->result;
	}
	public function renderSessionData() {
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
	public function renderPastResults() {
		if (!$this->pastResults) {
			$this->retrievePastResults();
		}
		return $this->pastResults;
	}
	public abstract function renderInstructions();
	public abstract function renderForm();
	public abstract function renderHelp();
	public abstract function getSubTitle();
	public function getWorkflow() {
		return $this->workflow;
	}
	public function renderOutput() {
		include 'views/template.php';
	}
	public function getContent() {
		ob_start();
		include 'views/content.php';
		return ob_get_clean();
	}
	public function run() {
		$this->parseSession();
		$this->parseInput();
		$this->renderOutput();
	}
}
