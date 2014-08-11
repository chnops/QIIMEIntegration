<?php

namespace Controllers;

class IndexController extends Controller {

	protected $subController = NULL;
	public function setSubController($subController) {
		$this->subController = $subController;
	}
	public function getSubController() {
		return $this->subController;
	}

	public function parseSession() {
		return;
	}

	public function parseInput() {
		if (isset($_REQUEST['step'])) {
			$this->setSubController($this->workflow->getController($_REQUEST['step']));
		}
		else {
			$this->setSubController(new LoginController($this->getWorkflow()));
		}
	}

	public function retrievePastResults() { return ""; }
	public function renderInstructions() { return ""; }
	public function renderForm() { return ""; }
	public function renderHelp() { return ""; }
	public function getSubTitle() { return ""; }
	public function renderSpecificStyle() { return ""; }
	public function renderSpecificScript() { return ""; }
	public function getScriptLibraries() { return array(); }

	public function renderOutput() {
		if ($this->subController) {
			$this->subController->run();
		}
		else {
			error_log("Attempted to render output before setting subcontroller");
		}
	}
}
