<?php

namespace Controllers;

class ViewResultsController extends Controller {

	protected $subTitle = "View Results";

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "You have not selected a project, therefore there are no results to view.";
		}
	}

	public function getInstructions() {
		$this->help = "There isn't much to do here, but you can look at all the results you've generated so far.";
		return "<p>Here is the moment you've been waiting for... your results!</p>";
	}
	public function getForm() {
		if (!$this->project) {
			return "";
		}

		$project = $this->project;
		ob_start();
		include 'views/projectView.php';
		return ob_get_clean();
	}
}
