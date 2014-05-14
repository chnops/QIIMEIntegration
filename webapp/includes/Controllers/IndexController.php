<?php

namespace Controllers;

class IndexController extends Controller {

	protected $subController = NULL;

	public function parseSession() {
		return;
	}

	public function parseInput() {
		if (isset($_REQUEST['step'])) {
			$this->subController = $this->workflow->getController($_REQUEST['step'], $this->database);
		}
		else {
			$this->subController = new LoginController($this->database, $this->workflow);
		}
	}

	public function hasImmediateResult() {return false;}
	public function getImmediateResult() {return;}
	public function hasPastResults() {return false;}
	public function getPastResults() {return;}
	public function getInstructions() {return "";}
	public function getForm() {return "";}

	public function renderOutput() {
		if ($this->subController) {
			$this->subController->run();
		}
		else {
			error_log("Attempted to render output before setting subcontroller");
		}
	}
}
