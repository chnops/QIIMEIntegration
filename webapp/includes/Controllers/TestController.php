<?php

namespace Controllers;

class TestController extends Controller {
	public function parseSession() {
		parent::parseSession();
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

		echo "<p>Testing past run display</p>";

		$helper = \Utils\Helper::getHelper();
		$pastRuns = $this->project->getPastScriptRuns();
//		$pastRuns = $helper->categorizeArray($pastRuns, "name");

		echo "<style>
			.accordion h4,.accordion div{margin-bottom:0em;margin-top:0em;padding:.25em}
			</style>";

		echo "<div class=\"accordion\" style=\"display:inline-block\">";
		foreach ($pastRuns as $run) {
			echo "<h4 onclick=\"hideMe($(this).next())\">Run id: {$run['id']}</h4>";
			echo "<div><strong>User input:</strong> {$run['input']}";
			if (!empty($run['file_names'])) {
				echo "<br/><strong>Generated files</strong><ul>";
				foreach($run['file_names'] as $file) {
					echo "<li>{$file}</li>";
				}
				echo "</ul>";
			}
			echo "</div>";
		}
		echo "</div>";

		echo "<script type=\"text/javascript\">$(function() { $('.accordion').width($('.accordion').width()) })</script>";
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
