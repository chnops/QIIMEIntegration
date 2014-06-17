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

		echo "<p>Testing some UI widgets</p>";
		$script = "<script type=\"text/javascript\">$(function() { $('.accordion').accordion({collapsible: true}); });</script>";
		echo $script;
		echo htmlentities($script);

		$html = "<div class=\"accordion\"><h3>Opt 1</h3><div>Content 1</div><h3>Opt 2</h3><div>Content 3</div><h3>Opt 3</h3><div>Content 2</div></div>";
		echo $html;
		echo htmlentities($html);

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
