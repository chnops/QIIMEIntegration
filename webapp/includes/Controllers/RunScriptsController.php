<?php

namespace Controllers;

class RunScriptsController extends Controller {

	protected $subTitle = "Run Scripts";

	public function retrievePastResults() {
		return "You have not run any scripts yet.";
		//TODO $project->getScripts();
		// freach (script)
		// echo <div class=\"hideable\" id=\"past_results_{$script->getHtmlId()}\">$script->renderAsForm()</div>
		// render past results
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->disabled = " disabled";
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "In order to run scripts, you must be logged in and have a project selected.";
			return;
		}
	}

	public function getInstructions() {
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();	
		
		$scripts = $this->project->getScripts();
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
		$defaultScript = $scripts[0];
		$form = "";
		foreach ($scripts as $script) {
			$form .= "<div class=\"hideable script_form\" id=\"form_{$script->getHtmlId()}\">{$script->renderAsForm()}</div>\n";
		}
		$form .= "<script type=\"text/javascript\">window.onload=function(){hideableFields=['form', 'help', 'past_results']};</script>\n";
		return $form;
	}
}
