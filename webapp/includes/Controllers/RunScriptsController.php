<?php

namespace Controllers;

class RunScriptsController extends Controller {
	private $scriptId = "";

	public function getSubTitle() {
		return "Run Scripts";
	}
	
	public function retrievePastResults() {
		if (!$this->project) {
			return "";
		}
		$pastScriptRuns = $this->project->getPastScriptRuns();
		if(empty($pastScriptRuns)) {
			return "";
		}

		$pastScriptRunsFormatted = $this->helper->categorizeArray($pastScriptRuns, 'name');

		$output = "";
		foreach ($this->project->getScripts() as $scriptName => $scriptObject) {
			$output .= "<div class=\"hideable accordion\" id=\"past_results_{$scriptName}\">";
			if (!isset($pastScriptRunsFormatted[$scriptName])) {
				$output .= "This script has not been run yet.";
				$output .= "</div>\n";
				continue;
			}
			foreach ($pastScriptRunsFormatted[$scriptName] as $run) {
				$status = ($run['is_finished']) ? "ready" : "still running";
				$output .= "<h4 onclick=\"hideMe($(this).next())\">Run {$run['id']} (<em>{$status}</em>)</h4>";
				$output .= "<div><strong>User input:</strong> " . $this->helper->htmlentities($run['input']);
				if (!empty($run['file_names'])) {
					$output .= "<br/><strong>Generated files</strong><ul>";
					foreach($run['file_names'] as $fileName) {
						$output .= "<li>" . $this->helper->htmlentities($fileName) . "</li>";
					}
					$output .= "</ul>";
				}
				$output .= "</div>";
			}
			$output .= "</div>\n";
		}

		if ($output) {
			return $output;
		}
		return "";
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->result = "In order to run scripts, you must be logged in and have a project selected.";
			return;
		}
		if (!isset($_POST['step'])) {
			return;
		}

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
		$form = "";
		$scripts = $project->getScripts();
		foreach ($scripts as $script) {
			$form .= "<div class=\"hideable script_form\" id=\"form_{$script->getHtmlId()}\">{$script->renderAsForm($shouldBeDisabled)}</div>\n";
		}
		
		return $form;
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
			.accordion h4,.accordion div{margin-bottom:0em;margin-top:0em;padding:.25em;white-space:nowrap}
			select[size]{padding:.5em .5em 1.5em .5em}
			table.either_or{border:1px solid #999966;display:inline-block;padding:.25em}
			table.either_or td{padding:.25em;text-align:center}
			table.either_or tbody tr:first-child td {border-bottom:1px solid #999966}
			table.either_or td:not(:first-child) {border-left:1px solid #999966}
			#per_param_help{white-space:pre-line;overflow:auto;border-top-width:1px}";
	}
	public function renderSpecificScript() {
		$displayHideables= "hideableFields=['form', 'help', 'past_results'];";
		if ($this->scriptId) {
			$displayHideables.= "displayHideables('{$this->scriptId}');";
		}
		$perParamHelp = "$('.param_help').click(function() {
			$('#per_param_help').html('-loading-').load('public/help/' + $(this).attr('id') + '.txt') });";
		$hideAccordionChildren = "$('.accordion div').css('display', 'none')";
		return "$(function() {{$displayHideables};{$perParamHelp};{$hideAccordionChildren}});";
	}
	public function getScriptLibraries() {
		return array("parameter_relationships.js");
	}

	public function getExtraHtml($marker) {
		if ($marker == 'post_help') {
			return "<p>Parameter-specific help (<a onclick=\"hideMe($(this).parent().next());\">hide</a>)</p>
				<div id=\"per_param_help\"></div>";
		}
		return "";
	}
}
