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

		$trigger1 = new \Models\Scripts\Parameters\TrueFalseParameter("--trigger1");

		$param1 = new \Models\Scripts\Parameters\TextArgumentParameter("--param1", "val1", "/.*/");
		$param2 = new \Models\Scripts\Parameters\TextArgumentParameter("--param2", "val2", "/.*/");
		$dependents = $param1->linkTo($param2);
		$dependents->requireIf($trigger1);
		
		echo "<form id=\"form_test_form\">";
		echo $trigger1->renderForForm($disabled = false);
		echo $dependents->renderForForm($disabled = false);
		echo "</form>";

		echo "<script type=\"text/javascript\" src=\"parameter_relationships.js\"></script>";
		echo "<script type=\"text/javascript\">var test_form = $('form#form_test_form');";
		echo $trigger1->renderFormScript($formJsVar = "test_form", $disabled = false);
		echo $dependents->renderFormScript($formJsVar = "test_form", $disabled = false);
		echo "</script>";

		return ob_get_clean();
	}
}
