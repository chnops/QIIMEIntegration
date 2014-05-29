<?php

namespace Controllers;

class TestController extends Controller {
	public function parseSession() {
		return;
	}
	public function parseInput() {
		return;
	}
	public function getInstructions() {
		ob_start();

		echo "<p>Testing form submission with escaped quotes</p>";
		if (!empty($_POST)) {
			echo "<pre>";
			print_r($_POST);
			echo "</pre><br/><br/>";
		}

		echo "<form method=\"POST\">
			<input type=\"hidden\" name=\"step\" value=\"test\"/>
			<input type=\"text\" value='Aaron' name=\"input\"/>
			<button type=\"submit\">Submit</button>
		</form>";

		return ob_get_clean();
	}
}
