<?php

namespace Controllers;

class RunScriptsController extends Controller {

	public function getSubTitle() {
		return "Run Scripts";
	}
	private $scriptId = "";

	public function retrievePastResults() {
		if (!$this->project) {
			return "";
		}
		$pastScriptRuns = $this->project->getPastScriptRuns();
		if(empty($pastScriptRuns)) {
			return "";
		}

		$helper = \Utils\Helper::getHelper();
		$pastScriptRunsFormatted = $helper->categorizeArray($pastScriptRuns, 'name');

		$output = "";
		foreach ($this->project->getScripts() as $scriptName => $scriptObject) {
			$output .= "<div class=\"hideable\" id=\"past_results_{$scriptName}\"><ul>";
			if (!isset($pastScriptRunsFormatted[$scriptName])) {
				$output .= "This script has not been run yet.";
				$output .= "</ul></div>\n";
				continue;
			}
			foreach ($pastScriptRunsFormatted[$scriptName] as $run) {
				$output .= "<li><strong>Run {$run['id']}</strong><br/>";
				$output .= "<strong>Script name:</strong> {$run['name']}<br/>";
				$output .= "<strong>User input:</strong> " . htmlentities($run['input']) . "<br/>";

				$output .= "<strong>Generated files:</strong><ul>";
				foreach ($run['file_names'] as $fileName) {
					$output .= "<li>" . htmlentities($fileName) . "</li>";
				}
				$output .= "</ul>";

				$output .= "<strong>Console output:</strong> {$run['output']}<br/>";
				$output .= "<strong>Script version:</strong> {$run['version']}<br/>";
				$output .= "</li>\n";
			}
			$output .= "</ul></div>\n";
		}

		if ($output) {
			return "<h4>Past runs:</h4>\n" . $output;
		}
		return "";
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
		$this->scriptId = $_POST['script'];

		try {
			$this->result = $this->project->runScript($_POST);
		}
		catch (\Exception $ex) {
			$this->isResultError = true;
			$this->result = $ex->getMessage();
		}
	}

	public function renderInstructions() {
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();	

		$instructions = "<p>From here, you can run any of the scripts that make up your workflow.  They are listed below in the order they are likely to be run,
			although it is possible that you will run them in a totally different order (see the help bar on the right for rules and requirements for specific scripts)</p>\n";

		$instructions .= $project->renderOverview();
		return $instructions;
	}

	public function renderForm() {
		if ($this->project) {
			$project = $this->project;
			$shouldBeDisabled = false;
		}
		else {
			$project = $this->workflow->getNewProject();
			$shouldBeDisabled = true;
		}
		$scripts = $project->getScripts();
		$form = "";
		foreach ($scripts as $script) {
			$form .= "<div class=\"hideable script_form\" id=\"form_{$script->getHtmlId()}\">{$script->renderAsForm($shouldBeDisabled)}</div>\n";
		}
		
		return "<span id=\"parameter_help\" class=\"draggable hideme\">Parameter help</span>" . $form;
	}

	public function renderHelp() {
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();	
		$help = "";
		$scripts = $project->getScripts();
		if ($scripts) {
			foreach ($scripts as $script) {
				$help .= "<div class=\"hideable\" id=\"help_{$script->getHtmlId()}\">\n";
				$help .= $script->renderHelp();
				$help .= "</div>\n";
			}
		}
		return $help;
	}
	public function renderSpecificStyle() {
		return "div.script_form input[type=\"text\"],div.script_form input[type=\"file\"],select{display:block}
			div#project_overview{border-width:1px;padding:.5em;overflow:auto}
			div#project_overview div{margin:1em 0em 1.5em 0em;white-space:nowrap}
			div#project_overview span{margin:0em 1em}
			select[size]{padding:.5em .5em 1.5em .5em}
			table.either_or{border:1px solid #999966;display:inline-block;padding:.25em}
			table.either_or td{padding:.25em;text-align:center}
			table.either_or tbody tr:first-child td {border-bottom:1px solid #999966}
			table.either_or td:not(:first-child) {border-left:1px solid #999966}";
	}
	public function renderSpecificScript() {
		$onLoadJavascript = "hideableFields=['form', 'help', 'past_results'];";
		if ($this->scriptId) {
			$onLoadJavascript .= "displayHideables('{$this->scriptId}');";
		}
		return "window.onload=function(){{$onLoadJavascript}};";
	}
	public function getScriptLibraries() {
		return array("parameter_relationships.js");
	}
}
