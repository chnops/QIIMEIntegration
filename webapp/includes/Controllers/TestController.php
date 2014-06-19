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

		echo "<p>Testing radio buttons w/jQuery<p>";
		$script = "<script type=\"text/javascript\">$(function() {
			$('[name=\"rad\"]').change(function() {
				var val = $(this).val();
				$('#click_result').html(\"we are here, we are here!! \" + val);
			});
		})</script>";
		echo $script;
		echo "<pre>";
		echo htmlentities($script);
		echo "</pre>";

		$html = "<form action=\"#\">
			<label for=\"rad\">Option 1<input type=\"radio\" name=\"rad\" value=\"1\"></label>
			<label for=\"rad\">Option 2<input type=\"radio\" name=\"rad\" value=\"2\"></label>
			<label for=\"rad\">Option 3<input type=\"radio\" name=\"rad\" value=\"3\"></label>
			</form>
			<div id=\"click_result\" style=\"border-width:1px\"><div>";
		echo $html;

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
