<?php

namespace Controllers;

class TestController extends Controller {
	public function parseSession() {
		return;
	}
	public function parseInput() {
		if (isset($_GET['clean'])) {
			$this->clean();
		}
		return;
	}
	private function clean() {
		return;
	}
	public function getInstructions() {
		ob_start();

		echo "<p>Testing regular expression for TextArgumentParameter<p>";

		if (!empty($_POST)) {
			echo "Results: ";
			echo "preg_match({$_POST['regex']}, {$_POST['input']}): ";
			echo preg_match($_POST['regex'], $_POST['input']);
			echo "!<hr/>";
		}

		echo "<form method=\"POST\">";
		echo "<input type=\"hidden\" name=\"step\" value=\"test\">";
		echo "<label for=\"regex\">Regex: <input name=\"regex\" value=\"{$_POST['regex']}\"></label><br/>";
		echo "<label for=\"input\">Input: <input name=\"input\" value=\"{$_POST['input']}\"></label><br/>";
		echo "<button type=\"submit\">Submit</button>";
		echo "</form>";

		return ob_get_clean();
	}
}
