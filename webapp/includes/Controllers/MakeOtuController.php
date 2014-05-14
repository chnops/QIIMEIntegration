<?php

namespace Controllers;

class MakeOtuController extends Controller {

	protected $subTitle = "Make OTU Table";

	public function parseSession() {
		if (!isset($_SESSION['username'])) {
			$this->content .= "You cannot work on a project if you are not logged in!<br/>";
		}
		if (!isset($_SESSION['project'])) {
			$this->content .= "You cannot work on a project if you haven't selected one!<br/>";
		}
	}

	public function parseInput() {

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
