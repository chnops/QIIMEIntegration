<?php

namespace Controllers;

class MakePhylogenyController extends Controller {

	protected $subTitle = "Perform Phylogeny Analysis";

	public function parseSession() {
		if (!isset($_SESSION['username'])) {
			$this->content .= "You cannot work on a project if you are not logged in!<br/>";
		}
		if (!isset($_SESSION['project'])) {
			$this->content .= "You cannot work on a project if you haven't selected one!<br/>";
		}
	}

	public function parseInput() {
		if (!isset($_POST['page'])) {
			return;
		}
		if ($_POST['perform_phylogeny'] == "no") {
			echo "ok<br/>";
		}
		else {
			echo "Sorry, phylogeny not implemented yet.<br/>";
		}
	}

	public function getInstructions() {
		return "
			<p>QIIME doesn't offer much in the way of phylogeny analysis, but they can create trees.</p>";
	}
	public function getForm() {
		return "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\"/>
				<input type=\"radio\" name=\"perform_phylogeny\" value=\"yes\" checked>Yes</input>
				<input type=\"radio\" name=\"perform_phylogeny\" value=\"no\">No</input>
				<button type=\"submit\">Perform</button>
			</form>";
	}
}
