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
	public function renderInstructions() {
		ob_start();

		echo "<p>Testing table row selectors</p>";

		echo "<style>
			div.form table{border-collapse:collapse;margin:0px;width:100%}
			div.form td{padding:.5em;white-space:nowrap}
			div.form tr:nth-child(4n+1){background-color:#FFFFE0}
			div.form tr:nth-child(4n+2){background-color:#FFFFE0}
			</style>";
		echo "<div class=\"form\" style=\"width:100%\"><table>";

		for ($i = 0 ; $i < 100; $i++) {
			$display = $i + 1;
			echo "<tr><td>{$display}</td></tr>";
		}

		echo "</table></div>";


		return ob_get_clean();
	}
	public function renderForm() {
		return "";
	}
	public function renderHelp() {
		return "";
	}
	public function getSubTitle() {
		return "Test";
	}
	public function retrievePastResults() {
		return "";
	}
	public function renderSpecificStyle() {
		return "";
	}
	public function renderSpecificScript() {
		return "";
	}
	public function getScriptLibraries() {
		return array();
	}
}
