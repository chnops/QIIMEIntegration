<?php

namespace Controllers;

class ViewResultsController extends Controller {

	protected $subTitle = "View Results";

	public function parseSession() {
		if (!isset($_SESSION['username'])) {
			$this->content .= "You are not corrently logged in.<br/>";
		}
		if (!isset($_SESSION['project'])) {
			$this->content .= "You have not currently selected a project.<br/>";
		}
	}

	public function parseInput() {
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
