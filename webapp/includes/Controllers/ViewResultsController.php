<?php

namespace Controllers;

class ViewResultsController extends Controller {

	protected $subTitle = "View Results";

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "In order to upload files, you must be logged in and have a project selected.";
		}
		if (!isset($_POST['page'])) {
			return;
		}
	}

	public function getInstructions() {
		return "<p>Here is the moment you've been waiting for... your results!</p>";
	}
	public function getForm() {
		return "<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
			<input type=\"hidden\" name=\"step\" value=\"{$step}\"/>
			<button type=\"submit\">Perform</button>
			</form>";
	}
}
