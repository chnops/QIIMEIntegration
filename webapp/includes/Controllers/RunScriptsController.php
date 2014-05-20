<?php

namespace Controllers;

class RunScriptsController extends Controller {

	protected $subTitle = "Run Scripts";
	private $script = "";

	public function retrievePastResults() {
		if (!$this->project) {
			return "";
		}
		return "<h4>Past runs:</h4>\n" . $this->project->getPastScriptRuns();
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->disabled = " disabled";
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "In order to run scripts, you must be logged in and have a project selected.";
			return;
		}
		if (!isset($_POST['step'])) {
			return;
		}
		$_GET['step'] = $_POST['step'];
		unset($_POST['step']);
		$this->hasResult = true;
		$this->script = $_POST['script'];

		try {
			$this->result = $this->project->processScriptInput($_POST);
		}
		catch (\Exception $ex) {
			$this->isResultError = true;
			$this->result = htmlentities($ex->getMessage());
		}
	}

	public function getInstructions() {
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();	
		
		$scripts = $project->getScripts();
		if ($scripts) {
			$this->help .= "\n";
			foreach ($scripts as $script) {
				$this->help .= "<div class=\"hideable\" id=\"help_{$script->getHtmlId()}\">\n";
				$this->help .= $script->renderHelp();
				$this->help .= "</div>\n";
			}
		}

		$instructions = "<p>From here, you can run any of the scripts that make up your workflow.  They are listed below in the order they are likely to be run,
			although it is possible that you will run them in a totally different order (see the help bar on the right for rules and requirements for specific scripts)</p>
			<hr class=\"small\"/>\n";

		$instructions .= $project->renderOverview();
		return $instructions;
	}

	public function getForm() {
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		$scripts = $project->getScripts();
		$form = "";
		foreach ($scripts as $script) {
			$form .= "<div class=\"hideable script_form\" id=\"form_{$script->getHtmlId()}\">{$script->renderAsForm()}</div>\n";
		}
		$onLoadJavascript = "hideableFields=['form', 'help', 'past_results'];";
		if ($this->script) {
			$onLoadJavascript .= "displayHideables('{$this->script}');";
		}
		$form .= "<script type=\"text/javascript\">window.onload=function(){{$onLoadJavascript}};</script>\n";
		return $form;
	}
}
