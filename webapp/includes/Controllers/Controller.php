<?php

namespace Controllers;

abstract class Controller {

	protected $workflow = NULL;
	protected $helper = NULL;

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
		$this->helper = \Utils\Helper::getHelper();
	}
	public function run() {
		$this->parseSession();
		$this->parseInput();
		$this->renderOutput();
	}

	public abstract function parseInput(); 
	public abstract function retrievePastResults();

	public abstract function getSubTitle();
	public abstract function renderInstructions();
	public abstract function renderForm();
	public abstract function renderHelp();

	public abstract function renderSpecificStyle();
	public abstract function renderSpecificScript();
	public abstract function getScriptLibraries();

	public function isResultError() {
		return $this->isResultError;
	}
	public function setIsResultError($isResultError) {
		$this->isResultError = $isResultError;
	}
	public function getResult() {
		return $this->result;
	}
	public function setResult($result) {
		$this->result = $result;
	}
	public function renderPastResults() {
		if (!$this->pastResults) {
			$this->pastResults = $this->retrievePastResults();
		}
		return $this->pastResults;
	}
	public function getWorkflow() {
		return $this->workflow;
	}
	public function getExtraHtml($marker) {
		return "";
	}

	public function getUsername() {
		return $this->username;
	}
	public function setUsername($username) {
		$this->username = $username;
	}

	public function getProject() {
		return $this->project;
	}
	public function setProject($project) {
		$this->project = $project;
	}

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
	public function renderSessionData() {
		if (!$this->username) {
			return "You are not logged in.";
		}
		$output =  "You are currently logged in as <strong>" . $this->helper->htmlentities($this->username) . "</strong>";

		if ($this->project) {
			$output .= ", and you have selected the project <strong>" . $this->helper->htmlentities($this->project->getName()) . ".</strong>";
		}
		else {
			$output .= ", but <strong>you have not selected a project.</strong>";
		}
		return $output;
	}

	public function renderOutput() {
		include 'views/template.php';
	}
	public function getContent() {
		$content =  "<div id=\"session_data\">{$this->renderSessionData()}</div>\n";
		$content .=  "<h2>{$this->getSubtitle()}</h2>\n";

		$result = $this->getResult();
		if ($result) {
			$class = $this->isResultError() ? " class=\"error\"" : "";
			$content .=  "<div id=\"result\"{$class}>{$result}</div><br/>\n";
		}

		$instructions = $this->renderInstructions();
		if ($instructions) {
			$content .=  "<div id=\"instructions\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe($(this).parent().next());\">hide</a>):</em><div>" .
		   		$instructions . "</div></div>\n";
		}

		$pastResults = $this->renderPastResults();
		if ($pastResults) {
			$content .=  "<div id=\"past_results\"><em>Past results (<a onclick=\"hideMe($(this).parent().next())\">hide</a>)</em><div>{$pastResults}</div></div><br/>\n";
		}

		$form = $this->renderForm();
		if ($form) {
			$content .=  "<div class=\"form\">{$form}</div>\n";
		}
		return $content;
	}
}
