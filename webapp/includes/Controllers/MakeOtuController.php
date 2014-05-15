<?php

namespace Controllers;

class MakeOtuController extends Controller {

	protected $subTitle = "Make OTU Table";

	public function retrievePastResults() {
		return "Not yet implemented";
	}

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
			<p>The OTU table is pretty much the end result of QIIME's analysis pipeline.  After that, you can import it into R. 
			Assigning to real-world taxonomies is optional, and time-intensive.</p>";
	}

	public function getForm() {
		return "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\"/>
				<button type=\"submit\">Perform</button>
				</form>";
	}
}
