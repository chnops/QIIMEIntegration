<?php

namespace Controllers;

class IndexController extends Controller {

	protected $subController = NULL;

	public function parseSession() {
		return;
	}

	public function parseInput() {
		if (isset($_REQUEST['step'])) {
			$this->subController = $this->workflow->getController($_REQUEST['step']);
		}
		else {
			$this->subController = new LoginController($this->workflow);
		}
	}

	public function retrievePastResults() { return ""; }
	public function renderInstructions() { return ""; }
	public function renderForm() { return ""; }
	public function renderHelp() { return ""; }
	public function getSubTitle() { return ""; }

	public function renderOutput() {
		if ($this->subController) {
			$this->subController->run();
		}
		else {
			error_log("Attempted to render output before setting subcontroller");
		}
	}
}
