<?php

namespace Controllers;
use \Models\Scripts\Parameters\EitherOrParameter;
use \Models\Scripts\Parameters\TrueFalseParameter;

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
		return "
			table.either_or{border:1px solid #999966;display:inline-block;padding:.25em}
			table.either_or td{padding:.25em;text-align:center}
			table.either_or tbody tr:first-child td {border-bottom:1px solid #999966}
			table.either_or td:not(:first-child) {border-left:1px solid #999966}
			";
	}
	public function renderSpecificScript() {
		return "";
	}
	public function getScriptLibraries() {
		return array('parameter_relationships.js');
	}
}
