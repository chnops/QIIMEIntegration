<?php

namespace Controllers;

class MakePhylogenyController extends Controller {

	protected $subTitle = "Perform Phylogeny Analysis";

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "In order to upload files, you must be logged in and have a project selected.";
			return;
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
