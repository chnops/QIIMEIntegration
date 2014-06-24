<?php

namespace Controllers;
use \Models\Scripts\Parameters\EitherOrParameter;
use \Models\Scripts\Parameters\TrueFalseParameter;

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

		echo "<p>Testing nested EitherOr parameters</p>";

		if (!empty($_POST)) {
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			echo "<hr/>";
		}
		$cBox1 = new TrueFalseParameter("--left_left");
		$cBox2 = new TrueFalseParameter("--left_right");
		$cBox3 = new TrueFalseParameter("--right_left");
		$cBox4 = new TrueFalseParameter("--right_right");
		$eo1 = $cBox1->linkTo($cBox2);
		$eo2 = $cBox3->linkTo($cBox4);
		$parentParam = $eo1->linkTo($eo2);

		echo "<form method=\"POST\">";
		echo $parentParam->renderForForm($disabled = false,
			new \Models\Scripts\QIIME\ValidateMappingFile($this->workflow->getNewProject()));
		echo "<script type=\"text/javascript\">var js_ = $('form');" .
			$parentParam->renderFormScript('js_', $disabled = false) . "</script>";
		echo "<input type=\"hidden\" name=\"step\" value=\"test\">
			<button type=\"submit\">Submit</button></form>";

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
