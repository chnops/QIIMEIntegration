<?php

namespace Controllers;

class RunScriptsController extends Controller {

	protected $subTitle = "Run Scripts";

	public function retrievePastResults() {
		return "You have not run any scripts yet.";
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
		$instructions = "<p>From here, you can run any of the scripts that make up your workflow.  They are listed below in the order they are likely to be run,
			although it is possible that you will run them in a totally different order (see the help bar on the right for rules and requirements for specific scripts)</p>
			<hr class=\"small\"/>\n";

		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();	
		$instructions .= $project->renderOverview();
		return $instructions;
	}

	public function getForm() {
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		$form = "";
		foreach ($project->getScripts() as $script) {
			$form .= "<div class=\"script_form\">{$script->renderAsForm()}</div>";
			$form .= "<hr class=\"small\">\n";
		}
		return $form;
	}
}
